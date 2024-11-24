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

// Mengambil data dari tabel users
$query = "SELECT * FROM users";
$stmt = $pdo->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
$query = "SELECT * FROM users ORDER BY id ASC";  // Atau bisa menggunakan created_at untuk urutan berdasarkan waktu

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/styleresult.css">
    <title>Data Pengguna</title>
    
</head> 
<body>
    <h2>Data Pengguna</h2>

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

    <!-- Tabel untuk menampilkan data pengguna -->
    <div class="content-wrapper">
    <div class="table-container">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Umur</th>
                        <th>Jenis Kelamin</th>
                        <th>File</th>
                        <th>Browser Info</th>
                        <th>Waktu Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id']) ?></td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['age']) ?></td>
                                <td><?= htmlspecialchars($user['gender']) ?></td>
                                <td><a href="<?= htmlspecialchars($user['file_path']) ?>" target="_blank">Lihat File</a></td>
                                <td><?= htmlspecialchars($user['browser_info']) ?></td>
                                <td><?= htmlspecialchars($user['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">Tidak ada data pengguna untuk ditampilkan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="button-container">
        <a href="form.php"><button>Tambahkan Pengguna</button></a>
    </div>
</div>
</body>
</html>
