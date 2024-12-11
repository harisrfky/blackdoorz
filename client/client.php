<?php
error_reporting(1); // error ditampilkan
class Client
{
    private $url;

    public function __construct($url)
    {
        $this->url = $url;
        unset($url);
    }

    // function untuk menghapus selain huruf dan angka
    public function filter($data)
    {
        $data = preg_replace('/[^a-zA-Z0-9]/', '', $data);
        return $data;
        unset($data);
    }

    // CRUD for Users
    public function tampil_semua_data_users()
    {
        $client = curl_init($this->url);
        curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($client);
        curl_close($client);
        $data = json_decode($response);
        return $data;
        unset($data, $client, $response);
    }

    public function tampil_data_users($user_id)
    {
        $user_id = $this->filter($user_id);
        $client = curl_init($this->url . "?aksi=tampil&user_id=" . $user_id);
        curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($client);
        curl_close($client);
        $data = json_decode($response);
        return $data;
        unset($user_id, $client, $response, $data);
    }

    public function tambah_data_users($data)
    {
        $data = '{
            "user_id":"' . $data['user_id'] . '",
            "username":"' . $data['username'] . '",
            "email":"' . $data['email'] . '",
            "password":"' . $data['password'] . '",
            "role":"' . $data['role'] . '",
            "aksi":"' . $data['aksi'] . '"
        }';
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $this->url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($c);
        curl_close($c);
        unset($data, $c, $response);
    }

    public function ubah_data_users($data)
    {
        $data = '{
            "user_id":"' . $data['user_id'] . '",
            "username":"' . $data['username'] . '",
            "email":"' . $data['email'] . '",
            "password":"' . $data['password'] . '",
            "role":"' . $data['role'] . '",
            "aksi":"' . $data['aksi'] . '"
        }';
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $this->url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($c);
        curl_close($c);
        unset($data, $c, $response);
    }

    public function hapus_data_users($data)
    {
        $user_id = $this->filter($data['user_id']);
        $data = '{
            "user_id":"' . $data['user_id'] . '",
            "username":"' . $data['username'] . '",
            "email":"' . $data['email'] . '",
            "password":"' . $data['password'] . '",
            "role":"' . $data['role'] . '",
            "aksi":"' . $data['aksi'] . '"
        }';
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $this->url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($c);
        curl_close($c);
        unset($user_id, $data, $c, $response);
    }

    // CRUD for Rooms
    public function tampil_semua_data_rooms()
    {
        $client = curl_init($this->url);
        curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($client);
        curl_close($client);
        $data = json_decode($response);
        return $data;
        unset($data, $client, $response);
    }

    public function tampil_data_rooms($room_id)
    {
        $response = $this->sendRequest("GET", "rooms/{$room_id}");
        return $response;
    }


    // Fungsi untuk menambahkan data kamar
    public function tambah_data_rooms($data)
    {
        // Setup CURL untuk mengirimkan data
        $ch = curl_init();

        // Set opsi-opsi CURL
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // Eksekusi CURL
        $response = curl_exec($ch);

        // Cek apakah ada error
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        } else {
            // Debug response dari API
            echo 'Response from API: ' . $response;
        }
    }


    public function ubah_data_rooms($data)
    {
        return $this->sendRequest("POST", "rooms/update", $data);
    }
    private function sendRequest($method, $endpoint, $data = null)
    {
        $ch = curl_init();
        $url = $this->url . '/' . $endpoint;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        if ($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        // Debugging: Memastikan response adalah string
        var_dump($response);
        // Menambahkan JSON decode agar selalu mengembalikan objek
        return json_decode($response, false);
    }


    public function hapus_data_rooms($room_id)
    {
        $room_id = $this->filter($room_id);
        $data = '{
            "room_id":"' . $room_id . '",
            "aksi":"hapus"
        }';
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $this->url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($c);
        curl_close($c);
        unset($room_id, $data, $c, $response);
    }

    // CRUD for Payments
    public function tampil_semua_data_payments()
    {
        $client = curl_init($this->url);
        curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($client);
        curl_close($client);
        $data = json_decode($response);
        return $data;
        unset($data, $client, $response);
    }

    public function tambah_data_payments($data)
    {
        $data = '{
            "payment_id":"' . $data['payment_id'] . '",
            "reservation_id":"' . $data['reservation_id'] . '",
            "payment_date":"' . $data['payment_date'] . '",
            "bukti_payment":"' . $data['bukti_payment'] . '",
            "payment_method":"' . $data['payment_method'] . '",
            "aksi":"' . $data['aksi'] . '"
        }';
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $this->url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($c);
        curl_close($c);
        unset($data, $c, $response);
    }

    public function ubah_data_payments($data)
    {
        $data = '{
            "payment_id":"' . $data['payment_id'] . '",
            "payment_date":"' . $data['payment_date'] . '",
            "bukti_payment":"' . $data['bukti_payment'] . '",
            "payment_method":"' . $data['payment_method'] . '",
            "aksi":"' . $data['aksi'] . '"
        }';
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $this->url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($c);
        curl_close($c);
        unset($data, $c, $response);
    }

    public function hapus_data_payments($payment_id)
    {
        $payment_id = $this->filter($payment_id);
        $data = '{
            "payment_id":"' . $payment_id . '",
            "aksi":"hapus"
        }';
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $this->url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($c);
        curl_close($c);
        unset($payment_id, $data, $c, $response);
    }

    // CRUD for Reservations
    public function tampil_semua_data_reservations()
    {
        $client = curl_init($this->url);
        curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($client);
        curl_close($client);
        $data = json_decode($response);
        return $data;
        unset($data, $client, $response);
    }

    public function tambah_data_reservations($data)
    {
        $data_json = json_encode($data); // Mengubah array menjadi format JSON

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);  // URL untuk mengirim data
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',  // Set header untuk JSON
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json); // Kirim data JSON
        $response = curl_exec($ch);  // Eksekusi curl
        curl_close($ch);
    }


    public function ubah_data_reservations($data)
    {
        $data = '{
            "reservation_id":"' . $data['reservation_id'] . '",
            "start_date":"' . $data['start_date'] . '",
            "end_date":"' . $data['end_date'] . '",
            "total":"' . $data['total'] . '",
            "status":"' . $data['status'] . '",
            "aksi":"' . $data['aksi'] . '"
        }';
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $this->url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($c);
        curl_close($c);
        unset($data, $c, $response);
    }

    public function hapus_data_reservations($reservation_id)
    {
        $reservation_id = $this->filter($reservation_id);
        $data = '{
            "reservation_id":"' . $reservation_id . '",
            "aksi":"hapus"
        }';
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $this->url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($c);
        curl_close($c);
        unset($reservation_id, $data, $c, $response);
    }

    // CRUD for Recaps
    public function tampil_semua_data_recaps()
    {
        $client = curl_init($this->url);
        curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($client);
        curl_close($client);
        $data = json_decode($response);
        return $data;
        unset($data, $client, $response);
    }

    public function tambah_data_recaps($data)
    {
        $data = '{
        "recap_id":"' . $data['recap_id'] . '",
        "reservation_id":"' . $data['reservation_id'] . '",
        "status":"' . $data['status'] . '",
        "check_in_date":"' . $data['check_in_date'] . '",
        "check_out_date":"' . $data['check_out_date'] . '",
        "aksi":"' . $data['aksi'] . '"
    }';
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $this->url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($c);
        curl_close($c);
        unset($data, $c, $response);
    }

    public function ubah_data_recaps($data)
    {
        $data = '{
        "recap_id":"' . $data['recap_id'] . '",
        "status":"' . $data['status'] . '",
        "check_in_date":"' . $data['check_in_date'] . '",
        "check_out_date":"' . $data['check_out_date'] . '",
        "aksi":"' . $data['aksi'] . '"
    }';
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $this->url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($c);
        curl_close($c);
        unset($data, $c, $response);
    }

    public function hapus_data_recaps($recap_id)
    {
        $recap_id = $this->filter($recap_id);
        $data = '{
        "recap_id":"' . $recap_id . '",
        "aksi":"hapus"
    }';
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $this->url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($c);
        curl_close($c);
        unset($recap_id, $data, $c, $response);
    }



    // function yang terakhir kali di-load saat class dipanggil
    public function __destruct()
    {
        unset($this->url);
    }
}

$url = 'http://192.168.1.57/restful-json-blackdoorz/server/server.php';
// buat objek baru dari class Client
$abc = new Client($url);
