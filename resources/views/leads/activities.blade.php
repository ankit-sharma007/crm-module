<!DOCTYPE html>
<html>
<head>
    <title>Lead Activities</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body class="bg-gray-100 font-sans">
    @include('layouts.navigation')
    <div class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Lead Activities</h1>
        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="lead_id" class="block text-sm font-medium text-gray-700">Filter by Lead</label>
                    <select name="lead_id" id="lead_id" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Leads</option>
                        @foreach ($leads as $id => $name)
                            <option value="{{ $id }}" {{ request('lead_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700">Filter by User</label>
                    <select name="user_id" id="user_id" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Users</option>
                        @foreach ($users as $id => $name)
                            <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="action" class="block text-sm font-medium text-gray-700">Filter by Action</label>
                    <select name="action" id="action" class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Actions</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition duration-300">Filter</button>
                </div>
            </form>
        </div>
        <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Lead</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">User</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Action</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Notes</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($activities as $activity)
                        @can('view-lead-activity', $activity)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-gray-600">
                                    @if ($activity->lead)
                                        {{ $activity->lead->first_name }} {{ $activity->lead->last_name }}
                                    @else
                                        Deleted Lead
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $activity->user->name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ ucfirst($activity->action->value) }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $activity->notes ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @endcan
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $activities->links('pagination::tailwind') }}
        </div>
    </div>
</body>
</html>
