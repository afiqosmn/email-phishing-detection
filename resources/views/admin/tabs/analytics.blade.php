<!-- Analytics Tab -->
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex gap-4">
        <form method="GET" action="{{ route('admin.analytics') }}" class="flex gap-2">
            <select name="period" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="7" {{ $period === '7' ? 'selected' : '' }}>Last 7 days</option>
                <option value="30" {{ $period === '30' ? 'selected' : '' }}>Last 30 days</option>
                <option value="90" {{ $period === '90' ? 'selected' : '' }}>Last 90 days</option>
                <option value="365" {{ $period === '365' ? 'selected' : '' }}>Last year</option>
            </select>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <p class="text-gray-600 text-sm font-medium">Total Detections</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $detection_stats['total_detections'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <p class="text-gray-600 text-sm font-medium">Phishing</p>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ $detection_stats['phishing_count'] }}</p>
            <p class="text-xs text-gray-500 mt-2">
                @if($detection_stats['total_detections'] > 0)
                    {{ round(($detection_stats['phishing_count'] / $detection_stats['total_detections']) * 100) }}%
                @else
                    0%
                @endif
            </p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <p class="text-gray-600 text-sm font-medium">Legitimate</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $detection_stats['legitimate_count'] }}</p>
            <p class="text-xs text-gray-500 mt-2">
                @if($detection_stats['total_detections'] > 0)
                    {{ round(($detection_stats['legitimate_count'] / $detection_stats['total_detections']) * 100) }}%
                @else
                    0%
                @endif
            </p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <p class="text-gray-600 text-sm font-medium">Avg ML Confidence</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">{{ round($detection_stats['avg_ml_confidence'] * 100) }}%</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Rule Performance -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Rule Detection Results</h3>
            <div class="space-y-3">
                @foreach($rule_performance as $perf)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ ucfirst($perf->rule_result) }}</span>
                        <span class="text-sm font-medium text-gray-700">{{ $perf->count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div @class([
                            'h-2 rounded-full',
                            'bg-red-500' => $perf->rule_result === 'phishing',
                            'bg-green-500' => $perf->rule_result === 'legitimate',
                            'bg-yellow-500' => $perf->rule_result === 'suspicious',
                        ]) style="width: {{ $detection_stats['total_detections'] > 0 ? ($perf->count / $detection_stats['total_detections']) * 100 : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- ML Performance -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">🤖 ML Detection Results</h3>
            <div class="space-y-3">
                @foreach($ml_performance as $perf)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ ucfirst($perf->ml_result) }}</span>
                        <span class="text-sm font-medium text-gray-700">{{ $perf->count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div @class([
                            'h-2 rounded-full',
                            'bg-red-500' => $perf->ml_result === 'phishing',
                            'bg-green-500' => $perf->ml_result === 'legitimate',
                        ]) style="width: {{ $detection_stats['total_detections'] > 0 ? ($perf->count / $detection_stats['total_detections']) * 100 : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Average Scores</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Rule Score</span>
                        <span class="font-bold text-gray-900">{{ round($detection_stats['avg_rule_score']) }}/100</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full bg-blue-500" style="width: {{ $detection_stats['avg_rule_score'] }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">ML Confidence</span>
                        <span class="font-bold text-gray-900">{{ round($detection_stats['avg_ml_confidence'] * 100) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full bg-purple-500" style="width: {{ $detection_stats['avg_ml_confidence'] * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📈 Detection Accuracy</h3>
            <div class="space-y-2">
                <p class="text-gray-600">Based on last {{ $period }} days of detection data</p>
                <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-sm text-blue-900">
                        <strong>Total Scans:</strong> {{ $detection_stats['total_detections'] }}
                    </p>
                    <p class="text-sm text-blue-900 mt-2">
                        <strong>Threat Detection Rate:</strong> 
                        @if($detection_stats['total_detections'] > 0)
                            {{ round(($detection_stats['phishing_count'] / $detection_stats['total_detections']) * 100, 2) }}%
                        @else
                            0%
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
