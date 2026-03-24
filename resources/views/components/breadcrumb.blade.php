<ol class="flex mt-2 md:mt-0">
    @foreach ($breadcrumbs as $index => $breadcrumb)
        <li class="{{ $loop->last ? 'text-gray-500' : 'text-blue-600' }}">
            @if ($breadcrumb['url'] && !$loop->last)
                <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
            @else
                {{ $breadcrumb['label'] }}
            @endif
        </li>
        @if (!$loop->last)
            <li class="mx-2 text-gray-500">/</li>
        @endif
    @endforeach
</ol>
