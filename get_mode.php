<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "monitoring";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Koneksi gagal: " . $conn->connect_error]);
    exit;
}

// Ambil data terbaru dari system_mode
$sql = "SELECT mode, status, pwm_manual, setpoint, kp, ki, kd FROM system_mode ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        "success" => true, 
        "mode" => $row["mode"], 
        "status" => $row["status"], 
        "pwm" => $row["pwm_manual"],
        "setpoint" => $row["setpoint"],
        "kp" => $row["kp"],
        "ki" => $row["ki"],
        "kd" => $row["kd"]
    ]);
} else {
    echo json_encode(["success" => false, "error" => "Data tidak ditemukan"]);
}

$conn->close();
?>
