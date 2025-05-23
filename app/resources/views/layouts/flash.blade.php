@foreach (['success', 'error', 'warning', 'info'] as $msg)
    @if (session($msg))
        <div
            class="p-4 mb-4 text-sm rounded
            @if ($msg === 'success') bg-green-100 text-green-800
            @elseif ($msg === 'error') bg-red-100 text-red-800
            @elseif ($msg === 'warning') bg-yellow-100 text-yellow-800
            @elseif ($msg === 'info') bg-blue-100 text-blue-800 @endif">
            {{ session($msg) }}
        </div>
    @endif
@endforeach
