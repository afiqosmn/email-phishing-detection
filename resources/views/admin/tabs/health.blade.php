<!-- System Health Tab -->
<div class="max-w-6xl mx-auto">
    <!-- System Status Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- ML Service -->
        <div class="bg-white rounded-lg shadow p-6 border-t-4 {{ $health_data['ml_service']['status'] === 'online' ? 'border-green-500' : 'border-red-500' }}">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">🤖 ML Service</h3>
            <div class="flex items-center mb-2">
                <div class="w-3 h-3 rounded-full mr-2 {{ $health_data['ml_service']['status'] === 'online' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                <span class="font-medium {{ $health_data['ml_service']['status'] === 'online' ? 'text-green-700' : 'text-red-700' }}">
                    {{ ucfirst($health_data['ml_service']['status']) }}
                </span>
            </div>
            @if($health_data['ml_service']['status'] === 'online')
            <p class="text-sm text-gray-600">Response: {{ $health_data['ml_service']['response_time'] }}</p>
            @else
            <p class="text-sm text-red-600">{{ $health_data['ml_service']['error'] ?? 'Service offline' }}</p>
            @endif
        </div>

        <!-- Database -->
        <div class="bg-white rounded-lg shadow p-6 border-t-4 {{ $health_data['database']['status'] === 'healthy' ? 'border-green-500' : 'border-red-500' }}">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">🗄️ Database</h3>
            <div class="flex items-center mb-2">
                <div class="w-3 h-3 rounded-full mr-2 {{ $health_data['database']['status'] === 'healthy' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                <span class="font-medium {{ $health_data['database']['status'] === 'healthy' ? 'text-green-700' : 'text-red-700' }}">
                    {{ ucfirst($health_data['database']['status']) }}
                </span>
            </div>
            <p class="text-sm text-gray-600">Connection OK</p>
        </div>

        <!-- Cache -->
        <div class="bg-white rounded-lg shadow p-6 border-t-4 {{ $health_data['cache']['status'] === 'healthy' ? 'border-green-500' : 'border-red-500' }}">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">⚡ Cache</h3>
            <div class="flex items-center mb-2">
                <div class="w-3 h-3 rounded-full mr-2 {{ $health_data['cache']['status'] === 'healthy' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                <span class="font-medium {{ $health_data['cache']['status'] === 'healthy' ? 'text-green-700' : 'text-red-700' }}">
                    {{ ucfirst($health_data['cache']['status']) }}
                </span>
            </div>
            <p class="text-sm text-gray-600">System cache</p>
        </div>

        <!-- Queue -->
        <div class="bg-white rounded-lg shadow p-6 border-t-4 {{ $health_data['queue']['status'] === 'running' ? 'border-green-500' : 'border-orange-500' }}">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">📦 Queue</h3>
            <div class="flex items-center mb-2">
                <div class="w-3 h-3 rounded-full mr-2 {{ $health_data['queue']['status'] === 'running' ? 'bg-green-500' : 'bg-orange-500' }}"></div>
                <span class="font-medium {{ $health_data['queue']['status'] === 'running' ? 'text-green-700' : 'text-orange-700' }}">
                    {{ ucfirst($health_data['queue']['status']) }}
                </span>
            </div>
            <p class="text-sm text-gray-600">{{ $health_data['queue']['pending_jobs'] ?? 0 }} pending jobs</p>
        </div>
    </div>

    <!-- Detailed Status -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Detailed Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 System Information</h3>
            <div class="space-y-4">
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600">Application</span>
                    <span class="font-medium">PhishingFYP v1.0</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600">Environment</span>
                    <span class="font-medium">{{ config('app.env') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600">PHP Version</span>
                    <span class="font-medium">{{ phpversion() }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600">Last Updated</span>
                    <span class="font-medium">{{ now()->format('M d, Y H:i:s') }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Database</span>
                    <span class="font-medium">{{ config('database.default') }}</span>
                </div>
            </div>
        </div>

        <!-- Health Checks -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">✅ Health Checks</h3>
            <div class="space-y-3">
                <div class="flex items-center py-2 border-b border-gray-200">
                    <div class="w-2 h-2 rounded-full bg-green-500 mr-3"></div>
                    <span class="text-gray-700">Database Connection</span>
                </div>
                <div class="flex items-center py-2 border-b border-gray-200">
                    <div class="w-2 h-2 rounded-full {{ $health_data['ml_service']['status'] === 'online' ? 'bg-green-500' : 'bg-red-500' }} mr-3"></div>
                    <span class="text-gray-700">ML Service API</span>
                </div>
                <div class="flex items-center py-2 border-b border-gray-200">
                    <div class="w-2 h-2 rounded-full {{ $health_data['cache']['status'] === 'healthy' ? 'bg-green-500' : 'bg-red-500' }} mr-3"></div>
                    <span class="text-gray-700">Cache System</span>
                </div>
                <div class="flex items-center py-2">
                    <div class="w-2 h-2 rounded-full bg-green-500 mr-3"></div>
                    <span class="text-gray-700">Queue Worker</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Errors (if any) -->
    @if(count($recent_errors) > 0)
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">⚠️ Recent Errors</h3>
        <div class="space-y-2 max-h-64 overflow-y-auto">
            @foreach($recent_errors as $error)
            <div class="p-2 bg-red-50 border border-red-200 rounded text-xs text-red-700 font-mono">
                {{ $error }}
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="bg-green-50 rounded-lg shadow p-6 mt-6 border border-green-200">
        <p class="text-green-700 font-medium">✅ No recent errors detected. System running smoothly!</p>
    </div>
    @endif
</div>
