<?php
include 'includes/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = md5(trim($_POST['password'])); // Password di-hash menggunakan MD5
    $discord = trim($_POST['discord']);
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Validasi panjang ID Discord
    if (strlen($discord) !== 18) {
        echo "<script>
            alert('ID Discord harus tepat 18 digit.');
            window.location.href = 'register.php';
        </script>";
        exit();
    }

    // Validasi username dan IP address
    $sql = "SELECT Username, IP FROM accounts WHERE Username='$username' OR IP='$ip_address'";
    $result = $conn->query($sql);
    $existing_user = false;
    $existing_ip = false;
    $existing_username = '';

    while ($row = $result->fetch_assoc()) {
        if ($row['Username'] === $username) {
            $existing_user = true;
            $existing_username = $username;
        }
        if ($row['IP'] === $ip_address) {
            $existing_ip = true;
        }
    }

    if ($existing_user && $existing_ip) {
        echo "<script>
            alert('Username dan IP address sudah terdaftar. Silakan gunakan username yang berbeda atau hubungi dukungan kami.');
            window.location.href = 'register.php';
        </script>";
    } elseif ($existing_user) {
        echo "<script>
            alert('Username sudah terdaftar. Silakan gunakan username yang berbeda.');
            window.location.href = 'register.php';
        </script>";
    } elseif ($existing_ip) {
        echo "<script>
            alert('Anda sudah memiliki akun dengan IP address ini. Jika Anda mengalami kesulitan, silakan hubungi dukungan kami.');
            window.location.href = 'register.php';
        </script>";
    } else {
        // Menyimpan data akun ke database
        $sql = "INSERT INTO accounts (Username, Email, Password, Discord, RegisterDate, Online, Quiz, Admin, DonateRank, SecretHint, SecretWord, LoginDate, IP, LastIP, Answer1, Answer2, answered_questions, Namechanges, Phonechanges, Forum, AdminNote, Serial)
                VALUES ('$username', '$email', '$password', '$discord', NOW(), 0, 0, 0, 0, 0, 0, 0, '$ip_address', '$ip_address', 0, 0, 0, 0, 0, 0, 0, 0)";

        if ($conn->query($sql) === TRUE) {
            echo "Registrasi Berhasil!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Register</title>
</head>
<body>
    <div class="container-content">
        <h2>Register</h2>
        <form method="POST" action="register.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="discord">Discord ID:</label>
            <input type="number" id="discord" name="discord" required>
            
            <p style="color:#ccc;"><b>Catatan</b> : Untuk Discord ID kalian dapat mengetahuinya melalui bot Official kami dengan cara <i>"Memberikan pesan pribadi kepada bot dengan menggunakan command <b>!myid</b>."</i> maka secara otomatis bot akan mengirimkan ID Discord anda.</p>
            
            <button type="submit">Register</button>
        </form>
        <p>Sudah memilki akun? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
