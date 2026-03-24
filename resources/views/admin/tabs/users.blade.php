<!-- Users Management Tab -->
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <!-- Search & Filter -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">👥 User Management</h3>
            <form method="GET" action="{{ route('admin.users') }}" class="flex gap-4">
                <input type="text" name="search" placeholder="Search by name or email" 
                       value="{{ request('search') }}"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <select name="role" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">All Roles</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    🔍 Search
                </button>
            </form>
        </div>

        <!-- Users Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Joined</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Last Login</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span @class([
                                'px-3 py-1 rounded-full text-xs font-semibold',
                                'bg-purple-100 text-purple-700' => $user->role === 'admin',
                                'bg-blue-100 text-blue-700' => $user->role === 'user',
                            ])>
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->last_admin_login?->format('M d, H:i') ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.user-detail', $user->id) }}" 
                               class="text-blue-600 hover:text-blue-800 font-medium">
                                View Details →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No users found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    </div>
</div>
