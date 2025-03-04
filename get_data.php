<?php
header('Content-Type: application/json');

// Set zona waktu Indonesia (WIB)
date_default_timezone_set('Asia/Jakarta');

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "monitoring";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Pastikan MySQL pakai zona waktu Indonesia
$conn->query("SET time_zone = '+07:00'");

// Ambil data suhu dan PWM dengan waktu yang sudah dikonversi ke WIB
$sql = "SELECT 
            DATE_FORMAT(timestamp, '%H:%i:%s') AS waktu_lokal, 
            temperature, 
            pwm 
        FROM tape_ketan 
        ORDER BY timestamp DESC 
        LIMIT 10";

$result = $conn->query($sql);

$suhu_data = [];
$pwm_data = [];
$labels = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['waktu_lokal']; // Waktu sudah dalam WIB
        $suhu_data[] = $row['temperature']; // Data suhu
        $pwm_data[] = $row['pwm'];  // Data PWM
    }
}

// Tutup koneksi
$conn->close();

// Kirim data JSON
echo json_encode([
    'labels' => $labels ?: [],
    'suhu_data' => $suhu_data ?: [],
    'pwm_data' => $pwm_data ?: []
]);
?>
