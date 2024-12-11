<?php
// Include database connection
include "../server/database.php";

$db = new Database();

// Check database connection
if (!$db->conn) {
    die("Failed to connect to the database.");
}

// Handle actions (e.g., Add, Edit, Delete)
if (isset($_GET['aksi'])) {
    $action = $_GET['aksi'];

    switch ($action) {
        case 'tambah_room':
            // Process adding a room
            if (isset($_POST['room_name'], $_POST['price_per_night'], $_POST['descriptions'])) {
                $room_name = $_POST['room_name'];
                $price_per_night = $_POST['price_per_night'];
                $descriptions = $_POST['descriptions'];

                // Process image upload
                if ($_FILES['room_image']['error'] == 0) {
                    $upload_dir = 'uploads/rooms/';
                    $file_name = basename($_FILES['room_image']['name']);
                    $file_path = $upload_dir . $file_name;
                    move_uploaded_file($_FILES['room_image']['tmp_name'], $file_path);
                } else {
                    $file_name = null;
                }

                // Insert room data
                $stmt = $db->conn->prepare("INSERT INTO rooms (room_name, price_per_night, descriptions, room_image) 
                                            VALUES (:room_name, :price_per_night, :descriptions, :room_image)");
                $stmt->bindParam(':room_name', $room_name);
                $stmt->bindParam(':price_per_night', $price_per_night);
                $stmt->bindParam(':descriptions', $descriptions);
                $stmt->bindParam(':room_image', $file_name);
                $stmt->execute();

                header('Location: index_admin.php?page=rooms');
            }
            break;

        case 'edit_room':
            // Process editing a room
            if (isset($_GET['room_id'])) {
                $room_id = $_GET['room_id'];

                if (isset($_POST['room_name'], $_POST['price_per_night'], $_POST['descriptions'])) {
                    $room_name = $_POST['room_name'];
                    $price_per_night = $_POST['price_per_night'];
                    $descriptions = $_POST['descriptions'];

                    // Check if image is uploaded
                    if ($_FILES['room_image']['error'] == 0) {
                        $upload_dir = 'uploads/rooms/';
                        $file_name = basename($_FILES['room_image']['name']);
                        $file_path = $upload_dir . $file_name;
                        move_uploaded_file($_FILES['room_image']['tmp_name'], $file_path);
                    } else {
                        $file_name = $_POST['current_image']; // Keep the current image if no new one is uploaded
                    }

                    // Update room data
                    $stmt = $db->conn->prepare("UPDATE rooms SET room_name = :room_name, price_per_night = :price_per_night, 
                                                descriptions = :descriptions, room_image = :room_image WHERE room_id = :room_id");
                    $stmt->bindParam(':room_name', $room_name);
                    $stmt->bindParam(':price_per_night', $price_per_night);
                    $stmt->bindParam(':descriptions', $descriptions);
                    $stmt->bindParam(':room_image', $file_name);
                    $stmt->bindParam(':room_id', $room_id);
                    $stmt->execute();

                    header('Location: index_admin.php?page=rooms');
                }
            }
            break;

        case 'hapus_room':
            // Process deleting a room
            if (isset($_GET['room_id'])) {
                $room_id = $_GET['room_id'];

                // First, delete the image from the folder if it exists
                $stmt = $db->conn->prepare("SELECT room_image FROM rooms WHERE room_id = :room_id");
                $stmt->bindParam(':room_id', $room_id);
                $stmt->execute();
                $room = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($room && $room['room_image']) {
                    $image_path = 'uploads/rooms/' . $room['room_image'];
                    if (file_exists($image_path)) {
                        unlink($image_path);  // Delete the image file
                    }
                }

                // Delete the room record from the database
                $stmt = $db->conn->prepare("DELETE FROM rooms WHERE room_id = :room_id");
                $stmt->bindParam(':room_id', $room_id);
                $stmt->execute();

                header('Location: index_admin.php?page=rooms');
            }
            break;

        case 'check_in':
            if (isset($_GET['reservation_id'])) {
                $reservation_id = $_GET['reservation_id'];

                // Cek apakah sudah ada entri recap untuk reservasi ini
                $stmt_check_recap = $db->conn->prepare("SELECT * FROM recaps WHERE reservation_id = :reservation_id");
                $stmt_check_recap->bindParam(':reservation_id', $reservation_id);
                $stmt_check_recap->execute();

                if ($stmt_check_recap->rowCount() == 0) {
                    // Jika tidak ada, insert baru dengan status 'Checked In' dan tanggal check-in
                    $stmt_insert_recap = $db->conn->prepare("INSERT INTO recaps (reservation_id, status, check_in_date) 
                                                                 VALUES (:reservation_id, 'in', NOW())");
                    $stmt_insert_recap->bindParam(':reservation_id', $reservation_id);
                    $stmt_insert_recap->execute();
                } else {
                    // Jika sudah ada, update status dan tanggal check-in
                    $stmt_update_recap = $db->conn->prepare("UPDATE recaps SET status = 'in', check_in_date = NOW() 
                                                                 WHERE reservation_id = :reservation_id");
                    $stmt_update_recap->bindParam(':reservation_id', $reservation_id);
                    $stmt_update_recap->execute();
                }

                // Redirect setelah sukses
                header('Location: index_admin.php?page=recap');
                exit(); // Penting untuk menghentikan eksekusi setelah redirect
            } else {
                echo "No reservation ID provided.";
            }
            break;

        // Process check-out action
        case 'check_out':
            if (isset($_GET['reservation_id'])) {
                $reservation_id = $_GET['reservation_id'];

                // Update status di tabel recaps ke 'Checked Out' dan set check-out date
                $stmt_update_recap = $db->conn->prepare("UPDATE recaps SET status = 'out', check_out_date = NOW() 
                                                         WHERE reservation_id = :reservation_id");
                $stmt_update_recap->bindParam(':reservation_id', $reservation_id);
                $stmt_update_recap->execute();

                // Redirect setelah sukses
                header('Location: index_admin.php?page=recap');
            }
            break;



        default:
            // If no action matches, just show the home page or handle other actions
            header('Location: index_admin.php');
            break;
    }
}
?>