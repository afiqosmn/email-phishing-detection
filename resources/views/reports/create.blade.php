@extends('layouts.dashtemp')

@section('content')
<div class="min-h-screen bg-linear-to-br from-slate-50 to-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('reports.index') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm mb-4 inline-block">
                ← Back to Reports
            </a>
            <h1 class="text-3xl font-bold text-slate-900">Report an Email</h1>
            <p class="text-slate-600 mt-2">Help us improve our detection system by reporting this email</p>
        </div>

        <!-- Email Preview -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-8">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Email Details</h2>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="text-sm font-medium text-slate-600">From:</label>
                    <p class="text-slate-900">{{ $email->from }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Subject:</label>
                    <p class="text-slate-900">{{ $email->subject }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Date:</label>
                    <p class="text-slate-900">{{ $email->date->format('M d, Y H:i') }}</p>
                </div>
                @if ($detectionResult)
                    <div>
                        <label class="text-sm font-medium text-slate-600">System Decision:</label>
                        <div class="flex items-center gap-2 mt-1">
                            @if ($detectionResult->final_decision === 'phishing')
                                <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                                    🚨 Flagged as Phishing
                                </span>
                            @else
                                <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                    ✓ Legitimate
                                </span>
                            @endif
                            <span class="text-slate-600 text-sm">(Confidence: {{ number_format($detectionResult->ml_confidence * 100, 1) }}%)</span>
                        </div>
                    </div>
                @endif
                <div>
                    <label class="text-sm font-medium text-slate-600">Preview:</label>
                    <div class="mt-2 p-4 bg-slate-50 rounded border border-slate-200 text-sm text-slate-600 max-h-48 overflow-y-auto">
                        {{ Str::limit($email->snippet, 300) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Form -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-6">Your Report</h2>

            <form action="{{ route('reports.store') }}" method="POST">
                @csrf

                <input type="hidden" name="email_id" value="{{ $email->id }}">

                <!-- Report Type -->
                <div class="mb-6">
                    <label for="report_type" class="block text-sm font-medium text-slate-900 mb-2">
                        Report Type <span class="text-red-600">*</span>
                    </label>
                    <select id="report_type" name="report_type" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('report_type') @enderror" required>
                        <option value="">Select a report type...</option>
                        <option value="false_positive">
                            ✓ False Positive (System flagged it, but it's legitimate)
                        </option>
                        <option value="false_negative">
                            🚨 False Negative (System missed it, it's actually phishing)
                        </option>
                        <option value="unrequested_phishing">
                            ⚠️ Unrequested Phishing (Report suspicious email regardless)
                        </option>
                        <option value="whitelist_request">
                            ⭐ Whitelist Request (Trust this sender)
                        </option>
                        <option value="other">
                            ❓ Other (Please describe in reason field)
                        </option>
                    </select>
                    @error('report_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reason -->
                <div class="mb-6">
                    <label for="reason" class="block text-sm font-medium text-slate-900 mb-2">
                        Your Reason or Notes
                    </label>
                    <textarea id="reason" name="reason" rows="6" placeholder="Please provide any additional context or details about your report..." class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('reason') @enderror"></textarea>
                    <p class="mt-1 text-sm text-slate-600">Example: 'This looks like a phishing attempt spoofing our bank. I received 3 similar emails today.'</p>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h3 class="font-medium text-blue-900 mb-2">💡 Why Your Feedback Matters</h3>
                    <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                        <li>Helps improve our detection rules and machine learning model</li>
                        <li>Identifies emerging phishing threats and patterns</li>
                        <li>Supports security team investigations</li>
                        <li>Contributes to organizational threat intelligence</li>
                    </ul>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Submit Report
                    </button>
                    <a href="{{ route('reports.index') }}" class="px-6 py-2 bg-slate-200 text-slate-900 font-medium rounded-lg hover:bg-slate-300 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
