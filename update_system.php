<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "monitoring";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Koneksi gagal: " . $conn->connect_error]);
    exit;
}

// Tangkap data dari POST
$status = isset($_POST['status']) ? $_POST['status'] : null;
$mode = isset($_POST['mode']) ? $_POST['mode'] : null;
$pwm = isset($_POST['pwm']) ? intval($_POST['pwm']) : null;
$setpoint = isset($_POST['setpoint']) ? floatval($_POST['setpoint']) : null;
$kp = isset($_POST['kp']) ? floatval($_POST['kp']) : null;
$ki = isset($_POST['ki']) ? floatval($_POST['ki']) : null;
$kd = isset($_POST['kd']) ? floatval($_POST['kd']) : null;

// Debug: Log data yang diterima
file_put_contents("log.txt", date("Y-m-d H:i:s") . " - STATUS: $status, MODE: $mode, PWM: $pwm, SP: $setpoint, Kp: $kp, Ki: $ki, Kd: $kd\n", FILE_APPEND);

// Cek apakah ada data dalam tabel
$result = $conn->query("SELECT id FROM system_mode ORDER BY id DESC LIMIT 1");
$row = $result->fetch_assoc();

if ($row) {
    // Jika data ada, lakukan UPDATE
    $id = $row['id'];

    $sql = "UPDATE system_mode SET ";
    $update_fields = [];

    if (!empty($status)) $update_fields[] = "status = '$status'";
    if (!empty($mode)) $update_fields[] = "mode = '$mode'";
    if ($pwm !== null) $update_fields[] = "pwm_manual = $pwm";
    if ($setpoint !== null) $update_fields[] = "setpoint = $setpoint";
    if ($kp !== null) $update_fields[] = "kp = $kp";
    if ($ki !== null) $update_fields[] = "ki = $ki";
    if ($kd !== null) $update_fields[] = "kd = $kd";

    if (!empty($update_fields)) {
        $sql .= implode(", ", $update_fields);
        $sql .= " WHERE id = $id";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Gagal update: " . $conn->error]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Tidak ada data yang diperbarui"]);
    }
} else {
    // Jika tabel kosong, lakukan INSERT pertama kali
    $sql_insert = "INSERT INTO system_mode (status, mode, pwm_manual, setpoint, kp, ki, kd) 
                   VALUES ('off', 'manual', 0, 40, 8.81, 0.03596, 539.63)";
    
    if ($conn->query($sql_insert) === TRUE) {
        echo json_encode(["success" => true, "message" => "Data awal dibuat"]);
    } else {
        echo json_encode(["success" => false, "error" => "Gagal membuat data awal: " . $conn->error]);
    }
}

// Tutup koneksi
$conn->close();
?>
