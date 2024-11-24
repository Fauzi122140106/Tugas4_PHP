<?php
// Mulai sesi PHP
session_start();

// Koneksi ke database
$host = 'localhost'; // Ganti dengan host database Anda
$dbname = 'fauzi'; // Nama database
$username = 'root'; // Username MySQL Anda
$password = ''; // Password MySQL Anda

try {
    // Membuat koneksi PDO ke database MySQL
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Menetapkan mode error ke exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

if (isset($_POST['submit'])) { 
    // Tangkap data dari formulir
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $age = $_POST['age'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $file = $_FILES['file'] ?? null;

    // Validasi server-side
    $errors = [];
    if (strlen($name) < 3) {
        $errors[] = "Nama harus memiliki minimal 3 karakter.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid.";
    }
    if (!is_numeric($age) || $age <= 0) {
        $errors[] = "Umur harus berupa angka positif.";
    }

    if ($file && $file['error'] === 0) {
        // Validasi ekstensi file menggunakan pathinfo()
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png']; // Daftar ekstensi gambar yang diperbolehkan
        if (!in_array($fileExt, $allowedExts)) {
            $errors[] = "File harus berformat gambar (.jpg, .jpeg, .png).";
        }
        // Validasi ukuran file
        if ($file['size'] > 2 * 1024 * 1024) { // 2MB max
            $errors[] = "Ukuran file tidak boleh lebih dari 2MB.";
        }
    } else {
        $errors[] = "File harus diunggah.";
    }

    if (empty($errors)) {
        // Simpan file jika tidak ada error
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
            die("Gagal membuat direktori unggahan.");
        }
        $filePath = $uploadDir . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Menyimpan data ke database setelah file berhasil diunggah
            $stmt = $pdo->prepare("INSERT INTO users (name, email, age, gender, file_path, browser_info) 
                                   VALUES (:name, :email, :age, :gender, :file_path, :browser_info)");

            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':age' => $age,
                ':gender' => $gender,
                ':file_path' => $filePath,
                ':browser_info' => $_SERVER['HTTP_USER_AGENT'] // Mengambil informasi browser
            ]);

            $_SESSION['message'] = "Form berhasil dikirim.";
            header("Location: index.php"); // Pengalihan ke index.php
            exit;
        } else {
            $errors[] = "Gagal mengunggah file.";
        }
    }

    // Simpan pesan error ke sesi
    $_SESSION['errors'] = $errors;
    header("Location: form.php"); // Pengalihan kembali ke form.php jika ada error
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/stylefrom.css">
    <title>Form Pendaftaran</title>
</head>
<body>
    <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
        <h2>Form Pendaftaran</h2>

        <!-- Pesan Error -->
        <?php if (isset($_SESSION['errors'])): ?>
            <div class="error">
                <ul>
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <!-- Pesan Sukses -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="success">
                <?= htmlspecialchars($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="form-group">
            <label for="name">Nama:</label>
            <input type="text" id="name" name="name">
            <div id="nameError" class="error"></div>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
            <div id="emailError" class="error"></div>
        </div>
        <div class="form-group">
            <label for="age">Umur:</label>
            <input type="number" id="age" name="age">
            <div id="ageError" class="error"></div>
        </div>
        <div class="form-group">
            <label for="gender">Jenis Kelamin:</label>
            <select id="gender" name="gender">
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
        </div>
        <div class="form-group">
            <label for="file">Unggah File Gambar (.jpg, .jpeg, .png):</label>
            <input type="file" id="file_path" name="file" accept=".jpg,.jpeg,.png">
            <div id="fileError" class="error"></div>
        </div>
        <button type="submit" name="submit">Kirim</button>
    </form>
</body>
</html>
