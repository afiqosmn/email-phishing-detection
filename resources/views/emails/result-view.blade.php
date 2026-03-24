@extends('layouts.dashtemp')

@section('page-header')
<h1 class="text-2xl font-semibold">🕵️‍♀️ Email Detection Result</h1>
@endsection

@section('content')
    <div class="w-9/10 mx-auto bg-gray-50 rounded-2xl shadow-md p-6">

        <!-- Email Info -->
        <div class="mb-6 border-b pb-4">
            <!-- Message ID and Dates - 2 Column Layout -->
            <div class="flex justify-between mb-4">
                <!-- Left: Message ID -->
                <div>
                    <p class="text-sm text-gray-500">Message ID:</p>
                    <p class="font-mono text-gray-800 text-sm">
                        {{ $result->message_id }}
                    </p>
                </div>
                <!-- Right: Email Date and Scanned -->
                <div>
                    <p class="text-sm text-gray-600">
                        <strong>Email Date:</strong> {{ $result->email->date ? $result->email->date->format('d/m/Y H:i:s') : 'N/A' }}
                    </p>
                    <p class="text-sm text-gray-600">
                        <strong>Scanned:</strong> {{ $result->created_at->format('d/m/Y H:i:s') }}
                    </p>
                </div>
            </div>

            <!-- From and Subject -->
            <p class="text-sm text-gray-600 mt-2">
                <strong>From:</strong> {{ $result->email->from }}
            </p>
            <p class="text-sm text-gray-600">
                <strong>Subject:</strong> {{ $result->email->subject }}
            </p>
        </div>

        <!-- Detection Results - Two Column Layout -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Rule-Based Result -->
            <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
                <h2 class="text-lg font-medium text-blue-900 mb-3">
                    📋 Rule-Based Detection Result
                </h2>

                <div class="flex items-center justify-between mb-4">
                    <span class="text-blue-700">Status:</span>

                    <span @class([
                        'px-4 py-2 rounded-lg text-sm font-semibold',
                        'bg-red-100 text-red-700' => strtolower($result->rule_result) === 'phishing',
                        'bg-green-100 text-green-700' => strtolower($result->rule_result) === 'legitimate',
                        'bg-yellow-100 text-yellow-700' => strtolower($result->rule_result) === 'suspicious',
                        'bg-gray-200 text-gray-600' => !in_array(strtolower($result->rule_result), ['phishing','legitimate','suspicious']),
                    ])>
                        {{ ucfirst($result->rule_result) }}
                    </span>
                </div>

                <div class="mt-4 p-3 bg-white rounded border border-gray-300">
                    <p class="text-sm text-gray-700">
                        <strong>Score:</strong> {{ $result->rule_score ?? 0 }}/100
                    </p>
                </div>
            </div>

            <!-- ML Detection Result -->
            <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
                <h2 class="text-lg font-medium text-blue-900 mb-3">
                    🤖 ML Detection Result
                </h2>

                <div class="flex items-center justify-between mb-4">
                    <span class="text-blue-700">Status:</span>
                    <span @class([
                        'px-4 py-2 rounded-lg text-sm font-semibold',
                        'bg-red-100 text-red-700' => strtolower($result->ml_result) === 'phishing',
                        'bg-green-100 text-green-700' => strtolower($result->ml_result) === 'legitimate',
                    ])>
                        {{ ucfirst($result->ml_result) }}
                    </span>
                </div>

                <div class="mt-4 p-3 bg-white rounded border border-blue-300">
                    <p class="text-sm text-blue-800">
                        <strong>Confidence:</strong> {{ round($result->ml_confidence * 100) }}%
                    </p>
                </div>
            </div>
        </div>

        <!-- Final Decision Banner -->
        <div class="mb-6 p-4 rounded-xl border-2 @class([
            'bg-red-50 border-red-300' => $result->final_decision === 'phishing',
            'bg-green-50 border-green-300' => $result->final_decision === 'legitimate',
        ])">
            <h3 class="text-lg font-semibold @class([
                'text-red-700' => $result->final_decision === 'phishing',
                'text-green-700' => $result->final_decision === 'legitimate',
            ])">
                ⚠️ Final Decision: <span class="uppercase">{{ $result->final_decision }}</span>
            </h3>
        </div>

        <!-- Evidence Report Section -->
        <div class="mb-6 bg-white rounded-xl p-6 border border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                📊 Evidence Report & Explanation
            </h2>

            <!-- URL Evidence -->
            @if($result->urlEvidences->count() > 0)
            <div class="mb-6 border-b pb-4">
                <h3 class="text-lg font-medium text-gray-700 mb-3 flex items-center">
                    🔗 URL Analysis ({{ $result->urlEvidences->count() }} URLs checked)
                </h3>
                <div class="space-y-3">
                    @foreach($result->urlEvidences as $evidence)
                    <div class="p-4 rounded-lg bg-gray-50 border @class([
                        'border-red-300' => $evidence->status === 'malicious',
                        'border-green-300' => $evidence->status === 'safe',
                        'border-yellow-300' => $evidence->status === 'suspicious',
                        'border-gray-300' => $evidence->status === 'unknown',
                    ])">
                        <div class="flex items-start justify-between mb-2">
                            <p class="text-sm font-mono text-gray-700 break-all">{{ $evidence->url }}</p>
                            <span @class([
                                'px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap ml-2',
                                'bg-red-100 text-red-700' => $evidence->status === 'malicious',
                                'bg-green-100 text-green-700' => $evidence->status === 'safe',
                                'bg-yellow-100 text-yellow-700' => $evidence->status === 'suspicious',
                                'bg-gray-100 text-gray-700' => $evidence->status === 'unknown',
                            ])>
                                {{ ucfirst($evidence->status) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">{{ $evidence->explanation }}</p>
                        @if(!empty($evidence->threat_types))
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach($evidence->threat_types as $threat)
                            <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded">{{ $threat }}</span>
                            @endforeach
                        </div>
                        @endif
                        <p class="text-xs text-gray-500 mt-2">Source: {{ $evidence->source }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Authentication Evidence -->
            @if($result->authenticationEvidences->count() > 0)
            <div class="mb-6 border-b pb-4">
                <h3 class="text-lg font-medium text-gray-700 mb-3 flex items-center">
                    🔐 Email Authentication
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($result->authenticationEvidences as $evidence)
                    <div class="p-4 rounded-lg bg-gray-50 border @class([
                        'border-red-300' => $evidence->result === 'fail',
                        'border-green-300' => $evidence->result === 'pass',
                        'border-yellow-300' => in_array($evidence->result, ['neutral', 'none']),
                        'border-gray-300' => $evidence->result === 'unknown',
                    ])">
                        <div class="flex items-center justify-between mb-2">
                            <p class="font-medium text-gray-700">{{ strtoupper($evidence->check_type) }}</p>
                            <span @class([
                                'px-2 py-1 rounded text-xs font-semibold',
                                'bg-red-100 text-red-700' => $evidence->result === 'fail',
                                'bg-green-100 text-green-700' => $evidence->result === 'pass',
                                'bg-yellow-100 text-yellow-700' => in_array($evidence->result, ['neutral', 'none']),
                                'bg-gray-100 text-gray-700' => $evidence->result === 'unknown',
                            ])>
                                {{ ucfirst($evidence->result) }}
                            </span>
                        </div>
                        @if($evidence->aligned !== null)
                        <p class="text-xs text-gray-600 mb-2">
                            Domain Aligned: <strong>{{ $evidence->aligned ? 'Yes' : 'No' }}</strong>
                        </p>
                        @endif
                        <p class="text-sm text-gray-600">{{ $evidence->explanation }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Keyword Evidence -->
            @if($result->keywordEvidences->count() > 0)
            <div class="mb-6 border-b pb-4">
                <h3 class="text-lg font-medium text-gray-700 mb-3 flex items-center">
                    ⚠️ Suspicious Keywords Detected ({{ $result->keywordEvidences->sum('count') }} total)
                </h3>
                <div class="space-y-3">
                    @foreach($result->keywordEvidences as $evidence)
                    <div class="p-4 rounded-lg bg-gray-50 border border-yellow-300">
                        <div class="flex items-center justify-between mb-2">
                            <p class="font-medium text-gray-700">
                                {{ str_replace('_', ' ', ucfirst($evidence->category)) }}
                            </p>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                {{ $evidence->count }} found
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">{{ $evidence->explanation }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($evidence->keywords_found as $keyword)
                            <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded font-mono">
                                "{{ $keyword }}"
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- HTML Anomaly Evidence -->
            @if($result->htmlAnomalyEvidences->count() > 0)
            <div class="mb-6 border-b pb-4">
                <h3 class="text-lg font-medium text-gray-700 mb-3 flex items-center">
                    🧩 HTML Anomalies Detected
                </h3>
                <div class="space-y-3">
                    @foreach($result->htmlAnomalyEvidences as $evidence)
                    <div class="p-4 rounded-lg bg-gray-50 border @class([
                        'border-red-300' => $evidence->severity === 'high',
                        'border-yellow-300' => $evidence->severity === 'medium',
                        'border-blue-300' => $evidence->severity === 'low',
                    ])">
                        <div class="flex items-center justify-between mb-2">
                            <p class="font-medium text-gray-700">
                                {{ str_replace('_', ' ', ucfirst($evidence->anomaly_type)) }}
                            </p>
                            <span @class([
                                'px-3 py-1 rounded-full text-xs font-semibold',
                                'bg-red-100 text-red-700' => $evidence->severity === 'high',
                                'bg-yellow-100 text-yellow-700' => $evidence->severity === 'medium',
                                'bg-blue-100 text-blue-700' => $evidence->severity === 'low',
                            ])>
                                {{ ucfirst($evidence->severity) }} Severity
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">{{ $evidence->explanation }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($result->urlEvidences->count() === 0 && 
                $result->authenticationEvidences->count() === 0 && 
                $result->keywordEvidences->count() === 0 && 
                $result->htmlAnomalyEvidences->count() === 0)
            <div class="p-4 rounded-lg bg-blue-50 border border-blue-300 text-center">
                <p class="text-sm text-blue-700">No specific evidence collected for this email.</p>
            </div>
            @else
                <!-- Legitimate Email Evidence Explanation -->
                @if($result->final_decision === 'legitimate')
                <div class="mt-6 p-4 rounded-lg bg-green-50 border border-green-200">
                    <h3 class="text-lg font-semibold text-green-900 mb-3">
                        ✅ Why This Email Appears Legitimate
                    </h3>
                    <p class="text-sm text-green-800 mb-3">
                        The following evidence supports the classification of this email as legitimate:
                    </p>
                    <ul class="text-sm text-green-800 space-y-2 list-disc list-inside">
                        @if($result->authenticationEvidences->where('classification', 'legitimate')->count() > 0)
                            <li>Authentication checks (SPF, DKIM, DMARC) passed successfully</li>
                        @endif
                        @if($result->urlEvidences->where('classification', 'legitimate')->count() > 0)
                            <li>All URLs checked appear safe and trustworthy</li>
                        @endif
                        @if($result->keywordEvidences->where('classification', 'legitimate')->count() === 0)
                            <li>No suspicious phishing keywords detected</li>
                        @endif
                        @if($result->htmlAnomalyEvidences->where('classification', 'legitimate')->count() === 0)
                            <li>No suspicious HTML anomalies found</li>
                        @endif
                    </ul>
                </div>
                @endif
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex justify-center gap-4 flex-wrap">
            <a href="{{ route('detection.download-pdf', $result->id) }}"
               class="inline-block bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition font-medium">
                📥 Download PDF Report
            </a>
            <a href="{{ route('reports.create', $result->email->id) }}"
               class="inline-block bg-purple-600 text-white px-6 py-2 rounded-xl hover:bg-purple-700 transition font-medium">
                📝 Report This Email
            </a>
            <a href="{{ route('result') }}"
               class="inline-block bg-blue-600 text-white px-6 py-2 rounded-xl hover:bg-blue-700 transition font-medium">
                ← Back to Results
            </a>
        </div>

    </div>

@endsection
