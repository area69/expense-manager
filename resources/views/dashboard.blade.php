@extends('layouts.app')

@section('content')
    <div class="bg-white text-gray-800 rounded-xl shadow p-4 flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-xl font-bold">Welcome back, {{ Auth::user()->name }}!</h1>
            <p class="text-sm text-gray-800">{{ now()->format('l, F j, Y') }}</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow text-gray-800 dark:text-white">
        <h2 class="text-2xl font-bold mb-4"><b>Dashboard Overview</b></h2>
            
        <div class="bg-white bg-gray-800 p-4 rounded-xl shadow-md text-white" style="width: auto; height: 500px;"> 
             <canvas id="realExpensePieChart" class="w-full max-w-lg mx-auto"></canvas>
        </div>
    </div>
    @push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartLabels = {!! json_encode($chartData->pluck('category')) !!};
    const chartValues = {!! json_encode($chartData->pluck('total')) !!};
    const total = chartValues.reduce((sum, val) => sum + parseFloat(val), 0);

    new Chart(document.getElementById('realExpensePieChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: chartLabels,
            datasets: [{
                data: chartValues,
                backgroundColor: [
                    '#4f46e5', '#10b981', '#f59e0b', '#ef4444',
                    '#3b82f6', '#ec4899', '#8b5cf6', '#22c55e'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed;
                            const percent = ((value / total) * 100).toFixed(1);
                            return `${context.label}: ₱${parseFloat(value).toLocaleString()} (${percent}%)`;
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#9ca3af',
                        generateLabels: function(chart) {
                            const data = chart.data;
                            const dataset = data.datasets[0];
                            const total = dataset.data.reduce((sum, val) => sum + parseFloat(val), 0);

                            return data.labels.map((label, i) => {
                                const value = dataset.data[i];
                                const percentage = ((value / total) * 100).toFixed(1);

                                return {
                                    text: `${label}: ${value} (${percentage}%)`,
                                    fillStyle: dataset.backgroundColor[i],
                                    strokeStyle: dataset.backgroundColor[i],
                                    lineWidth: 1,
                                    hidden: chart.getDatasetMeta(0).data[i]?.hidden,
                                    index: i
                                };
                            });
                        }
                    }
                }
            }
        }
    });
</script>
@endpush


@endsection