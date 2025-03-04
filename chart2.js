document.addEventListener('DOMContentLoaded', function () {
    const ctx2 = document.getElementById('chart2').getContext('2d');

    let labels = JSON.parse(localStorage.getItem('labels2')) || [];
    let pwmData = JSON.parse(localStorage.getItem('pwmData2')) || [];

    const chart2 = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'PWM (0-255)',
                data: pwmData,
                borderColor: 'rgba(255, 159, 64, 1)',
                fill: false
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
                        text: 'Nilai PWM'
                    },
                    min: 0,
                    max: 255
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
                    pwmData.push(data.pwm_data[data.pwm_data.length - 1] || null);

                    // Simpan data di localStorage agar tetap ada setelah refresh
                    localStorage.setItem('labels2', JSON.stringify(labels));
                    localStorage.setItem('pwmData2', JSON.stringify(pwmData));
                }

                chart2.data.labels = labels;
                chart2.data.datasets[0].data = pwmData;
                chart2.update();
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    setInterval(updateChart, 5000);

    document.getElementById('saveChart2').addEventListener('click', function () {
        const link = document.createElement('a');
        link.href = document.getElementById('chart2').toDataURL('image/png');
        link.download = `grafik_pwm_${new Date().toISOString().split('T')[0]}.png`;
        link.click();
    });

    document.getElementById('resetChart2').addEventListener('click', function () {
        console.log("Tombol Reset Ditekan!");

        labels.length = 0;
        pwmData.length = 0;

        localStorage.removeItem('labels2');
        localStorage.removeItem('pwmData2');

        chart2.data.labels = [];
        chart2.data.datasets[0].data = [];
        chart2.update();
    });
});
