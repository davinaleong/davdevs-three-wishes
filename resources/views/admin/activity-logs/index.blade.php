<x-admin-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 print:px-0 space-y-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 print:p-0 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Admin Activity Logs</h2>
                    <div class="flex space-x-3 print:hidden">
                        <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Print Logs
                        </button>
                        <a href="{{ route('admin.activity-logs.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Export CSV
                        </a>
                    </div>
                </div>

                <!-- Filter Form -->
                <form method="GET" class="mb-6 bg-gray-50 p-4 rounded-lg print:hidden">
                    <div class="print:hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="action" class="block text-sm font-medium text-gray-700">Action</label>
                            <select name="action" id="action" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Actions</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                        {{ $action }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="admin" class="block text-sm font-medium text-gray-700">Admin Email</label>
                            <input type="text" name="admin" id="admin" value="{{ request('admin') }}" 
                                    placeholder="Search by email..."
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700">From Date</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700">To Date</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="flex space-x-3 no-print">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Filter
                        </button>
                        <a href="{{ route('admin.activity-logs.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md text-sm font-medium">
                            Clear
                        </a>
                    </div>
                </form>

                <!-- Activity Logs Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 print-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Admin
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Details
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date/Time
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $log->admin->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $log->admin->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if(str_contains($log->action, 'LOGIN')) bg-green-100 text-green-800
                                            @elseif(str_contains($log->action, 'LOGOUT')) bg-red-100 text-red-800
                                            @elseif(str_contains($log->action, 'CREATED')) bg-blue-100 text-blue-800
                                            @elseif(str_contains($log->action, 'UPDATED')) bg-yellow-100 text-yellow-800
                                            @elseif(str_contains($log->action, 'DELETED')) bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ str_replace('ADMIN_', '', $log->action) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($log->meta)
                                            <div class="text-sm text-gray-900 max-w-xs">
                                                @foreach($log->meta as $key => $value)
                                                    @if(!in_array($key, ['ip', 'user_agent']))
                                                        <div><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> 
                                                            @if(is_array($value) || is_object($value))
                                                                {{ json_encode($value) }}
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endforeach
                                                
                                                @if(isset($log->meta['ip']))
                                                    <div class="text-xs print-ip">
                                                        {{ $log->meta['ip'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>{{ $log->created_at->format('M j, Y') }}</div>
                                        <div>{{ $log->created_at->format('g:i A') }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        No activity logs found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($logs->hasPages())
                    <div class="mt-6 no-print">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>