<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            作成
        </h2>
    </x-slot>
    <div id="upload-form" data-upload-url="{{ $uploadUrl }}" data-max-text-length={{ $maxTextLength }}
        data-max-image-size={{ $maxImageSize }} data-list-url={{ $listUrl }} data-image-type={{ $imageType }}
        data-mode={{ $mode }}
        @isset($diary)
            data-diary-id={{ $diary->id }} data-diary-image-url={{ asset('storage/images/' . $diary->file_name) }}
            data-diary-text="{{ $diary->content }}"
        @endisset>
    </div>
    @vite(['resources/js/diaries/save.js'])
</x-app-layout>
