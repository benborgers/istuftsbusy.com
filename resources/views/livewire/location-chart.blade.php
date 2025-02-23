<div class="relative h-48">
    <canvas id="chart-{{ $this->id() }}"></canvas>
</div>

@script
    <script>
        const color = '#0891b2'; // cyan-600
        const canvas = document.getElementById('chart-' + $wire.id);
        const data = @json($this->data);

        const chart = new Chart(canvas, {
            type: 'line',
            data: {
                labels: data.map(d => d.time),
                datasets: [
                    {
                        label: 'Today',
                        data: data.map(d => d.current_value), 
                        borderWidth: 3,
                        borderColor: color,
                        backgroundColor: color,
                        pointRadius: 0
                    },
                    {
                        label: 'Historical Average',
                        data: data.map(d => d.past_value),
                        borderWidth: 1,
                        borderColor: '#d4d4d4',
                        backgroundColor: '#d4d4d4',
                        pointRadius: 0
                    }
                ]
            },
            options: {
                animation: false,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: {
                            maxTicksLimit: 5,
                            align: 'start',
                            font: {
                                family: "'Clash Grotesk', sans-serif"
                            }
                        }
                    },
                    y: { display: false }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            }
        });

        // Update the chart when the fonts are loaded
        document.fonts.addEventListener('loadingdone', () => chart.update());
    </script>
@endscript

@assets
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endassets
