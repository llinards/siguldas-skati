<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        $checkIn = $this->faker->dateTimeBetween('now', '+3 months');
        $checkOut = (clone $checkIn)->modify('+3 days');

        return [
            'product_id' => Product::factory(),
            'reference' => 'SS-'.Str::upper(Str::random(5)),
            'check_in' => $checkIn->format('Y-m-d'),
            'check_out' => $checkOut->format('Y-m-d'),
            'adults' => 2,
            'children' => 0,
            'guest_name' => $this->faker->name(),
            'guest_email' => $this->faker->safeEmail(),
            'guest_phone' => $this->faker->phoneNumber(),
            'nights_total' => 54000,
            'grand_total' => 54000,
            'currency' => 'eur',
            'status' => BookingStatus::Confirmed,
            'management_token' => (string) Str::uuid(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => BookingStatus::Pending,
            'expires_at' => now()->addMinutes(30),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => BookingStatus::Pending,
            'expires_at' => now()->subMinute(),
        ]);
    }
}
