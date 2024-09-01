<?php
include 'includes/db_connection.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION['username'];
    $char_name = trim($_POST['char_name']); // Menghilangkan spasi di awal dan akhir

    // Validasi nama karakter: harus terdiri dari dua kata yang dipisahkan oleh underscore
    // Setiap kata harus diawali dengan huruf kapital, hanya mengandung huruf, dan minimal 5 karakter
    if (!preg_match('/^[A-Z][a-zA-Z]{4,}_+[A-Z][a-zA-Z]{4,}$/', $char_name)) {
        echo "<script>
            alert('Nama karakter harus terdiri dari dua kata, masing-masing minimal 5 karakter, dipisahkan oleh underscore (_), dan harus diawali dengan huruf kapital.');
            window.location.href = 'dashboard.php';
        </script>";
        exit();
    }

    // Mendapatkan ID pengguna
    $sql = "SELECT id FROM accounts WHERE Username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['id'];

        // Memeriksa jumlah karakter yang sudah ada
        $char_count_sql = "SELECT COUNT(*) as count FROM characters WHERE master='$user_id'";
        $char_count_result = $conn->query($char_count_sql);
        $char_count_row = $char_count_result->fetch_assoc();
        $char_count = $char_count_row['count'];

        if ($char_count >= 3) {
            echo "<script>
                alert('Anda sudah memiliki maksimal 3 karakter. Hapus salah satu karakter untuk menambahkan karakter baru.');
                window.location.href = 'dashboard.php';
            </script>";
            exit();
        }

        // Memeriksa apakah nama karakter sudah ada
        $char_exists_sql = "SELECT COUNT(*) as count FROM characters WHERE char_name='$char_name'";
        $char_exists_result = $conn->query($char_exists_sql);
        $char_exists_row = $char_exists_result->fetch_assoc();
        $char_exists = $char_exists_row['count'];

        if ($char_exists > 0) {
            echo "<script>
                alert('Nama karakter sudah ada. Silakan pilih nama lain.');
                window.location.href = 'dashboard.php';
            </script>";
            exit();
        }

        // Mendapatkan alamat IP
        $last_ip = $_SERVER['REMOTE_ADDR'];
        // Mengisi jumlah uang karakter yang baru 
        $cheque_cash_amount = 1500.00;
        // Uang di tabungan
        $bank_money = 2000.00;
        $atribut = 100;

        // Menyimpan data karakter ke database
        $sql = "INSERT INTO characters (master, char_name, Level, PhoneTextRingtone, LastIP, 
            ChequeCash, SavingsCollect, Attribute, Jailed, SentenceTime, 
            CarLic, WepLic, ADPoint, PackageWeapons, PrimaryLicense, 
            SecondaryLicense, CCWLicense, health, hunger, SpawnHealth, Activated, model) VALUES (
            '$user_id', '$char_name', '1', '1', '$last_ip', 
            '$cheque_cash_amount', '$bank_money', '$atribut', 0, 0, 
            0, 0, 0, 0, 0, 0, 0, '100', '100', '100', '1', '2'
        )";

        if ($conn->query($sql) === TRUE) {
            // Mengarahkan ke dashboard.php dengan alert
            echo "<script>
                alert('Karakter berhasil ditambahkan dengan status aktif!');
                window.location.href = 'dashboard.php';
            </script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "User not found!";
    }

    $conn->close();
}
?>
