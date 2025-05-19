<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            一覧ページ
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="space-y-4">
                    @if (count($diaries) < 1)
                        <div class="text-gray-800 text-xl">
                            まだ日記がありません
                        </div>
                    @else
                        @foreach ($diaries as $diary)
                            <div class="bg-white rounded-2xl shadow p-4 flex items-start space-x-4">
                                <img src="{{ asset('storage/images/' . $diary->file_name) }}" alt="画像"
                                    class="max-w-12 max-h-12 object-cover rounded-md">
                                <div class="text-gray-800 text-sm">
                                    {{ $diary->content }}
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

            </div>
        </div>
        <div class="mt-8">
            {{ $diaries->links() }}
        </div>

    </div>
</x-app-layout>
