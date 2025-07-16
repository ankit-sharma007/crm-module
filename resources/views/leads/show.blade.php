<!DOCTYPE html>
<html>
<head>
    <title>Lead Details</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body class="bg-gray-100 font-sans">
    @include('layouts.navigation')
    <div class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Lead: {{ $lead->first_name }} {{ $lead->last_name }}</h1>
        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-700">Email</p>
                    <p class="text-gray-600">{{ $lead->email }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Phone</p>
                    <p class="text-gray-600">{{ $lead->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Status</p>
                    @can('updateStatus', $lead)
                        <select onchange="updateStatus({{ $lead->id }}, this.value)" class="border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach (\App\Enums\LeadStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ $lead->status->value == $status->value ? 'selected' : '' }}>{{ ucfirst($status->value) }}</option>
                            @endforeach
                        </select>
                    @else
                        <p class="text-gray-600">{{ ucfirst($lead->status->value) }}</p>
                    @endcan
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Assigned To</p>
                    <p class="text-gray-600">{{ $lead->assignedTo->name ?? 'Unassigned' }}</p>
                </div>
            </div>
            <div class="mt-6">
                <p class="text-sm font-medium text-gray-700">Notes</p>
                <p class="text-gray-600">{{ $lead->notes ?? 'N/A' }}</p>
            </div>
            @can('addNote', $lead)
                <div class="mt-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Add Note</h2>
                    <form id="add-note-form" onsubmit="addNote(event, {{ $lead->id }})">
                        @csrf
                        <textarea name="notes" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter note"></textarea>
                        @error('notes')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        <button type="submit" class="mt-2 bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition duration-300">Add Note</button>
                    </form>
                </div>
            @endcan
            <div class="mt-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Activity Log</h2>
                <ul id="activity-log" class="space-y-2">
                    @foreach ($lead->activities as $activity)
                        @can('view-lead-activity', $activity)
                            <li class="p-4 bg-gray-50 rounded-md border border-gray-200">
                                <span class="text-gray-600">{{ ucfirst($activity->action->value) }} by {{ $activity->user->name }} at {{ $activity->created_at->format('Y-m-d H:i:s') }}</span>
                                <p class="text-gray-600">{{ $activity->notes ?? 'N/A' }}</p>
                            </li>
                        @endcan
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <script>
        function updateStatus(leadId, status) {
            const csrfToken = "{{csrf_token()}}";
            if (!csrfToken) {
                console.error('CSRF token not found');
                toastr.error('CSRF token not found. Please refresh the page.');
                return;
            }

            const url = '{{ route("leads.status", ":leadId") }}'.replace(':leadId', leadId);
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ status })
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
                if (data.activity) {
                    const activityList = document.getElementById('activity-log');
                    const li = document.createElement('li');
                    li.className = 'p-4 bg-gray-50 rounded-md border border-gray-200';
                    li.innerHTML = `<span class="text-gray-600">${data.activity.action} by ${data.activity.user} at ${data.activity.created_at}</span><p class="text-gray-600">${data.activity.notes || 'N/A'}</p>`;
                    activityList.prepend(li);
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                toastr.error('Failed to update status: ' + error.message);
            });
        }

        function addNote(event, leadId) {
            event.preventDefault();
            const csrfToken = "{{csrf_token()}}";
            if (!csrfToken) {
                console.error('CSRF token not found');
                toastr.error('CSRF token not found. Please refresh the page.');
                return;
            }

            const form = document.getElementById('add-note-form');
            const notes = form.querySelector('textarea[name="notes"]').value;
            const url = '{{ route("leads.note", ":leadId") }}'.replace(':leadId', leadId);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ notes })
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
                form.reset();
                if (data.activity) {
                    const activityList = document.getElementById('activity-log');
                    const li = document.createElement('li');
                    li.className = 'p-4 bg-gray-50 rounded-md border border-gray-200';
                    li.innerHTML = `<span class="text-gray-600">${data.activity.action} by ${data.activity.user} at ${data.activity.created_at}</span><p class="text-gray-600">${data.activity.notes || 'N/A'}</p>`;
                    activityList.prepend(li);
                }
            })
            .catch(error => {
                console.error('Error adding note:', error);
                toastr.error('Failed to add note: ' + error.message);
            });
        }
    </script>
</body>
</html>
