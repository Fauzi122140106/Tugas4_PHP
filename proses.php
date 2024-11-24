<?php
// Koneksi ke database
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'fauzi';

$conn = new mysqli($host, $user, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Variabel yang diharapkan dari form atau file
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$age = $_POST['age'] ?? '';
$gender = $_POST['gender'] ?? '';
$filePath = 'uploads/' . ($_FILES['file']['name'] ?? '');

// Validasi input sebelum menyimpan
if (empty($name) || empty($email) || empty($age) || empty($gender) || empty($filePath)) {
    die("Semua data wajib diisi.");
}

// Simpan data ke database
$stmt = $conn->prepare("INSERT INTO users (name, email, age, gender, file_path, browser_info) VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    die("Kesalahan pada prepare statement: " . $conn->error);
}

$stmt->bind_param(
    "ssisss", 
    $name, 
    $email, 
    $age, 
    $gender, 
    $filePath, 
    $_SERVER['HTTP_USER_AGENT']
);

if ($stmt->execute()) {
    echo "Data berhasil disimpan ke database.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
