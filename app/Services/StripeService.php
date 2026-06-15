<?php

namespace App\Services;

use App\Models\Booking;
use Stripe\Checkout\Session;
use Stripe\Event;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeService
{
    private ?StripeClient $client = null;

    public function __construct()
    {
        $secret = config('services.stripe.secret');

        if ($secret) {
            $this->client = new StripeClient((string) $secret);
        }
    }

    /**
     * Build Stripe line items from a booking's stored cost components.
     * One item per component (quantity 1) so the Stripe total equals grand_total.
     *
     * @return array<int, array{price_data: array{currency: string, unit_amount: int, product_data: array{name: string}}, quantity: int}>
     */
    public function buildLineItems(Booking $booking): array
    {
        $currency = $booking->currency;
        $items = [];

        $items[] = $this->lineItem($currency, __('Naktis').' ('.$booking->check_in->toDateString().' – '.$booking->check_out->toDateString().')', $booking->nights_total);

        if ($booking->cleaning_fee > 0) {
            $items[] = $this->lineItem($currency, __('Uzkopšana'), $booking->cleaning_fee);
        }

        foreach ($booking->addons as $addon) {
            $items[] = $this->lineItem(
                $currency,
                $addon->pivot->name,
                $addon->pivot->price * $addon->pivot->quantity,
            );
        }

        return $items;
    }

    public function createCheckoutSession(Booking $booking, string $successUrl, string $cancelUrl): Session
    {
        return $this->client->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => $this->buildLineItems($booking),
            'customer_email' => $booking->guest_email,
            'client_reference_id' => $booking->reference,
            'metadata' => ['booking_id' => (string) $booking->id],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'expires_at' => now()->addMinutes(30)->getTimestamp(),
        ], [
            'idempotency_key' => 'checkout_'.$booking->reference,
        ]);
    }

    public function constructWebhookEvent(string $payload, ?string $signature): Event
    {
        return Webhook::constructEvent($payload, (string) $signature, (string) config('services.stripe.webhook_secret'));
    }

    /**
     * @return array{price_data: array{currency: string, unit_amount: int, product_data: array{name: string}}, quantity: int}
     */
    private function lineItem(string $currency, string $name, int $amount): array
    {
        return [
            'price_data' => [
                'currency' => $currency,
                'unit_amount' => $amount,
                'product_data' => ['name' => $name],
            ],
            'quantity' => 1,
        ];
    }
}
