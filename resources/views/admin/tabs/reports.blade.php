<!-- Reports Tab -->
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <!-- Filter -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 User Reports</h3>
            <form method="GET" action="{{ route('admin.reports') }}" class="flex gap-4">
                <input type="text" name="search" placeholder="Search by subject or sender" 
                       value="{{ request('search') }}"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="investigating" {{ request('status') === 'investigating' ? 'selected' : '' }}>Investigating</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="false_positive" {{ request('status') === 'false_positive' ? 'selected' : '' }}>False Positive</option>
                </select>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    🔍 Filter
                </button>
            </form>
        </div>

        <!-- Reports Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Reporter</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Reported</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($reports as $report)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $report->user->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 truncate max-w-xs">{{ $report->email->subject }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst($report->report_category) }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span @class([
                                'px-3 py-1 rounded-full text-xs font-semibold',
                                'bg-yellow-100 text-yellow-700' => $report->admin_status === 'pending',
                                'bg-blue-100 text-blue-700' => $report->admin_status === 'investigating',
                                'bg-green-100 text-green-700' => $report->admin_status === 'resolved',
                                'bg-purple-100 text-purple-700' => $report->admin_status === 'false_positive',
                            ])>
                                {{ ucfirst(str_replace('_', ' ', $report->admin_status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $report->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.report-detail', $report->id) }}" 
                               class="text-blue-600 hover:text-blue-800 font-medium">
                                View →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No reports found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $reports->links() }}
        </div>
    </div>
</div>
