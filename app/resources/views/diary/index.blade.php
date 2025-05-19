@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">一覧ページ</h1>

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

        <div class="mt-8">
            {{ $diaries->links() }}
        </div>
    </div>
@endsection
