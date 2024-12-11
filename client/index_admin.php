<?php
// Include database connection
include "../server/database.php";

$db = new Database();

// Check database connection
if (!$db->conn) {
    die("Failed to connect to the database.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Data BlackDoorz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .navbar {
            margin-bottom: 20px;
        }

        .logout-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: #e60000;
        }

        table {
            background-color: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
        }

        table thead {
            background-color: #007bff;
            color: white;
        }

        table tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }

        table tbody tr:hover {
            background-color: #e9ecef;
        }

        h1,
        h2 {
            margin-bottom: 20px;
        }

        .btn-primary,
        .btn-warning,
        .btn-danger {
            border-radius: 5px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-warning:hover {
            background-color: #ffcc00;
        }

        .btn-danger:hover {
            background-color: #cc0000;
        }

        .table-wrapper {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 10px;
            background-color: white;
        }
    </style>
</head>

<body>
    <div class="container my-4">
        <h1 class="text-center">Manajemen Data BlackDoorz</h1>

        <button class="logout-btn" onclick="confirmLogout()">Logout</button>

        <nav class="navbar navbar-expand-lg navbar-light bg-light my-3">
            <div class="container-fluid">
                <a class="navbar-brand" href="index_admin.php">Dashboard</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="?page=users">Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="?page=rooms">Rooms</a></li>
                        <li class="nav-item"><a class="nav-link" href="?page=reservations">Reservations</a></li>
                        <li class="nav-item"><a class="nav-link" href="?page=payments">Payments</a></li>
                        <li class="nav-item"><a class="nav-link" href="?page=recap">Recap</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="table-wrapper">
            <?php
            // Determine which page to show
            $page = isset($_GET['page']) ? $_GET['page'] : 'home';

            switch ($page) {
                case 'users':
                    echo "<h2>Users</h2>";
                    $stmt = $db->conn->query("SELECT * FROM users");
                    echo "<table class='table table-hover table-bordered'>
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tbody>";
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                                <td>{$row['user_id']}</td>
                                <td>{$row['username']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['role']}</td>
                            </tr>";
                    }
                    echo "</tbody></table>";
                    break;

                case 'rooms':
                    echo "<h2>Rooms</h2>";
                    echo "<a href='tambah_rooms.php' class='btn btn-primary mb-3'>Tambah Room</a>";
                    $stmt = $db->conn->query("SELECT * FROM rooms");
                    echo "<table class='table table-hover table-bordered'>
                                <thead>
                                    <tr>
                                        <th>Room ID</th>
                                        <th>Room Name</th>
                                        <th>Price</th>
                                        <th>Room Image</th>
                                        <th>Descriptions</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>";
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $room_image = !empty($row['room_image'])
                            ? "<img src='uploads/rooms/{$row['room_image']}' alt='Room Image' width='100'>"
                            : "No Image";
                        echo "<tr>
                                    <td>{$row['room_id']}</td>
                                    <td>{$row['room_name']}</td>
                                    <td>{$row['price_per_night']}</td>
                                    <td>$room_image</td>
                                    <td>{$row['descriptions']}</td>
            <td>
                <a href='edit_rooms.php?room_id={$row['room_id']}' class='btn btn-warning btn-sm'>Edit</a>
                <a href='proses.php?aksi=hapus_room&room_id={$row['room_id']}' class='btn btn-danger btn-sm'>Delete</a>
            </td>
                                </tr>";
                    }
                    echo "</tbody></table>";
                    break;

                case 'reservations':
                    echo "<h2>Reservations</h2>";
                    $stmt = $db->conn->query("SELECT * FROM reservations");
                    echo "<table class='table table-hover table-bordered'>
                            <thead>
                                <tr>
                                    <th>Reservation ID</th>
                                    <th>Room ID</th>
                                    <th>User ID</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                </tr>
                            </thead>
                            <tbody>";
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                                <td>{$row['reservation_id']}</td>
                                <td>{$row['room_id']}</td>
                                <td>{$row['user_id']}</td>
                                <td>{$row['start_date']}</td>
                                <td>{$row['end_date']}</td>
                            </tr>";
                    }
                    echo "</tbody></table>";
                    break;

                case 'payments':
                    echo "<h2>Payments</h2>";
                    $stmt = $db->conn->query("SELECT * FROM payments");
                    echo "<table class='table table-hover table-bordered'>
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Reservation ID</th>
                                        <th>Tanggal Pembayaran</th>
                                        <th>Metode Pembayaran</th>
                                        <th>Bukti Pembayaran</th>
                                    </tr>
                                </thead>
                                <tbody>";
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $bukti_payment = !empty($row['bukti_payment'])
                            ? "<img src='uploads/payments/{$row['bukti_payment']}' alt='Bukti Payment' width='100'>"
                            : "No Image";
                        echo "<tr>
                                    <td>{$row['payment_id']}</td>
                                    <td>{$row['reservation_id']}</td>
                                    <td>{$row['payment_date']}</td>
                                    <td>{$row['payment_method']}</td>
                                    <td>$bukti_payment</td>
                                </tr>";
                    }
                    echo "</tbody></table>";
                    break;

                case 'recap': // Display Recap Page
                    echo "<h2>Recap</h2>";
                    $stmt = $db->conn->query("SELECT * FROM recaps");
                    echo "<table class='table table-hover table-bordered'>
                                <thead>
                                    <tr>
                                        <th>Recap ID</th>
                                        <th>Reservation ID</th>
                                        <th>Status</th>
                                        <th>Check In Date</th>
                                        <th>Check Out Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>";
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                                    <td>{$row['recap_id']}</td>
                                    <td>{$row['reservation_id']}</td>
                                    <td>{$row['status']}</td>
                                    <td>{$row['check_in_date']}</td>
                                    <td>{$row['check_out_date']}</td>
                                    <td>
<a href='proses.php?aksi=check_in&reservation_id={$row['reservation_id']}' class='btn btn-success btn-sm'>Check In</a>
<a href='proses.php?aksi=check_out&reservation_id={$row['reservation_id']}' class='btn btn-danger btn-sm'>Check Out</a>

                                    </td>
                                </tr>";
                    }
                    echo "</tbody></table>";
                    break;

                default:
                    echo "<h2>Welcome to BlackDoorz</h2>";
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = "logout.php";
            }
        }
    </script>
</body>

</html>