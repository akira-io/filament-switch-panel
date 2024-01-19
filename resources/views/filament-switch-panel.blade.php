@php
   $urlScheme = (string) app()->environment('production') ? 'https://' : 'http://';
  
$getFirstDomainPath = function (\Filament\Panel $panel) use ($urlScheme): string {
    $firstDomain = collect($panel->getDomains())->first();
    return str($firstDomain)->prepend($urlScheme)->toString();
};

$getDefaultPath = function (\Filament\Panel $panel): string {
    return str($panel->getPath())->prepend('/')->toString();
};

$getPanelPath = function (\Filament\Panel $panel) use ($getFirstDomainPath, $getDefaultPath): string {
    if (filled($panel->getDomains())) {
        return $getFirstDomainPath($panel);
    }
    return $getDefaultPath($panel);
};
 
 $computeHref = fn (\Filament\Panel $panel): ?string => $canSwitchPanels && $panel->getId() !== $currentPanel->getId()
           ? $getPanelPath($panel)
           : null;
@endphp

@if ($isSimple)
   <x-filament-switch-panel::dropdown
   :labels="$labels"
   :panels="$panels"
   :currentPanel="$currentPanel"
   :getHref="$computeHref"
   />

@else
   <style>
       .panel-switch-modal .fi-modal-content {
           align-items: center !important;
           justify-content: center !important;
       }
   </style>
   <x-filament-switch-panel::icon-button />
   <x-filament-switch-panel::modal
   :modalWidth="$modalWidth"
   :isSlideOver="$isSlideOver"
   :heading="$heading"
   :panels="$panels"
   :computeHref="$computeHref"
   :currentPanel="$currentPanel"
   :renderIconAsImage="$renderIconAsImage"
   :iconSize="$iconSize"
   :icons="$icons"
   :labels="$labels" />
@endif
