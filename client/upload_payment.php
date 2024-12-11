<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Mengambil data reservation_id dari user yang login
$user_id = $_SESSION['user_id'];
require_once '../server/database.php';
$database = new Database();
$pdo = $database->conn;

try {
    // Ambil data reservasi user
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $reservations = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Gagal mengambil data reservasi: " . $e->getMessage();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi form
    if (isset($_FILES['payment_receipt']) && $_FILES['payment_receipt']['error'] == 0) {
        // Ambil data dari form
        $reservation_id = $_POST['reservation_id'];
        $payment_method = $_POST['payment_method'];
        $payment_date = date('Y-m-d');

        // Nama file upload
        $payment_receipt = $_FILES['payment_receipt']['name'];
        $payment_receipt_tmp = $_FILES['payment_receipt']['tmp_name'];
        $payment_receipt_ext = strtolower(pathinfo($payment_receipt, PATHINFO_EXTENSION));

        // Daftar format gambar yang diperbolehkan
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

        // Validasi ekstensi file
        if (in_array($payment_receipt_ext, $allowed_extensions)) {
            // Tentukan folder untuk menyimpan gambar
            $upload_dir = 'uploads/payments/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true); // Membuat folder jika belum ada
            }

            // Pindahkan file ke folder upload
            $upload_path = $upload_dir . basename($payment_receipt);
            if (move_uploaded_file($payment_receipt_tmp, $upload_path)) {
                // Tambahkan pesan debug
                echo "File berhasil diupload ke: " . $upload_path . "<br>";

                try {
                    // Simpan data ke database
                    $stmt = $pdo->prepare("INSERT INTO payments (reservation_id, payment_date, bukti_payment, payment_method) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$reservation_id, $payment_date, $payment_receipt, $payment_method]);
                    // Menampilkan pop-up dan mengarahkan ke index_user.php setelah klik OK
                    echo "<script>
                            alert('Bukti pembayaran berhasil diupload!');
                            window.location.href = 'index_user.php';
                          </script>";
                } catch (PDOException $e) {
                    echo "Gagal menyimpan data pembayaran: " . $e->getMessage();
                }
            } else {
                echo "Gagal mengupload bukti pembayaran.";
            }
        } else {
            echo "Format file tidak diperbolehkan. Harap upload file dengan format: .jpg, .jpeg, .png, atau .webp.";
        }
    } else {
        echo "Harap pilih bukti pembayaran untuk diupload.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bukti Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }

        select,
        input[type="file"] {
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }

        .notification {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Upload Bukti Pembayaran</h1>

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($upload_error)) { ?>
            <div class="notification"><?= $upload_error ?></div>
        <?php } ?>

        <form action="upload_payment.php" method="POST" enctype="multipart/form-data">
            <label for="reservation_id">Reservation ID</label>
            <select name="reservation_id" required>
                <?php foreach ($reservations as $reservation): ?>
                    <option value="<?= $reservation['reservation_id']; ?>">
                        <?= "Room: " . $reservation['room_id'] . " - " . $reservation['start_date'] . " to " . $reservation['end_date']; ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <label for="payment_method">Metode Pembayaran</label>
            <select name="payment_method" required>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Credit Card">Credit Card</option>
                <option value="Cash">Cash</option>
                <option value="E-Wallet">E-Wallet</option>
            </select><br>

            <label for="payment_receipt">Upload Bukti Pembayaran</label>
            <input type="file" name="payment_receipt" required><br>

            <button type="submit">Upload Payment</button>
        </form>
    </div>

</body>

</html>