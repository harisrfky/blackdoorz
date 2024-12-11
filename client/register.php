<?php
session_start();

$success = false; // Tambahkan variabel ini untuk menandai keberhasilan

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    if ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } else {
        include '../server/database.php'; // Pastikan sudah ada file ini
        $db = new Database();
        $conn = $db->conn;

        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error = "Username sudah terdaftar!";
        } else {
            // Langsung menyimpan password tanpa hashing
            $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password); // Password disimpan langsung
            $stmt->bindParam(':role', $role);

            if ($stmt->execute()) {
                $success = true; // Tandai bahwa registrasi berhasil
            } else {
                $error = "Gagal mendaftar. Coba lagi!";
            }
        }

        $conn = null;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-container {
            background: #fff;
            color: #333;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            padding: 30px 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            position: relative;
        }

        .logo {
            width: 100px;
            height: 100px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            position: absolute;
            top: -50px;
            left: 50%;
            transform: translateX(-50%);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .login-container h1 {
            font-size: 28px;
            margin-top: 60px;
            margin-bottom: 20px;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .login-container label {
            font-weight: bold;
            text-align: left;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .login-container input[type="text"],
        .login-container input[type="email"],
        .login-container input[type="password"] {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .login-container input:focus {
            border-color: #6a11cb;
            outline: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .login-container button {
            background-color: #6a11cb;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .login-container button:hover {
            background-color: #2575fc;
        }

        .login-container p {
            margin-top: 20px;
            font-size: 14px;
        }

        .login-container a {
            color: #2575fc;
            text-decoration: none;
            font-weight: bold;
        }

        .login-container a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Logo -->
        <div class="logo">
            <img src="assets/logo.png" alt="Logo">
        </div>
        <h1>Daftar</h1>

        <?php if (isset($error)) {
            echo "<p class='error-message'>$error</p>";
        } ?>

        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <label for="confirm_password">Konfirmasi Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <input type="hidden" name="role" value="user">

            <button type="submit">Daftar</button>
        </form>

        <p>Sudah memiliki akun? <a href="login.php">Login di sini</a></p>
    </div>

    <?php if ($success): ?>
        <script>
            alert("Pendaftaran akun berhasil!");
            window.location.href = "login.php"; // Redirect ke halaman login
        </script>
    <?php endif; ?>
</body>

</html>