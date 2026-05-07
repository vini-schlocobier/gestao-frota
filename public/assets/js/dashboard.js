const costsChartElement = document.getElementById('costsChart');

if (costsChartElement && window.Chart) {
    const labels = JSON.parse(costsChartElement.dataset.labels || '[]');
    const data = JSON.parse(costsChartElement.dataset.values || '[]');

    new Chart(costsChartElement.getContext('2d'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Custo Total (R$)',
                data,
                backgroundColor: 'rgba(37, 99, 235, 0.6)',
                borderColor: 'rgb(37, 99, 235)',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
}
