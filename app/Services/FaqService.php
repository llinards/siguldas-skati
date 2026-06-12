<?php

namespace App\Services;

use App\Models\Faq;
use Illuminate\Support\Collection;

class FaqService
{
    public function getAllFaqs(): Collection
    {
        return Faq::all();
    }

    public function getAllActiveFaqs(): Collection
    {
        return Faq::where('is_active', true)->get();
    }

    public function getFaqById(int $id): ?Faq
    {
        return Faq::find($id);
    }

    public function createFaq(array $faqData): Faq
    {
        return Faq::create($faqData);
    }

    public function updateFaq(Faq $faq, array $faqData): bool
    {
        return $faq->update($faqData);
    }

    public function deleteFaq(Faq $faq): bool
    {
        return $faq->delete();
    }

    public function toggleFaqStatus(int $faqId): bool
    {
        $faq = Faq::find($faqId);

        if (! $faq) {
            return false;
        }

        return $faq->update(['is_active' => ! $faq->is_active]);
    }

    public function updateFaqOrder(int $id, int $position): void
    {
        $faq = Faq::findOrFail($id);
        $faqs = Faq::orderBy('order')->get()->reject(fn ($f) => $f->id === $faq->id)->values();
        $faqs->splice($position, 0, [$faq]);

        foreach ($faqs as $index => $f) {
            if ($f->order !== $index) {
                $f->update(['order' => $index]);
            }
        }
    }
}
