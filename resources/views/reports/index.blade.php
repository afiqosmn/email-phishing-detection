@extends('layouts.dashtemp')

@section('content')
<div class="min-h-screen bg-linear-to-br from-slate-50 to-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">My Reports</h1>
            <p class="text-slate-600 mt-2">Manage your phishing email reports and feedback</p>
        </div>

        <!-- Messages -->
        @if ($message = Session::get('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start">
                <svg class="h-5 w-5 text-green-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-medium text-green-800">{{ $message }}</p>
                </div>
            </div>
        @endif

        @if ($message = Session::get('info'))
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg flex items-start">
                <svg class="h-5 w-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-medium text-blue-800">{{ $message }}</p>
                </div>
            </div>
        @endif

        <!-- Reports Table -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            @if ($reports->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50">
                                <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900">From</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900">Subject</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900">Report Type</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900">Date</th>
                                <th class="px-6 py-3 text-right text-sm font-semibold text-slate-900">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reports as $report)
                                <tr class="border-b border-slate-200 hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        {{ Str::limit($report->email->from, 30) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-900">
                                        <a href="{{ route('reports.show', $report->id) }}" class="font-medium text-blue-600 hover:text-blue-800">
                                            {{ Str::limit($report->email->subject, 40) }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-block bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs font-medium">
                                            {{ $report->getReportTypeLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @php
                                            $colorMap = [
                                                'submitted' => 'bg-blue-100 text-blue-800',
                                                'reviewed' => 'bg-yellow-100 text-yellow-800',
                                                'acknowledged' => 'bg-green-100 text-green-800',
                                                'dismissed' => 'bg-gray-100 text-gray-800',
                                            ];
                                        @endphp
                                        <span class="inline-block {{ $colorMap[$report->status] ?? 'bg-gray-100 text-gray-800' }} px-3 py-1 rounded-full text-xs font-medium">
                                            {{ $report->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        {{ $report->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('reports.show', $report->id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                                View
                                            </a>
                                            @if ($report->status === 'submitted')
                                                <form action="{{ route('reports.delete', $report->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $reports->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-slate-900">No reports yet</h3>
                    <p class="mt-1 text-sm text-slate-500">Help us improve by reporting phishing emails or feedback.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
