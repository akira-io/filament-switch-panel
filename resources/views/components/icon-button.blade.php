<x-filament::icon-button
x-data="{}"
icon="heroicon-s-square-2-stack"
icon-alias="panels::panel-switch-modern-icon"
icon-size="lg"
@click="$dispatch('open-modal', { id: 'panel-switch' })"
label="Switch Panels"
@class(["bg-gray-100 !rounded-full dark:bg-custom-500/20"])
style="{{ \Filament\Support\get_color_css_variables('primary', shades: [100, 500]) }}; min-width: 36px;"
/>
