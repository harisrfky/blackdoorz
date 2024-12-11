<?php
error_reporting(1);
include "database.php";
$abc = new Database();

// Handle CORS
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400'); // cache for 1 day
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    exit(0);
}

// Function to sanitize input
function filter($data)
{
    return preg_replace('/[^a-zA-Z0-9]/', '', $data);
}

// Main Logic
$postdata = file_get_contents("php://input");
$aksi = $data->aksi;

if ($aksi == 'login') {
    $username = $data->username;
    $password = $data->password;

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $abc->conn->prepare($query);
    $stmt->bindValue(1, $username, PDO::PARAM_STR); // Mengikat parameter dengan PDO
    $stmt->execute();

    // Mengambil hasil sebagai array asosiatif
    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    if ($user) {
        // Verifikasi password (gunakan hashing seperti password_hash() untuk keamanan)
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user'] = [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];

            // Arahkan berdasarkan role
            $redirect_page = ($user['role'] == 'admin') ? 'index_admin.php' : 'index_user.php';

            echo json_encode([
                'status' => 'success',
                'message' => 'Login successful',
                'redirect' => $redirect_page
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid password'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Username not found'
        ]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode($postdata);
    $aksi = $data->aksi;

    // Handle Users Table
    if (isset($data->user_id)) {
        $user_id = $data->user_id;
        $username = $data->username;
        $email = $data->email;
        $password = $data->password;
        $role = $data->role;

        $data2 = [
            'user_id' => $user_id,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => $role
        ];

        if ($aksi == 'tambah') {
            $abc->tambah_data_users($data2);
        } elseif ($aksi == 'ubah') {
            $abc->ubah_data_users($data2);
        } elseif ($aksi == 'hapus') {
            $abc->hapus_data_users($user_id);
        }
    }

    // Server-side handler (server.php)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = json_decode($postdata);
        $aksi = $data->aksi;

        // Handle Rooms Table
        if (isset($data->room_name) && isset($data->price_per_night) && isset($data->descriptions)) {
            // Ambil data dari request JSON
            $room_name = $data->room_name;
            $price_per_night = $data->price_per_night;
            $descriptions = $data->descriptions;
            $room_image = $data->room_image;

            $data2 = [
                'room_name' => $room_name,
                'price_per_night' => $price_per_night,
                'descriptions' => $descriptions,
                'room_image' => $room_image
            ];

            // Proses penambahan kamar menggunakan metode yang ada
            if ($aksi == 'tambah') {
                $abc->tambah_data_rooms($data2);
                echo json_encode(['status' => 'success', 'message' => 'Room added successfully']);
            } elseif ($aksi == 'ubah') {
                // Logika untuk mengubah data kamar
                $abc->ubah_data_rooms($data2);
                echo json_encode(['status' => 'success', 'message' => 'Room updated successfully']);
            } elseif ($aksi == 'hapus') {
                // Logika untuk menghapus data kamar
                $abc->hapus_data_rooms($data->room_id);
                echo json_encode(['status' => 'success', 'message' => 'Room deleted successfully']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing room data']);
        }
    }


    // Handle Reservations Table
    if (isset($data->reservation_id)) {
        $reservation_id = $data->reservation_id;
        $room_id = $data->room_id;
        $user_id = $data->user_id;
        $start_date = $data->start_date;
        $end_date = $data->end_date;

        $data2 = [
            'reservation_id' => $reservation_id,
            'room_id' => $room_id,
            'user_id' => $user_id,
            'start_date' => $start_date,
            'end_date' => $end_date
        ];

        if ($aksi == 'tambah') {
            $abc->tambah_data_reservations($data2);
        } elseif ($aksi == 'ubah') {
            $abc->ubah_data_reservations($data2);
        } elseif ($aksi == 'hapus') {
            $abc->hapus_data_reservations($reservation_id);
        }
    }

    // Handle Payments Table
    if (isset($data->payment_id)) {
        $payment_id = $data->payment_id;
        $reservation_id = $data->reservation_id;
        $payment_date = $data->payment_date;
        $bukti_payment = $data->bukti_payment;
        $payment_method = $data->payment_method;

        $data2 = [
            'payment_id' => $payment_id,
            'reservation_id' => $reservation_id,
            'payment_date' => $payment_date,
            'bukti_payment' => $bukti_payment,
            'payment_method' => $payment_method
        ];

        if ($aksi == 'tambah') {
            $abc->tambah_data_payments($data2);
        } elseif ($aksi == 'ubah') {
            $abc->ubah_data_payments($data2);
        } elseif ($aksi == 'hapus') {
            $abc->hapus_data_payments($payment_id);
        }
    }

    // Handle Recaps Table
    if (isset($data->recap_id)) {
        $recap_id = $data->recap_id;
        $reservation_id = $data->reservation_id;
        $status = $data->status;
        $check_in_date = $data->check_in_date;
        $check_out_date = $data->check_out_date;

        $data2 = [
            'recap_id' => $recap_id,
            'reservation_id' => $reservation_id,
            'status' => $status,
            'check_in_date' => $check_in_date,
            'check_out_date' => $check_out_date
        ];

        if ($aksi == 'tambah') {
            $abc->tambah_data_recaps($data2);
        } elseif ($aksi == 'ubah') {
            $abc->ubah_data_recaps($data2);
        } elseif ($aksi == 'hapus') {
            $abc->hapus_data_recaps($recap_id);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $data_users = $abc->tampil_semua_data_users();
    $data_rooms = $abc->tampil_semua_data_rooms();
    $data_reservations = $abc->tampil_semua_data_reservations();
    $data_payments = $abc->tampil_semua_data_payments();
    $data_recaps = $abc->tampil_semua_data_recaps();

    echo json_encode([
        'users' => $data_users,
        'rooms' => $data_rooms,
        'reservations' => $data_reservations,
        'payments' => $data_payments,
        'recaps' => $data_recaps
    ]);
}
?>