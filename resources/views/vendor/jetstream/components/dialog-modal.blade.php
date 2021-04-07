@props(['id' => null, 'maxWidth' => null])

<x-jet-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="px-6 py-4 bg-gray-900">
        <div class="text-lg text-gray-400">
            {{ $title }}
        </div>

        <div class="mt-4">
            {{ $content }}
        </div>
    </div>

    <div class="px-6 py-4 bg-gray-100 text-right bg-gray-900">
        {{ $footer }}
    </div>
</x-jet-modal>
