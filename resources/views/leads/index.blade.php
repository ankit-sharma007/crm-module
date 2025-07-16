<!DOCTYPE html>
<html>
<head>
    <title>Leads</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body class="bg-gray-100 font-sans">
    @include('layouts.navigation')
    <div class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Leads</h1>
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md">
                {{ session('success') }}
            </div>
        @endif
        <a href="{{ route('leads.create') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition duration-300 mb-6">Create Lead</a>
        <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Email</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Phone</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Assigned To</th>
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
                            <td class="px-6 py-4">
                                @can('assign', \App\Models\Lead::class)
                                    <select onchange="assignLead({{ $lead->id }}, this.value, this)" class="border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Unassigned</option>
                                        @foreach (\App\Models\User::where('is_admin', false)->get() as $agent)
                                            <option value="{{ $agent->id }}" {{ $lead->assigned_to == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <span class="text-gray-600">{{ $lead->assignedTo->name ?? 'Unassigned' }}</span>
                                @endcan
                            </td>
                            <td class="px-6 py-4 flex space-x-2">
                                @can('update', $lead)
                                    <a href="{{ route('leads.edit', $lead) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                @endcan
                                @can('delete', $lead)
                                    <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
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

    <script>
        function assignLead(leadId, userId, selectElement) {
            const csrfToken = "{{csrf_token()}}";
            if (!csrfToken) {
                console.error('CSRF token not found');
                toastr.error('CSRF token not found. Please refresh the page.');
                return;
            }

            const url = '{{ route("leads.assign", ":leadId") }}'.replace(':leadId', leadId);
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ assigned_to: userId || null })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Network response was not ok: ' + response.statusText);
                    });
                }
                return response.json();
            })
            .then(data => {
                toastr.success(data.message);
                selectElement.value = userId || '';
            })
            .catch(error => {
                console.error('Error assigning lead:', error);
                toastr.error('Failed to assign lead: ' + error.message);
                selectElement.value = '{{ $lead->assigned_to ?? '' }}';
            });
        }
    </script>
</body>
</html>
