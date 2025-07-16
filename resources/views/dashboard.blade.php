<!DOCTYPE html>
<html>
<head>
    <title>CRM Dashboard</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body class="bg-gray-100 font-sans">
    @include('layouts.navigation')
    <div class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">CRM Dashboard</h1>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Lead Status Distribution</h2>
                <canvas id="statusChart" class="w-full h-64"></canvas>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Activity Trends (Last 7 Days)</h2>
                <canvas id="activityChart" class="w-full h-64"></canvas>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusChartCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusChartCtx, {
                type: 'pie',
                data: {
                    labels: @json(array_keys($statusCounts)),
                    datasets: [{
                        data: @json(array_values($statusCounts)),
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top', labels: { font: { size: 14 } } },
                        title: { display: true, text: 'Lead Status Distribution', font: { size: 16 } }
                    }
                }
            });

            const activityChartCtx = document.getElementById('activityChart').getContext('2d');
            new Chart(activityChartCtx, {
                type: 'bar',
                data: {
                    labels: @json(array_keys($activityTrends)),
                    datasets: [{
                        label: 'Activities',
                        data: @json(array_values($activityTrends)),
                        backgroundColor: '#36A2EB',
                        borderColor: '#2563EB',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Number of Activities' } },
                        x: { title: { display: true, text: 'Date' } }
                    },
                    plugins: {
                        legend: { display: false },
                        title: { display: true, text: 'Activity Trends', font: { size: 16 } }
                    }
                }
            });
        });
    </script>
</body>
</html>
