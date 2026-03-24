@extends('layouts.dashtemp')

@section('page-header')
<h2 class="text-xl font-semibold">Email Status</h2>
@endsection

@section('content')

<div class="overflow-x-auto">
    <table class="min-w-full border border-gray-200 rounded-lg shadow-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-1 py-2">No</th>
                <th class="border px-3 py-2">Message ID</th>
                <th class="border px-3 py-2">From</th>
                <th class="border px-3 py-2">Subject</th>
                <th class="border px-3 py-2">Fetched At</th>
                <th class="border px-2 py-2">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($emails as $email)
            <tr class="hover:bg-gray-50">
                <td class="border px-3 py-2">{{ ($emails->currentPage() - 1) * $emails->perPage() + $loop->iteration }}</td>
                <td class="border px-3 py-2">{{ $email->message_id }}</td>
                <td class="border px-3 py-2">{{ $email->from ?? '-' }}</td>
                <td class="border px-3 py-2 truncate max-w-40" title="{{ $email->subject ?? '' }}">
                    {{ $email->subject ?? '-' }}
                </td>
                <td class="border px-3 py-2">{{ $email->created_at->format('d M Y - H:i') }}</td>
                <td class="border px-3 py-2 font-semibold capitalize">{{ $email->processing_status }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-4 text-gray-500">No emails fetched yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $emails->links() }}
</div>

@endsection
