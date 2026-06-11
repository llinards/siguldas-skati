<x-admin.app-layout>
    <div class="hs-accordion-group space-y-4" data-hs-accordion-always-open="">
        <x-admin.collapsible-section :title="__('Galvenes virsraksts')">
            <livewire:admin.home-hero-settings />
        </x-admin.collapsible-section>
        <x-admin.collapsible-section :title="__('Par mums')">
            <livewire:admin.about-us-settings />
        </x-admin.collapsible-section>
        <x-admin.collapsible-section :title="__('Mājas')">
            <livewire:admin.product-section-settings />
        </x-admin.collapsible-section>
        <x-admin.collapsible-section :title="__('Galerija')">
            <livewire:admin.gallery-section-settings />
        </x-admin.collapsible-section>
        <x-admin.collapsible-section :title="__('Pieredze')">
            <livewire:admin.experience-section-settings />
        </x-admin.collapsible-section>
    </div>
</x-admin.app-layout>
