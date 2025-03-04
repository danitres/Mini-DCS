<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "monitoring";

// Membuat koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Debug: Cetak semua data POST yang diterima
echo "Received POST Data: ";
print_r($_POST);
echo "<br>";

// Pastikan semua data POST tersedia
if (isset($_POST['temperature']) && isset($_POST['pwm']) &&
    isset($_POST['P']) && isset($_POST['I']) && isset($_POST['D']) && isset($_POST['Output'])) {
    
    $temperature = $_POST['temperature'];
    $pwm = $_POST['pwm'];
    $P = $_POST['P'];
    $I = $_POST['I'];
    $D = $_POST['D'];
    $Output = $_POST['Output'];

    // Insert ke tabel tape_ketan
    $stmt1 = $conn->prepare("INSERT INTO tape_ketan (pwm, temperature, timestamp) VALUES (?, ?, NOW())");
    $stmt1->bind_param("id", $pwm, $temperature);
    
    if ($stmt1->execute()) {
        // Dapatkan ID terakhir yang dimasukkan
        $tape_ketan_id = $stmt1->insert_id;
        
        // Insert ke tabel pid_values
        $stmt2 = $conn->prepare("INSERT INTO pid_values (tape_ketan_id, proportional, integral, derivative, output, timestamp) 
                                 VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt2->bind_param("idddd", $tape_ketan_id, $P, $I, $D, $Output);

        if ($stmt2->execute()) {
            echo "Data inserted successfully";
        } else {
            echo "Error: " . $stmt2->error;
        }

        $stmt2->close();
    } else {
        echo "Error: " . $stmt1->error;
    }

    $stmt1->close();
} else {
    echo "Missing data";
}

// Menutup koneksi
$conn->close();
?>
