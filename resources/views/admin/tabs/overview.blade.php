<!-- Overview Tab Content -->
<div class="max-w-7xl mx-auto">
    <!-- Key Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_users'] }}</p>
                </div>
                <div class="text-blue-500 text-3xl">👥</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Emails Scanned</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_emails_scanned']) }}</p>
                </div>
                <div class="text-purple-500 text-3xl">📧</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Phishing Detected</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ $stats['phishing_emails'] }}</p>
                </div>
                <div class="text-red-500 text-3xl">⚠️</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Legitimate</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['legitimate_emails'] }}</p>
                </div>
                <div class="text-green-500 text-3xl">✅</div>
            </div>
        </div>
    </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Total Detections</h3>
            <p class="text-4xl font-bold text-blue-600">{{ number_format($stats['total_detections']) }}</p>
            <p class="text-sm text-gray-500 mt-2">System-wide detection runs</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Admin Accounts</h3>
            <p class="text-4xl font-bold text-purple-600">{{ $stats['total_admins'] }}</p>
            <p class="text-sm text-gray-500 mt-2">Administrative users</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Pending Reports</h3>
            <p class="text-4xl font-bold text-orange-600">{{ $stats['pending_reports'] }}</p>
            <p class="text-sm text-gray-500 mt-2">Awaiting investigation</p>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Detections -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">📊 Recent Detections</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">From</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Decision</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Score</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recent_detections as $detection)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-600 truncate">{{ substr($detection->email->from, 0, 25) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span @class([
                                    'px-3 py-1 rounded-full text-xs font-semibold',
                                    'bg-red-100 text-red-700' => $detection->final_decision === 'phishing',
                                    'bg-green-100 text-green-700' => $detection->final_decision === 'legitimate',
                                ])>
                                    {{ ucfirst($detection->final_decision) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-gray-700">{{ $detection->rule_score }}/100</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No detections yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Emails -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">📧 Recent Emails</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recent_emails as $email)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-600 truncate">{{ substr($email->subject, 0, 25) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $email->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $email->date?->format('M d, H:i') ?? 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No emails yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
