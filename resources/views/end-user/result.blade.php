@extends('layouts.dashtemp')

@section('page-header')
<h2 class="text-xl font-semibold">Detection Result</h2>
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
    {{ session('success') }}
</div>
@endif

<div class="overflow-x-auto">
    <table class="min-w-full border border-gray-200 rounded-lg shadow-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-3 py-2">No</th>
                <th class="border px-3 py-2">Message ID</th>
                <th class="border px-3 py-2">Final Decision</th>
                <th class="border px-3 py-2">Scanned At</th>
                <th class="border px-3 py-2 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($results as $result)
            <tr class="hover:bg-gray-50">
                <td class="border px-3 py-2">{{ ($results->currentPage() - 1) * $results->perPage() + $loop->iteration }}</td>
                <td class="border px-3 py-2">
                    {{ $result->message_id }}
                </td>
                <td class="border px-3 py-2 font-semibold">
                    {{ ucfirst($result->final_decision) }}
                </td>
                <td class="border px-3 py-2">
                    {{ $result->created_at->format('d M Y - H:i') }}
                </td>
                <td class="border px-3 py-2 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('detection.result', $result->id) }}"
                           class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                            View
                        </a>

                        <form method="POST"
                              action="{{ route('detection.delete', $result->id) }}"
                              onsubmit="return confirm('Delete this result?')">
                            @csrf
                            <button
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-4 text-gray-500">
                    No detection results found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $results->links() }}
</div>

@endsection
