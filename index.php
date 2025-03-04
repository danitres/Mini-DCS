<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Rubik+Mono+One&display=swap" rel="stylesheet">
    <script src="chart1.js"></script>
    <script src="chart2.js"></script>
    <script src="chart3.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Monitoring</title>
</head>
<body>
    <div class="container">
        <div class="topbar">
            <div class="logo">
                <h2>Monitoring</h2>
            </div>
            <div class="Logo2">
                <img class="fotologo" src="" alt="">
            </div>

            <i class="fas fa-user"></i> 
            <div class="user">
                <img src="img/Dani.png" alt="">
            </div>
        </div>
        <div class="sidebar">
            <ul>
                <li>
                    <a href="#">
                        <i class="fas fa-home"></i>
                        <div>Monitoring</div>
                    </a>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="#">
                        <i class="fas fa-user"></i>
                        <div>Profile</div>
                    </a>
                </li>
            </ul>
        </div>
        <div class="main">
            <div class="opening">
                <div class="logo-text">
                    <i class="fa fa-archive"></i>
                </div>
                <div class="text1">Monitoring Tape Ketan</div>
                <div class="text1">Dengan Kontrol PID</div>
            </div>
                <div class="cards">
                    <div class="card">
                        <div class="card-content">
                            <div class="number" id="temperature">N/a°C</div> <!-- ID untuk suhu -->
                            <div class="card-name">Temperature</div>
                        </div>
                        <div class="icon-box">
                            <i class="fa fa-thermometer-full"></i>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <div class="number" id="pwm">N/a</div> <!-- ID untuk PWM -->
                            <div class="card-name">PWM</div>
                            <script>
                                    function fetchData() {
                                        fetch('get_data.php') // Mengambil data dari server (PHP file yang sudah Anda buat sebelumnya)
                                            .then(response => response.json())
                                            .then(data => {
                                                // Memperbarui elemen HTML dengan data terbaru
                                                document.getElementById('temperature').textContent = data.suhu_data[0] + "°C"; // Menampilkan suhu
                                                document.getElementById('pwm').textContent = data.pwm_data[0] ? data.pwm_data[0] : "N/a"; // Menampilkan PWM
                                            })
                                            .catch(error => {
                                                console.error('Error fetching data:', error);
                                            });
                                    }

                                    // Memanggil fungsi fetchData setiap 5 detik untuk memperbarui data
                                    setInterval(fetchData, 1000);

                                    // Panggil fungsi fetchData pertama kali saat halaman dimuat
                                    fetchData();
                            </script>
                        </div>
                        <div class="icon-box">
                            <i class="fas fa-area-chart"></i>
                        </div>
                    </div>
                </div>
                <div class="cards2">
                    <div class="card2">
                        <div class="card-content2">
                            <div class="number2" id="status">On</div> <!-- ID untuk suhu -->
                            <div class="card-name2">On/Off</div>
                        </div>
                        <div class="icon-box">
                            <i class="fa fa-thermometer-full"></i>
                        </div>
                    </div>
                    <div class="card2">
                        <div class="card-content2">
                            <div class="number2" id="mode">Manual</div> <!-- ID untuk PWM -->
                            <div class="card-name2">Manual/Auto</div>
                        </div>
                        <div class="icon-box2">
                            <i class="fas fa-area-chart"></i>
                        </div>
                    </div>
                </div>
                <div class="cards3">
                    <div class="card3">
                        <div class="card-content3">
                            <label for="pwmInput">Set PWM:</label>
                            <input type="number" id="pwmInput" min="0" max="255" value="0">
                            <button onclick="setPWM()">Simpan</button>
                        </div>
                        <div class="card-content3">
                            <label for="setpointInput">Setpoint:</label>
                            <input type="number" id="setpointInput" min="35" max="40" value="0">
                        </div>
                        <div class="card-content3">
                            <label for="kpInput">Kp:</label>
                            <input type="number" id="kpInput" step="0.01" value="0">
                        </div>
                        <div class="card-content3">
                            <label for="kiInput">Ki:</label>
                            <input type="number" id="kiInput" step="0.0001" value="0">
                        </div>
                        <div class="card-content3">
                            <label for="kdInput">Kd:</label>
                            <input type="number" id="kdInput" step="0.1" value="0">
                        </div>

                        <!-- Satu tombol untuk menyimpan semua -->
                        <button onclick="setPID()">Simpan PID</button>
                    </div>
                </div>
            <div class="charts">
                <div class="chart">
                    <h2>Monitoring Grafik</h2>
                    <div class="card-name3">Menampilkan Suhu & PWM</div>
                    <canvas id="chart1"></canvas> 
                    <button id="resetChart">Reset Grafik</button>
                    <button id="saveChart">Simpan Grafik</button>              
                </div>
                <div class="chart">
                    <h2>PWM</h2>
                    <div class="card-name3">Nominal PWM</div>
                    <canvas id="chart2"></canvas>
                    <button id="resetChart2">Reset Grafik</button>
                    <button id="saveChart2">Simpan Grafik</button>
                    <div class="card-name3"  id="pwmValue" style="font-size: 20px;"></div> <!-- Grafik nominal PWM -->
                </div>
                <div class="chart">
                    <h2>PWM & Suhu</h2>
                    <div class="card-name3">Nominal PWM</div>
                    <canvas id="chart3"></canvas>
                    <button id="resetChart3">Reset Grafik</button>
                    <button id="saveChart3">Simpan Grafik</button>
                    <div class="card-name3"  id="pwmValue" style="font-size: 20px;"></div> <!-- Grafik nominal PWM -->
                </div>
            </div>
        </div>
    </div>
<script>

document.querySelectorAll('.cards2 .card2').forEach(card => {
    card.addEventListener('click', () => {
        card.classList.toggle('active');

        let numberText = card.querySelector('.number2');
        let newValue;

        if (numberText.id === "status") {
            newValue = numberText.textContent === "On" ? "Off" : "On";
            numberText.textContent = newValue;
            updateSystem({ status: newValue.toLowerCase() });
        } else if (numberText.id === "mode") {
            newValue = numberText.textContent === "Manual" ? "Auto" : "Manual";
            numberText.textContent = newValue;
            updateSystem({ mode: newValue.toLowerCase() });
        }
    });
});

function setPWM() {
    let pwmValue = document.getElementById("pwmInput").value;
    console.log("Mengirim PWM:", pwmValue); // Debugging
    updateSystem({ pwm: pwmValue });
}

// Tambahkan fungsi untuk mengirim Kp, Ki, Kd, dan Setpoint
function setPID() {
    let setpointValue = document.getElementById("setpointInput").value;
    let kpValue = document.getElementById("kpInput").value;
    let kiValue = document.getElementById("kiInput").value;
    let kdValue = document.getElementById("kdInput").value;

    console.log("Mengirim PID:", setpointValue, kpValue, kiValue, kdValue); // Debugging

    updateSystem({
        setpoint: setpointValue,
        kp: kpValue,
        ki: kiValue,
        kd: kdValue
    });
}

function updateSystem(data) {
    fetch("update_system.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams(data).toString()
    })
    .then(response => response.json())
    .then(result => {
        if (!result.success) {
            console.error("Gagal update:", result.error);
        }
    })
    .catch(error => console.error("Fetch error:", error));
}

function updateStatus() {
    fetch("get_mode.php")
        .then(response => response.json())
        .then(data => {
            if (data && data.success) {
                document.getElementById("status").innerText = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                document.getElementById("mode").innerText = data.mode.charAt(0).toUpperCase() + data.mode.slice(1);
                document.getElementById("pwm").innerText = data.pwm;
                document.getElementById("setpointInput").value = data.setpoint;
                document.getElementById("kpInput").value = data.kp;
                document.getElementById("kiInput").value = data.ki;
                document.getElementById("kdInput").value = data.kd;
            } else {
                console.error("Data tidak valid:", data);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            document.getElementById("status").innerText = "Error";
            document.getElementById("mode").innerText = "Error";
            document.getElementById("pwm").innerText = "Error";
        });
}

setInterval(updateStatus, 5000);
updateStatus();

</script>
</body>



</html>
