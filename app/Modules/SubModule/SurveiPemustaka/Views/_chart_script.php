<script>
    // Pengganti HighCharts INLISLite 3 memakai Chart.js 2.x.
    // Canvas membawa data-chart='{"title":..,"labels":[..],"data":[..]}'
    var surveyChartColors = ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1'];

    function surveyChart(canvas, type) {
        var cfg = JSON.parse(canvas.getAttribute('data-chart'));
        var colors = cfg.labels.map(function(_, i) {
            return surveyChartColors[i % surveyChartColors.length];
        });
        var total = cfg.data.reduce(function(a, b) {
            return a + b;
        }, 0);

        return new Chart(canvas, {
            type: type,
            data: {
                labels: cfg.labels,
                datasets: [{
                    label: 'Pilihan',
                    data: cfg.data,
                    backgroundColor: type === 'pie' ? colors : surveyChartColors[0]
                }]
            },
            options: {
                title: { display: true, text: cfg.title, fontSize: 16 },
                legend: { display: type === 'pie', position: 'bottom' },
                tooltips: {
                    callbacks: {
                        label: function(item, data) {
                            var value = data.datasets[0].data[item.index];
                            var label = data.labels[item.index];
                            if (type !== 'pie') return label + ': ' + value;
                            var pct = total ? (value * 100 / total).toFixed(1) : '0.0';
                            return label + ': ' + pct + '%';
                        }
                    }
                },
                scales: type === 'pie' ? {} : {
                    yAxes: [{
                        ticks: { beginAtZero: true, precision: 0 },
                        scaleLabel: { display: true, labelString: cfg.yLabel || '' }
                    }]
                }
            }
        });
    }
</script>
