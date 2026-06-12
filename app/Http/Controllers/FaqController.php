<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Services\FaqService;
use App\Services\SiteSettingService;
use Illuminate\View\View;

class FaqController extends Controller
{
    protected FaqService $faqService;

    protected SiteSettingService $siteSettingService;

    public function __construct(FaqService $faqService, SiteSettingService $siteSettingService)
    {
        $this->faqService = $faqService;
        $this->siteSettingService = $siteSettingService;
    }

    public function __invoke(): View
    {
        $faqs = $this->faqService->getAllActiveFaqs();
        $faqTitle = $this->siteSettingService->get(SiteSetting::KEY_FAQ_TITLE, __('Biežāk uzdotie jautājumi'));
        $faqSubtitle = $this->siteSettingService->get(
            SiteSetting::KEY_FAQ_SUBTITLE,
            __('Šeit ir atbildes uz biežāk uzdotajiem jautājumiem par mūsu brīvdienu dizaina mājām.')
        );

        return view('faq', compact('faqs', 'faqTitle', 'faqSubtitle'));
    }
}
