<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            作成
        </h2>
    </x-slot>
    <div id="upload-form" data-upload-url="{{ $uploadUrl }}" data-max-text-length={{ $maxTextLength }}
        data-max-image-size={{ $maxImageSize }} data-list-url={{ $listUrl }} data-image-type={{ $imageType }}>
    </div>
    @vite(['resources/js/diaries/create.js'])
</x-app-layout>
