<!DOCTYPE html>
<html>
<head>
    <title>My Leads</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body class="bg-gray-100 font-sans">
    @include('layouts.navigation')
    <div class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">My Leads</h1>
        <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Email</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Phone</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($leads as $lead)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4"><a href="{{ route('leads.show', $lead) }}" class="text-blue-600 hover:underline">{{ $lead->first_name }} {{ $lead->last_name }}</a></td>
                            <td class="px-6 py-4 text-gray-600">{{ $lead->email }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $lead->phone ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ ucfirst($lead->status->value) }}</td>
                            <td class="px-6 py-4 flex space-x-2">
                                @can('update', $lead)
                                    <a href="{{ route('leads.edit', $lead) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $leads->links('pagination::tailwind') }}
        </div>
    </div>
</body>
</html>
