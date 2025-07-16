<!DOCTYPE html>
<html>
<head>
    <title>My Leads</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body class="bg-gray-100">
    @include('layouts.navigation')
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">My Leads</h1>
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                {{ session('success') }}
            </div>
        @endif
        <div class="bg-white shadow-md rounded">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Phone</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leads as $lead)
                        <tr class="border-b">
                            <td class="px-6 py-4"><a href="{{ route('leads.show', $lead) }}" class="text-blue-500">{{ $lead->first_name }} {{ $lead->last_name }}</a></td>
                            <td class="px-6 py-4">{{ $lead->email }}</td>
                            <td class="px-6 py-4">{{ $lead->phone ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ ucfirst($lead->status->value) }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('leads.edit', $lead) }}" class="text-blue-500">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
