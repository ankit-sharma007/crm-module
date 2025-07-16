<!DOCTYPE html>
<html>
<head>
    <title>Lead Details</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body class="bg-gray-100">
    @include('layouts.navigation')
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Lead: {{ $lead->first_name }} {{ $lead->last_name }}</h1>
        <div class="bg-white p-6 rounded shadow-md">
            <p><strong>Email:</strong> {{ $lead->email }}</p>
            <p><strong>Phone:</strong> {{ $lead->phone ?? 'N/A' }}</p>
            <p><strong>Status:</strong> {{ ucfirst($lead->status) }}</p>
            <p><strong>Assigned To:</strong> {{ $lead->assignedTo->name ?? 'Unassigned' }}</p>
            <p><strong>Notes:</strong> {{ $lead->notes ?? 'N/A' }}</p>
            <h2 class="text-xl font-bold mt-6 mb-2">Activity Log</h2>
            <ul class="list-disc pl-5">
                @foreach ($lead->activities as $activity)
                    <li>{{ ucfirst($activity->action) }} by {{ $activity->user->name }} at {{ $activity->created_at->format('Y-m-d H:i:s') }}: {{ $activity->notes ?? 'N/A' }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</body>
</html>
