<?php
class Database
{
    private $host = "localhost";
    private $dbname = "blackdoorz";
    private $user = "root";
    private $password = "newpassword";
    public $conn;

    public function __construct()
    {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname;charset=utf8", $this->user, $this->password);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    // CRUD for Users
    public function tampil_semua_data_users()
    {
        $query = $this->conn->prepare("SELECT * FROM users");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tambah_data_users($data)
    {
        $query = $this->conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $query->execute([$data['username'], $data['email'], $data['password'], $data['role']]);
    }

    public function ubah_data_users($data)
    {
        $query = $this->conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, role = ? WHERE user_id = ?");
        $query->execute([$data['username'], $data['email'], $data['password'], $data['role'], $data['user_id']]);
    }

    public function hapus_data_users($user_id)
    {
        $query = $this->conn->prepare("DELETE FROM users WHERE user_id = ?");
        $query->execute([$user_id]);
    }

    // CRUD for Rooms
    public function tampil_semua_data_rooms()
    {
        $query = $this->conn->prepare("SELECT * FROM rooms");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fungsi untuk menambah data kamar (dengan gambar)
    public function tambah_data_rooms($data)
    {
        // Pastikan data yang diterima sudah benar
        if (isset($data['room_name']) && isset($data['price_per_night']) && isset($data['descriptions'])) {
            // Periksa jika gambar ada, maka gunakan path gambar yang sudah disimpan
            $room_image = isset($data['room_image']) ? $data['room_image'] : null;

            // Query SQL untuk menambah data room ke database
            $query = "INSERT INTO rooms (room_name, price_per_night, descriptions, room_image) VALUES (:room_name, :price_per_night, :descriptions, :room_image)";

            // Siapkan pernyataan untuk dieksekusi
            $stmt = $this->conn->prepare($query);

            // Bind parameter
            $stmt->bindParam(':room_name', $data['room_name']);
            $stmt->bindParam(':price_per_night', $data['price_per_night']);
            $stmt->bindParam(':descriptions', $data['descriptions']);

            // Pastikan room_image memiliki nilai yang benar (bisa null)
            if ($room_image !== null) {
                $stmt->bindParam(':room_image', $room_image);
            } else {
                $stmt->bindParam(':room_image', $room_image, PDO::PARAM_NULL);
            }

            // Eksekusi query
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Room added successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add room']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing required data']);
        }
    }



    public function ubah_data_rooms($data)
    {
        $query = $this->conn->prepare("UPDATE rooms SET room_name = ?, room_image = ?, price_per_night = ?, descriptions = ? WHERE room_id = ?");
        $query->execute([$data['room_name'], $data['room_image'], $data['price_per_night'], $data['descriptions'], $data['room_id']]);
    }

    public function hapus_data_rooms($room_id)
    {
        $query = $this->conn->prepare("DELETE FROM rooms WHERE room_id = ?");
        $query->execute([$room_id]);
    }

    // CRUD Untuk Tabel Payments
    public function tampil_semua_data_payments()
    {
        $query = $this->conn->prepare("SELECT * FROM payments ORDER BY payment_id");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tampil_data_payments($payment_id)
    {
        $query = $this->conn->prepare("SELECT * FROM payments WHERE payment_id = ?");
        $query->execute(array($payment_id));
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tambah_data_payments($data)
    {
        $query = $this->conn->prepare("INSERT INTO payments (payment_id, reservation_id, payment_date, bukti_payment, payment_method) VALUES (?, ?, ?, ?, ?)");
        $query->execute([$data['payment_id'], $data['reservation_id'], $data['payment_date'], $data['bukti_payment'], $data['payment_method']]);
    }


    public function ubah_data_payments($data)
    {
        $query = $this->conn->prepare("UPDATE payments SET payment_date = ?, bukti_payment = ?, payment_method = ? WHERE payment_id = ?");
        $query->execute([$data['payment_date'], $data['bukti_payment'], $data['payment_method'], $data['payment_id']]);
    }


    public function hapus_data_payments($payment_id)
    {
        $query = $this->conn->prepare("DELETE FROM payments WHERE payment_id = ?");
        $query->execute([$payment_id]);
    }


    // CRUD Untuk Tabel Reservations
    public function tampil_semua_data_reservations()
    {
        $query = $this->conn->prepare("SELECT * FROM reservations ORDER BY reservation_id");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tampil_data_reservations($reservation_id)
    {
        $query = $this->conn->prepare("SELECT * FROM reservations WHERE reservation_id = ?");
        $query->execute(array($reservation_id));
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tambah_data_reservations($data)
    {
        try {
            // Mulai transaksi
            $this->conn->beginTransaction();

            // Insert ke tabel reservations
            $query = $this->conn->prepare("INSERT INTO reservations (reservation_id, room_id, user_id, start_date, end_date, total, status) 
                                            VALUES (?, ?, ?, ?, ?, ?, ?)");
            $query->execute([
                $data['reservation_id'],
                $data['room_id'],
                $data['user_id'],
                $data['start_date'],
                $data['end_date'],
                $data['total'],
                $data['status']
            ]);

            // Debugging: Periksa apakah query pertama berhasil
            if ($query->rowCount() > 0) {
                echo "Data reservations berhasil ditambahkan.\n";
            } else {
                echo "Gagal menambahkan data ke reservations.\n";
            }

            // Insert ke tabel recaps
            $queryRecaps = $this->conn->prepare("INSERT INTO recaps (reservation_id, status) VALUES (?, ?)");
            $queryRecaps->execute([$data['reservation_id'], 'Pending']); // Status awal "Pending"

            // Debugging: Periksa apakah query kedua berhasil
            if ($queryRecaps->rowCount() > 0) {
                echo "Data recaps berhasil ditambahkan.\n";
            } else {
                echo "Gagal menambahkan data ke recaps.\n";
            }

            // Commit transaksi jika semua berhasil
            $this->conn->commit();

        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $this->conn->rollBack();
            echo "Terjadi kesalahan: " . $e->getMessage();
        }
    }



    public function ubah_data_reservations($data)
    {
        $query = $this->conn->prepare("UPDATE reservations SET start_date = ?, end_date = ?, total = ?, status = ? WHERE reservation_id = ?");
        $query->execute(array($data['start_date'], $data['end_date'], $data['total'], $data['status']));
    }

    public function hapus_data_reservations($reservation_id)
    {
        $query = $this->conn->prepare("DELETE FROM reservations WHERE reservation_id = ?");
        $query->execute([$reservation_id]);
    }


    // CRUD Untuk Tabel Recaps
    public function tampil_semua_data_recaps()
    {
        $query = $this->conn->prepare("SELECT * FROM recaps ORDER BY recap_id");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tampil_data_recaps($recap_id)
    {
        $query = $this->conn->prepare("SELECT * FROM recaps WHERE recap_id = ?");
        $query->execute(array($recap_id));
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tambah_data_recaps($data)
    {
        $query = $this->conn->prepare("INSERT INTO recaps (recap_id, reservation_id, status, check_in_date, check_out_date) VALUES (?, ?, ?, ?, ?)");
        $query->execute(array($data['recap_id'], $data['reservation_id'], $data['status'], $data['check_in_date'], $data['check_out_date']));
    }


    public function ubah_data_recaps($data)
    {
        $query = $this->conn->prepare("UPDATE recaps SET status = ?, check_in_date = ?, check_out_date = ? WHERE recap_id = ?");
        $query->execute(array($data['status'], $data['check_in_date'], $data['check_out_date']));
    }

    public function hapus_data_recaps($recap_id)
    {
        $query = $this->conn->prepare("DELETE FROM recaps where recap_id=?");
        $query->execute(($recap_id));
    }


}