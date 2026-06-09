<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Services\GalleryServices;
use App\Services\HeaderMediaService;
use App\Services\ProductServices;
use App\Services\SiteSettingService;
use Illuminate\View\View;

class HomeController extends Controller
{
    protected ProductServices $productServices;

    protected GalleryServices $galleryService;

    protected HeaderMediaService $headerMediaService;

    protected SiteSettingService $siteSettingService;

    public function __construct(
        ProductServices $productServices,
        GalleryServices $galleryService,
        HeaderMediaService $headerMediaService,
        SiteSettingService $siteSettingService,
    ) {
        $this->productServices = $productServices;
        $this->galleryService = $galleryService;
        $this->headerMediaService = $headerMediaService;
        $this->siteSettingService = $siteSettingService;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        $products = $this->productServices->getAllActiveProducts();
        $galleries = $this->galleryService->getAllActiveGalleries();
        $headerMedia = $this->headerMediaService->getAllOrdered();
        $heroTitle = $this->siteSettingService->get(
            SiteSetting::KEY_HOME_HERO_TITLE,
            __('Modernas brīvdienu dizaina mājas tavai atpūtai!')
        );

        $aboutTitle = $this->siteSettingService->get(SiteSetting::KEY_ABOUT_TITLE, __('Par mums'));
        $aboutSubtitle = $this->siteSettingService->get(SiteSetting::KEY_ABOUT_SUBTITLE, __('Klusuma greznība Siguldas sirdī!'));
        $aboutHeading = $this->siteSettingService->get(SiteSetting::KEY_ABOUT_HEADING, __('Siguldas skati'));
        $aboutDescription = $this->siteSettingService->get(SiteSetting::KEY_ABOUT_DESCRIPTION);
        $aboutImage = $this->siteSettingService->get(SiteSetting::KEY_ABOUT_IMAGE);

        return view('home', compact(
            'products',
            'galleries',
            'headerMedia',
            'heroTitle',
            'aboutTitle',
            'aboutSubtitle',
            'aboutHeading',
            'aboutDescription',
            'aboutImage',
        ));
    }
}
