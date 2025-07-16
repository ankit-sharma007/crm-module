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
            <p><strong>Status:</strong>
                @can('updateStatus', $lead)
                    <select onchange="updateStatus({{ $lead->id }}, this.value)" class="border rounded p-1">
                        @foreach (\App\Enums\LeadStatus::cases() as $status)
                            <option value="{{ $status->value }}" {{ $lead->status->value == $status->value ? 'selected' : '' }}>{{ ucfirst($status->value) }}</option>
                        @endforeach
                    </select>
                @else
                    {{ ucfirst($lead->status->value) }}
                @endcan
            </p>
            <p><strong>Assigned To:</strong> {{ $lead->assignedTo->name ?? 'Unassigned' }}</p>
            <p><strong>Notes:</strong> {{ $lead->notes ?? 'N/A' }}</p>
            @can('addNote', $lead)
                <div class="mt-4">
                    <h2 class="text-xl font-bold mb-2">Add Note</h2>
                    <form id="add-note-form" onsubmit="addNote(event, {{ $lead->id }})">
                        @csrf
                        <textarea name="notes" class="w-full border rounded p-2" placeholder="Enter note"></textarea>
                        @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-2">Add Note</button>
                    </form>
                </div>
            @endcan
            <h2 class="text-xl font-bold mt-6 mb-2">Activity Log</h2>
            <ul id="activity-log" class="list-disc pl-5">
                @foreach ($lead->activities as $activity)
                    @can('view-lead-activity', $activity)
                        <li>
                            {{ ucfirst($activity->action->value) }} by {{ $activity->user->name }} at {{ $activity->created_at->format('Y-m-d H:i:s') }}:
                            {{ $activity->notes ?? 'N/A' }}
                            (Lead: {{ $activity->lead ? $activity->lead->first_name . ' ' . $activity->lead->last_name : 'Deleted Lead' }})
                        </li>
                    @endcan
                @endforeach
            </ul>
        </div>
    </div>

    <script>
        function updateStatus(leadId, status) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
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
                if (!response.ok) throw new Error('Network response was not ok: ' + response.statusText);
                return response.json();
            })
            .then(data => {
                toastr.success(data.message);
                if (data.activity) {
                    const activityList = document.getElementById('activity-log');
                    const li = document.createElement('li');
                    li.textContent = `${data.activity.action} by ${data.activity.user} at ${data.activity.created_at}: ${data.activity.notes || 'N/A'} (Lead: {{ $lead->first_name }} {{ $lead->last_name }})`;
                    activityList.prepend(li);
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                toastr.error('Failed to update status. Please try again.');
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
                if (!response.ok) throw new Error('Network response was not ok: ' + response.statusText);
                return response.json();
            })
            .then(data => {
                toastr.success(data.message);
                form.reset();
                if (data.activity) {
                    const activityList = document.getElementById('activity-log');
                    const li = document.createElement('li');
                    li.textContent = `${data.activity.action} by ${data.activity.user} at ${data.activity.created_at}: ${data.activity.notes || 'N/A'} (Lead: {{ $lead->first_name }} {{ $lead->last_name }})`;
                    activityList.prepend(li);
                }
            })
            .catch(error => {
                console.error('Error adding note:', error);
                toastr.error('Failed to add note. Please try again.');
            });
        }
    </script>
</body>
</html>
