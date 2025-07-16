<!DOCTYPE html>
<html>
<head>
    <title>Lead Activities</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body class="bg-gray-100">
    @include('layouts.navigation')
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Lead Activities</h1>
        <form method="GET" class="mb-4">
            <div class="flex space-x-4">
                <div>
                    <label class="block text-gray-700">Filter by Lead</label>
                    <select name="lead_id" class="border rounded p-2">
                        <option value="">All Leads</option>
                        @foreach ($leads as $id => $name)
                            <option value="{{ $id }}" {{ request('lead_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700">Filter by User</label>
                    <select name="user_id" class="border rounded p-2">
                        <option value="">All Users</option>
                        @foreach ($users as $id => $name)
                            <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700">Filter by Action</label>
                    <select name="action" class="border rounded p-2">
                        <option value="">All Actions</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="self-end">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
                </div>
            </div>
        </form>
        <div class="bg-white shadow-md rounded">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-6 py-3 text-left">Lead</th>
                        <th class="px-6 py-3 text-left">User</th>
                        <th class="px-6 py-3 text-left">Action</th>
                        <th class="px-6 py-3 text-left">Notes</th>
                        <th class="px-6 py-3 text-left">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($activities as $activity)
                        @can('view-lead-activity', $activity)
                            <tr class="border-b">
                                <td class="px-6 py-4">
                                    @if ($activity->lead)
                                        {{ $activity->lead->first_name }} {{ $activity->lead->last_name }}
                                    @else
                                        Deleted Lead
                                    @endif
                                </td>
                                <td class="px-6 py-4">{{ $activity->user->name }}</td>
                                <td class="px-6 py-4">{{ ucfirst($activity->action->value) }}</td>
                                <td class="px-6 py-4">{{ $activity->notes ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @endcan
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
