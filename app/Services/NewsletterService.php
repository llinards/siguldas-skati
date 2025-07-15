<?php

namespace App\Services;

use App\Models\Newsletter;
use Illuminate\Database\Eloquent\Collection;

class NewsletterService
{
    public function getAllSubscribers(): Collection
    {
        return Newsletter::all();
    }

    public function getSubscriberById(int $id): ?Newsletter
    {
        return Newsletter::find($id);
    }

    public function deleteSubscriberById(Newsletter $subscriber): bool
    {
        return $subscriber->delete();
    }

    public function deleteAllSubscribers(): void
    {
        $subscribers = Newsletter::all();
        foreach ($subscribers as $subscriber) {
            $subscriber->delete();
        }
    }

    public function store(array $subscriber)
    {
        return Newsletter::create($subscriber);
    }
}
