<?php
session_start();
include '../server/database.php'; // Check the correct path

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Instantiate Database object
$db = new Database(); // This creates an instance of the Database class

// Now use $db->conn for queries
$sql = "SELECT * FROM rooms";
$result = $db->conn->query($sql);

// Fetch all the results
$rooms = $result->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kamar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Header styling with buttons aligned to the top right */
        .header-container {
            text-align: center;
            padding: 20px;
            position: relative;
        }

        .logout-btn,
        .upload-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .logout-btn:hover {
            background-color: #e60000;
        }

        .upload-btn {
            background-color: #007bff;
            margin-right: 100px;
        }

        .upload-btn:hover {
            background-color: #0056b3;
        }

        /* Slider styling */
        .slider {
            width: 100%;
            height: 400px;
            overflow: hidden;
            position: relative;
            padding: 0 20px;
            /* Menambahkan jarak samping pada slider */
        }

        .slider-images {
            display: flex;
            transition: transform 0.5s ease;
        }

        .slider img {
            width: calc(100% - 40px);
            /* Mengurangi lebar gambar untuk memberi ruang pada margin */
            height: 100%;
            object-fit: cover;
            /* Membuat gambar tetap proporsional dan mengisi kontainer */
            margin: 0 20px;
            /* Memberikan jarak samping antar gambar */
        }


        /* Room container layout with grid */
        .rooms-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            padding: 20px;
        }

        .room-container {
            border: 1px solid #ccc;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .room-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .room-name {
            font-size: 1.5em;
            font-weight: bold;
            margin-top: 10px;
        }

        .room-price {
            color: green;
            font-size: 1.2em;
            margin-top: 5px;
        }

        .room-description {
            margin-top: 10px;
        }

        .room-action {
            display: inline-block;
            margin-top: 10px;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .room-action:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <header class="header-container">
        <h1>Daftar Kamar yang Tersedia</h1>
        <!-- Logout Button -->
        <button class="logout-btn" onclick="confirmLogout()">Logout</button>
        <a href="upload_payment.php">
            <button class="upload-btn">Upload Payment</button>
        </a>
    </header>

    <!-- Slider Section -->
    <div class="slider">
        <div class="slider-images">
            <?php
            // Display images from the rooms folder (assuming room_image stores file names)
            foreach ($rooms as $room) {
                echo "<img src='uploads/rooms/" . htmlspecialchars($room["room_image"]) . "' alt='Room Image'>";
            }
            ?>
        </div>
    </div>

    <!-- Rooms Listing -->
    <div class="rooms-container">
        <?php
        // Check if there are rooms available
        if (!empty($rooms)) {
            foreach ($rooms as $row) {
                echo "<div class='room-container'>";
                echo "<h3 class='room-name'>" . htmlspecialchars($row["room_name"]) . "</h3>";
                echo "<img class='room-image' src='uploads/rooms/" . htmlspecialchars($row["room_image"]) . "' alt='Room Image'><br>";
                echo "<p class='room-price'>Harga per malam: Rp " . number_format($row["price_per_night"], 2) . "</p>";
                echo "<p class='room-description'>" . htmlspecialchars($row["descriptions"]) . "</p>";
                echo "<a class='room-action' href='sewa.php?room_id=" . $row["room_id"] . "&room_name=" . urlencode($row["room_name"]) . "&price_per_night=" . $row["price_per_night"] . "'>Sewa Kamar</a>";
                echo "</div>";
            }
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function confirmLogout() {
            if (confirm("Apakah Anda yakin ingin logout?")) {
                window.location.href = "logout.php"; // Redirect to the logout page
            }
        }

        // Simple slider functionality (optional)
        let currentIndex = 0;
        const images = document.querySelectorAll('.slider img');
        setInterval(() => {
            currentIndex = (currentIndex + 1) % images.length;
            document.querySelector('.slider-images').style.transform = `translateX(-${currentIndex * 100}%)`;
        }, 3000);
    </script>

</body>

</html>