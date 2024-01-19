@props([
    'labels',
    'currentPanel',
    'panels',
    'getHref'
])
<x-filament::dropdown teleport placement="bottom-end" {{ $attributes }}>
   <x-slot name="trigger">
      <button type="button"
              class="flex items-center justify-center w-full p-2 text-sm font-medium rounded-lg shadow-sm outline-none group gap-x-3 bg-primary-500">
                <span class="w-5 h-5 font-semibold bg-white rounded-full shrink-0 text-primary-500">
                    {{str($labels[$currentPanel->getId()] ?? $currentPanel->getId())->substr(0, 1)->upper()}}
                </span>
         <span class="text-white">
                    {{ $labels[$currentPanel->getId()] ?? str($currentPanel->getId())->ucfirst() }}
                </span>
         <x-filament::icon
         icon="heroicon-m-chevron-down"
         icon-alias="panels::panel-switch-simple-icon"
         class="w-5 h-5 text-white ms-auto shrink-0"
         />
      </button>
   </x-slot>
   <x-filament::dropdown.list>
      @foreach ($panels as $panel)
         <x-filament::dropdown.list.item
         :href="$getHref($panel)"
         :badge="str($labels[$panel->getId()] ?? $panel->getId())->substr(0, 2)->upper()"
         tag="a"
         >
            {{ $labels[$panel->getId()] ?? str($panel->getId())->ucfirst() }}
         </x-filament::dropdown.list.item>
      @endforeach
   </x-filament::dropdown.list>
</x-filament::dropdown>
