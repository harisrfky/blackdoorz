<?php
session_start(); // Memulai session untuk mengakses data session

// Cek apakah user sudah login, jika belum arahkan ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Pastikan 'username' ada di session
if (!isset($_SESSION['username'])) {
    echo "Error: Username not found in session.";
    exit;
}

// Ambil data dari session
$username = $_SESSION['username']; // Username yang login

// Ambil parameter dari URL
$room_id = $_GET['room_id'];
$room_name = urldecode($_GET['room_name']);
$price_per_night = $_GET['price_per_night'];

// Menyertakan file koneksi database
require_once '../server/database.php'; // Pastikan file database.php ada dan berada di folder yang benar

// Membuat objek Database
$database = new Database();
$pdo = $database->conn; // Mengakses koneksi PDO


// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Hitung total harga berdasarkan jumlah hari
    $start_date_timestamp = strtotime($start_date);
    $end_date_timestamp = strtotime($end_date);
    $date_diff = ($end_date_timestamp - $start_date_timestamp) / (60 * 60 * 24); // Hitung selisih hari
    $total = $date_diff * $price_per_night;

    try {
        // Insert data reservasi ke database
        $stmt = $pdo->prepare("INSERT INTO reservations (user_id, room_id, start_date, end_date, total) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $room_id, $start_date, $end_date, $total]);

        // Menampilkan pop-up notifikasi dengan JavaScript
        echo "<script>
                alert('Reservasi berhasil! Total harga: Rp " . number_format($total, 2) . "');
                window.location.href = 'index_user.php'; // Arahkan ke halaman index_user.php setelah OK
              </script>";
    } catch (PDOException $e) {
        echo "Gagal menyimpan data: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Sewa Kamar</title>
    <style>
        /* Basic reset and layout */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #333;
        }

        input[type="text"],
        input[type="date"] {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="text"]:readonly {
            background-color: #f0f0f0;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }
    </style>
    <script>
        // Fungsi untuk menghitung total harga
        function calculateTotal() {
            const pricePerNight = parseFloat(document.getElementById('price_per_night').value);
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);

            // Hitung jumlah hari
            const timeDiff = endDate - startDate;
            const days = timeDiff / (1000 * 3600 * 24);

            // Jika tanggal valid dan end date setelah start date
            if (days > 0) {
                const totalPrice = pricePerNight * days;
                document.getElementById('total').value = totalPrice.toFixed();
            } else {
                document.getElementById('total').value = '0.00';
            }
        }
    </script>
</head>

<body>

    <div class="container">
        <h1>Form Sewa Kamar</h1>

        <form method="POST">
            <div class="form-group">
                <label for="room_name">Nama Ruangan</label>
                <input type="text" id="room_name" name="room_name" value="<?= htmlspecialchars($room_name) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="username">Nama Penyewa</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" required onchange="calculateTotal()">
            </div>
            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" id="end_date" name="end_date" required onchange="calculateTotal()">
            </div>
            <div class="form-group">
                <label for="price_per_night">Harga Per Malam</label>
                <input type="text" id="price_per_night" value="<?= $price_per_night ?>" readonly>
            </div>
            <div class="form-group">
                <label for="total">Total Harga (Rp)</label>
                <input type="text" id="total" name="total" readonly>
            </div>
            <button type="submit">Konfirmasi Sewa</button>
        </form>
    </div>

</body>

</html>