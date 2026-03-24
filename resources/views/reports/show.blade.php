@extends('layouts.dashtemp')

@section('content')
<div class="min-h-screen bg-linear-to-br from-slate-50 to-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('reports.index') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm mb-4 inline-block">
                ← Back to Reports
            </a>
            <h1 class="text-3xl font-bold text-slate-900">Report Details</h1>
        </div>

        <!-- Report Status Card -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-8">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-medium text-slate-600">Report Status:</label>
                    @php
                        $colorMap = [
                            'submitted' => 'bg-blue-100 text-blue-800',
                            'reviewed' => 'bg-yellow-100 text-yellow-800',
                            'acknowledged' => 'bg-green-100 text-green-800',
                            'dismissed' => 'bg-gray-100 text-gray-800',
                        ];
                    @endphp
                    <span class="inline-block mt-2 {{ $colorMap[$report->status] ?? 'bg-gray-100 text-gray-800' }} px-4 py-2 rounded-full font-medium">
                        {{ $report->getStatusLabel() }}
                    </span>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Report Type:</label>
                    <p class="mt-2 font-medium text-slate-900">{{ $report->getReportTypeLabel() }}</p>
                </div>
            </div>
        </div>

        <!-- Email Details -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-8">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Email Details</h2>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="text-sm font-medium text-slate-600">From:</label>
                    <p class="text-slate-900">{{ $report->email->from }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Subject:</label>
                    <p class="text-slate-900">{{ $report->email->subject }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Date Received:</label>
                    <p class="text-slate-900">{{ $report->email->date->format('M d, Y H:i') }}</p>
                </div>
                @if ($report->email->detectionResult)
                    <div>
                        <label class="text-sm font-medium text-slate-600">System Decision:</label>
                        <div class="flex items-center gap-2 mt-1">
                            @if ($report->email->detectionResult->final_decision === 'phishing')
                                <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                                    🚨 Flagged as Phishing
                                </span>
                            @else
                                <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                    ✓ Legitimate
                                </span>
                            @endif
                            <span class="text-slate-600 text-sm">(Confidence: {{ number_format($report->email->detectionResult->ml_confidence * 100, 1) }}%)</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Report Information -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-8">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Your Report</h2>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="text-sm font-medium text-slate-600">Report Type:</label>
                    <p class="text-slate-900 font-medium">{{ $report->getReportTypeLabel() }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Submitted On:</label>
                    <p class="text-slate-900">{{ $report->created_at->format('M d, Y H:i') }}</p>
                </div>
                @if ($report->reason)
                    <div>
                        <label class="text-sm font-medium text-slate-600">Your Notes:</label>
                        <p class="text-slate-900 mt-2 p-4 bg-slate-50 rounded border border-slate-200">
                            {{ $report->reason }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Admin Response (if available) -->
        @if ($report->admin_notes)
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-8">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Security Team Response</h2>
                <div class="p-4 bg-slate-50 rounded border border-slate-200">
                    <p class="text-slate-900">{{ $report->admin_notes }}</p>
                </div>
                @if ($report->updated_at && $report->updated_at->ne($report->created_at))
                    <p class="text-sm text-slate-600 mt-3">
                        Reviewed on {{ $report->updated_at->format('M d, Y H:i') }}
                    </p>
                @endif
            </div>
        @endif

        <!-- Actions -->
        <div class="flex gap-3">
            @if ($report->status === 'submitted')
                <form action="{{ route('reports.delete', $report->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                        Delete Report
                    </button>
                </form>
            @endif
            <a href="{{ route('reports.index') }}" class="px-6 py-2 bg-slate-200 text-slate-900 font-medium rounded-lg hover:bg-slate-300 transition-colors">
                Back to Reports
            </a>
        </div>

        <!-- Info Box -->
        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="font-medium text-blue-900 mb-2">ℹ️ Report Status Guide</h3>
            <ul class="text-sm text-blue-800 space-y-2">
                <li><strong>Submitted:</strong> Your report has been received and is awaiting review</li>
                <li><strong>Reviewed:</strong> The security team has reviewed your report</li>
                <li><strong>Acknowledged:</strong> Action has been taken based on your feedback</li>
                <li><strong>Dismissed:</strong> The security team determined no action was needed</li>
            </ul>
        </div>
    </div>
</div>
@endsection
