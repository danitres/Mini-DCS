document.addEventListener('DOMContentLoaded', function () {
    const ctx3 = document.getElementById('chart3').getContext('2d');

    let labels = JSON.parse(localStorage.getItem('labels3')) || [];
    let suhuData = JSON.parse(localStorage.getItem('suhuData3')) || [];
    let pwmData = JSON.parse(localStorage.getItem('pwmData3')) || [];

    const chart3 = new Chart(ctx3, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Suhu (°C)',
                data: suhuData,
                borderColor: 'rgba(75, 192, 192, 1)',
                fill: false
            }, {
                label: 'PWM (0-255)',
                data: pwmData,
                borderColor: 'rgba(255, 159, 64, 1)',
                fill: false,
                borderDash: [5, 5] // Garis putus-putus untuk membedakan
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
                    min: 0,
                    max: 100 // Bisa disesuaikan jika perlu
                }
            }
        }
    });

    function getCurrentTimeWIB() {
        let now = new Date();
        let utc = now.getTime() + now.getTimezoneOffset() * 60000; // Konversi ke UTC
        let wibTime = new Date(utc + (7 * 3600000)); // Tambah 7 jam untuk WIB
        return wibTime.toLocaleTimeString('id-ID', { hour12: false });
    }

    function updateChart() {
        fetch('get_data.php')
            .then(response => response.json())
            .then(data => {
                let currentTime = getCurrentTimeWIB();

                if (!labels.includes(currentTime)) {
                    labels.push(currentTime);
                    suhuData.push(data.suhu_data[data.suhu_data.length - 1] || null);
                    pwmData.push(data.pwm_data[data.pwm_data.length - 1] || null);

                    // Simpan data di localStorage agar tidak hilang setelah refresh
                    localStorage.setItem('labels3', JSON.stringify(labels));
                    localStorage.setItem('suhuData3', JSON.stringify(suhuData));
                    localStorage.setItem('pwmData3', JSON.stringify(pwmData));
                }

                chart3.data.labels = labels;
                chart3.data.datasets[0].data = suhuData;
                chart3.data.datasets[1].data = pwmData;
                chart3.update();
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    setInterval(updateChart, 5000);

    document.getElementById('saveChart3').addEventListener('click', function () {
        const link = document.createElement('a');
        link.href = document.getElementById('chart3').toDataURL('image/png');
        link.download = `grafik_suhu_pwm_${new Date().toISOString().split('T')[0]}.png`;
        link.click();
    });

    document.getElementById('resetChart3').addEventListener('click', function () {
        console.log("Tombol Reset Ditekan!");

        labels.length = 0;
        suhuData.length = 0;
        pwmData.length = 0;

        localStorage.removeItem('labels3');
        localStorage.removeItem('suhuData3');
        localStorage.removeItem('pwmData3');

        chart3.data.labels = [];
        chart3.data.datasets[0].data = [];
        chart3.data.datasets[1].data = [];
        chart3.update();
    });
});
