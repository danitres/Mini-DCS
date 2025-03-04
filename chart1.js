document.addEventListener('DOMContentLoaded', function () {
    const ctx1 = document.getElementById('chart1').getContext('2d');

    let labels = JSON.parse(localStorage.getItem('labels')) || [];
    let suhuData = JSON.parse(localStorage.getItem('suhuData')) || [];

    const chart1 = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Suhu (°C)',
                data: suhuData,
                borderColor: 'rgba(75, 192, 192, 1)',
                fill: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            animation: false,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Waktu'
                    },
                    ticks: {
                        autoSkip: true,
                        maxTicksLimit: 15
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Nilai'
                    },
                    min: 20,
                    max: 50
                }
            }
        }
    });

    function updateChart() {
        fetch('get_data.php')
            .then(response => response.json())
            .then(data => {
                let currentTime = new Date().toLocaleTimeString('id-ID', { hour12: false }); // Waktu sesuai zona lokal

                if (!labels.includes(currentTime)) {
                    labels.push(currentTime);
                    suhuData.push(data.suhu_data[data.suhu_data.length - 1] || null);

                    // Simpan data di localStorage agar tetap ada setelah refresh
                    localStorage.setItem('labels', JSON.stringify(labels));
                    localStorage.setItem('suhuData', JSON.stringify(suhuData));
                }

                chart1.data.labels = labels;
                chart1.data.datasets[0].data = suhuData;
                chart1.update();
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    setInterval(updateChart, 5000);

    document.getElementById('saveChart').addEventListener('click', function() {
        const link = document.createElement('a');
        link.href = document.getElementById('chart1').toDataURL('image/png');
        link.download = `grafik_suhu_${new Date().toISOString().split('T')[0]}.png`;
        link.click();
    });

    document.getElementById('resetChart').addEventListener('click', function () {
        console.log("Tombol Reset Ditekan!"); // Debugging

        labels.length = 0;
        suhuData.length = 0;

        localStorage.removeItem('labels');
        localStorage.removeItem('suhuData');

        chart1.data.labels = [];
        chart1.data.datasets[0].data = [];
        chart1.update();
    });
});
