<?php
// Pastikan file database.php sudah di-include untuk koneksi dan penggunaan metode
include "../server/database.php";
$abc = new Database();

// Menangani request POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $room_name = $_POST['room_name'];
    $price_per_night = $_POST['price_per_night'];
    $descriptions = $_POST['descriptions'];

    // Periksa apakah file gambar diupload
    if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
        $room_image = $_FILES['room_image'];

        // Menyimpan file gambar ke folder 'uploads/rooms'
        $upload_dir = "uploads/rooms/";
        $upload_file = $upload_dir . basename($room_image["name"]);

        // Pindahkan file gambar ke folder
        if (move_uploaded_file($room_image["tmp_name"], $upload_file)) {
            // Jika berhasil, simpan hanya nama file di database
            $room_image_name = basename($room_image["name"]);
        } else {
            $room_image_name = null;  // Jika gagal meng-upload gambar, set null
        }
    } else {
        // Jika tidak ada gambar, set null
        $room_image_name = null;
    }

    // Membuat array data untuk dikirim ke metode tambah_data_rooms
    $data = [
        'room_name' => $room_name,
        'price_per_night' => $price_per_night,
        'descriptions' => $descriptions,
        'room_image' => $room_image_name  // Simpan hanya nama file
    ];

    // Panggil metode tambah_data_rooms
    $abc->tambah_data_rooms($data);

    // Redirect atau memberi response sukses
    echo "<script>
            alert('Room added successfully!');
            window.location.href = 'index_admin.php'; // Redirect ke halaman admin atau daftar rooms
          </script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Room</title>

    <!-- Styling untuk form -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
        }

        h2 {
            color: #333;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 0 auto;
        }

        .form-container input,
        .form-container textarea,
        .form-container button {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .form-container input[type="file"] {
            padding: 10px;
        }

        .form-container button {
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #45a049;
        }

        .form-container label {
            font-weight: bold;
        }

        /* Style for the alert box */
        .alert {
            display: none;
            padding: 20px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            text-align: center;
            border-radius: 5px;
        }

        .alert.show {
            display: block;
        }
    </style>
</head>

<body>

    <h2>Tambah Rooms Baru</h2>

    <!-- Form untuk menambah kamar -->
    <div class="form-container">
        <form action="tambah_rooms.php" method="POST" enctype="multipart/form-data">
            <label for="room_name">Room Name:</label>
            <input type="text" id="room_name" name="room_name" required><br><br>

            <label for="price_per_night">Price Per Night:</label>
            <input type="number" id="price_per_night" name="price_per_night" required><br><br>

            <label for="descriptions">Description:</label>
            <textarea id="descriptions" name="descriptions" required></textarea><br><br>

            <label for="room_image">Room Image:</label>
            <input type="file" id="room_image" name="room_image" required><br><br>

            <button type="submit">Add Room</button>
        </form>
    </div>

    <!-- Alert box -->
    <div id="alertBox" class="alert">
        Room added successfully!
    </div>

</body>

</html>