<!DOCTYPE html>
<html>
<head>
    <title>Leads</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body class="bg-gray-100">
    @include('layouts.navigation')
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Leads</h1>
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                {{ session('success') }}
            </div>
        @endif
        <a href="{{ route('leads.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Create Lead</a>
        <div class="bg-white shadow-md rounded">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Phone</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Assigned To</th>
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
                                <select onchange="assignLead({{ $lead->id }}, this.value)" class="border rounded p-1">
                                    <option value="">Unassigned</option>
                                    @foreach (\App\Models\User::where('is_admin', false)->get() as $agent)
                                        <option value="{{ $agent->id }}" {{ $lead->assigned_to == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('leads.edit', $lead) }}" class="text-blue-500">Edit</a>
                                <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 ml-2" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function assignLead(leadId, userId) {
            const url = `{{ route('leads.assign', ':leadId') }}`.replace(':leadId', leadId);
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ assigned_to: userId || null })
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                alert(data.message);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to assign lead. Please try again.');
            });
        }
    </script>
</body>
</html>
