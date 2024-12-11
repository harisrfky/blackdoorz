<?php
// Include database connection
include "../server/database.php";

$db = new Database();

// Periksa apakah parameter `room_id` ada
if (!isset($_GET['room_id'])) {
    die("Room ID not provided.");
}

$room_id = $_GET['room_id'];

// Ambil data room berdasarkan `room_id`
$stmt = $db->conn->prepare("SELECT * FROM rooms WHERE room_id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika data tidak ditemukan
if (!$room) {
    die("Room not found.");
}

// Proses update data ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_name = $_POST['room_name'];
    $price_per_night = $_POST['price_per_night'];
    $descriptions = $_POST['descriptions'];

    // Handle file upload
    if (!empty($_FILES['room_image']['name'])) {
        $target_dir = "uploads/rooms/";
        $file_name = basename($_FILES['room_image']['name']);
        $target_file = $target_dir . $file_name;

        // Upload file
        if (move_uploaded_file($_FILES['room_image']['tmp_name'], $target_file)) {
            $stmt = $db->conn->prepare("UPDATE rooms SET room_name = ?, price_per_night = ?, descriptions = ?, room_image = ? WHERE room_id = ?");
            $stmt->execute([$room_name, $price_per_night, $descriptions, $file_name, $room_id]);
        } else {
            echo "Failed to upload image.";
            exit;
        }
    } else {
        // Update tanpa gambar
        $stmt = $db->conn->prepare("UPDATE rooms SET room_name = ?, price_per_night = ?, descriptions = ? WHERE room_id = ?");
        $stmt->execute([$room_name, $price_per_night, $descriptions, $room_id]);
    }

    // Redirect kembali ke halaman rooms dengan parameter sukses
    header("Location: index_admin.php?page=rooms&update=success&room_id=" . $room_id);


    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container my-5">
        <h1>Edit Room</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="room_name" class="form-label">Room Name</label>
                <input type="text" class="form-control" id="room_name" name="room_name"
                    value="<?= htmlspecialchars($room['room_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="price_per_night" class="form-label">Price Per Night</label>
                <input type="number" class="form-control" id="price_per_night" name="price_per_night"
                    value="<?= htmlspecialchars($room['price_per_night']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="descriptions" class="form-label">Descriptions</label>
                <textarea class="form-control" id="descriptions" name="descriptions" rows="3"
                    required><?= htmlspecialchars($room['descriptions']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="room_image" class="form-label">Room Image</label>
                <input type="file" class="form-control" id="room_image" name="room_image">
                <p>Current Image:
                    <?= $room['room_image'] ? "<img src='uploads/rooms/{$room['room_image']}' alt='Room Image' width='100'>" : "No image uploaded" ?>
                </p>
            </div>
            <button type="submit" class="btn btn-primary">Update Room</button>
            <a href="index_admin.php?page=rooms" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script>
        // Ambil parameter dari URL
        const urlParams = new URLSearchParams(window.location.search);
        const updateStatus = urlParams.get('update');
        const roomId = urlParams.get('room_id');  // Mengambil room_id dari URL

        // Jika parameter `update` adalah 'success', tampilkan popup
        if (updateStatus === 'success') {
            alert("Room data successfully updated!");

            // Hapus parameter dari URL setelah menampilkan popup
            // Menggunakan replaceState untuk menghilangkan parameter 'update' dan 'room_id' dari URL
            window.history.replaceState({}, document.title, window.location.pathname + (roomId ? `?room_id=${roomId}` : ''));
        }
    </script>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>