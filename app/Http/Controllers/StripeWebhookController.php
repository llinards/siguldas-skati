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

            $bookingId = $session->metadata->booking_id ?? null;
            $sessionId = $session->id ?? null;

            if ($bookingId === null && $sessionId === null) {
                return response('OK', 200);
            }

            $booking = Booking::query()
                ->where(function ($query) use ($bookingId, $sessionId) {
                    if ($bookingId !== null) {
                        $query->where('id', $bookingId);
                    }
                    if ($sessionId !== null) {
                        $query->orWhere('stripe_session_id', $sessionId);
                    }
                })
                ->first();

            if ($booking) {
                $paymentIntent = is_string($session->payment_intent ?? null)
                    ? $session->payment_intent
                    : ($session->payment_intent->id ?? null);
                $bookings->confirm($booking, $paymentIntent);
            }
        }

        if ($event->type === 'charge.refunded') {
            $charge = $event->data->object;

            $paymentIntentId = is_string($charge->payment_intent ?? null)
                ? $charge->payment_intent
                : ($charge->payment_intent->id ?? null);

            if ($paymentIntentId === null) {
                return response('OK', 200);
            }

            $booking = Booking::where('stripe_payment_intent_id', $paymentIntentId)->first();

            if ($booking) {
                $refundId = $charge->refunds->data[0]->id ?? null;
                $bookings->reconcileRefund($booking, (int) $charge->amount_refunded, $refundId);
            }
        }

        return response('OK', 200);
    }
}
