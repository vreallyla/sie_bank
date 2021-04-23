@props(['id' => null, 'maxWidth' => null])

<x-jet-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="px-6 py-4 bg-gray-900">
        <div class="text-lg text-gray-400">
            {{ $title }}
        </div>

        <div class="mt-4">
            {{ $content }}
        </div>
        <div wire:loading class="absolute w-full h-full top-0 left-0">
            <i class="w-full text-right pt-4 pr-4 animate-pulse material-icons text-gray-400">filter_center_focus</i>
        </div>
    </div>

    <div class="px-6 py-4 bg-gray-100 text-right bg-gray-900">
        {{ $footer }}
    </div>
</x-jet-modal>
