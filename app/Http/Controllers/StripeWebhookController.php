<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, StripeService $stripe, BookingService $bookings): Response
    {
        try {
            $event = $stripe->constructWebhookEvent($request->getContent(), $request->header('Stripe-Signature'));
        } catch (\Throwable $e) {
            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $booking = Booking::query()
                ->when($session->id ?? null, fn ($q) => $q->orWhere('stripe_session_id', $session->id))
                ->orWhere('id', $session->metadata->booking_id ?? null)
                ->first();

            if ($booking) {
                $paymentIntent = is_string($session->payment_intent ?? null)
                    ? $session->payment_intent
                    : ($session->payment_intent->id ?? null);
                $bookings->confirm($booking, $paymentIntent);
            }
        }

        return response('OK', 200);
    }
}
