<?php
include 'includes/db_connection.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Query untuk mendapatkan informasi pengguna
$sql = "SELECT id, Username, RegisterDate, admin FROM accounts WHERE Username='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_id = $row['id'];
    $register_date = $row['RegisterDate'];
    $admin = $row['admin'];
    $role = ($admin == 1) ? "Admin" : "Player";

    // Query untuk mendapatkan karakter-karakter yang dimiliki oleh pengguna
    $char_sql = "SELECT char_name, Level, Activated FROM characters WHERE master='$user_id'";
    $char_result = $conn->query($char_sql);

    // Query untuk mendapatkan 10 karakter teratas berdasarkan level tertinggi dan nama pemilik
    $top_chars_sql = "
        SELECT characters.char_name, characters.Level, accounts.Username AS owner 
        FROM characters 
        JOIN accounts ON characters.master = accounts.id 
        ORDER BY characters.Level DESC 
        LIMIT 10";
    $top_chars_result = $conn->query($top_chars_sql);

    // Query untuk mendapatkan daftar admin
    $admin_list_sql = "SELECT Username, RegisterDate FROM accounts WHERE admin=1";
    $admin_list_result = $conn->query($admin_list_sql);

} else {
    echo "User not found!";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Dashboard</title>
    <style>
        .hidden-form {
            display: none;
        }
    </style>
    <script>
        function toggleForm() {
            var form = document.getElementById("addCharacterForm");
            if (form.style.display === "none" || form.style.display === "") {
                form.style.display = "block";
            } else {
                form.style.display = "none";
            }
        }

        function formatDuration(duration) {
            var years = Math.floor(duration / (365 * 24 * 3600 * 1000));
            duration -= years * (365 * 24 * 3600 * 1000);
            var months = Math.floor(duration / (30 * 24 * 3600 * 1000));
            duration -= months * (30 * 24 * 3600 * 1000);
            var days = Math.floor(duration / (24 * 3600 * 1000));
            duration -= days * (24 * 3600 * 1000);
            var hours = Math.floor(duration / (3600 * 1000));
            duration -= hours * (3600 * 1000);
            var minutes = Math.floor(duration / (60 * 1000));
            duration -= minutes * (60 * 1000);
            var seconds = Math.floor(duration / 1000);

            return years + " tahun, " + months + " bulan, " + days + " hari, " + hours + " jam, " + minutes + " menit, " + seconds + " detik";
        }

        function updateAge() {
            var registerDate = new Date("<?php echo $register_date; ?>").getTime();
            var now = new Date().getTime();
            var duration = now - registerDate;
            document.getElementById("accountAge").innerText = formatDuration(duration);
        }

        setInterval(updateAge, 1000); // Update every second
    </script>
</head>
<body>
    <div class="container">
        <h2>Akun UCP Anda adalah : <?php echo htmlspecialchars($username); ?></h2>
        <p>Anda sebagai <span class="role"><?php echo htmlspecialchars($role); ?></span>!</p>
        <p>Akun ini Anda buat pada <?php echo htmlspecialchars($register_date); ?></p>
        <p>Lama akun: <span id="accountAge"></span></p>

        <h3>Karakter yang Anda Miliki</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Karakter</th>
                    <th>Level</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($char_result->num_rows > 0) {
                    while ($char_row = $char_result->fetch_assoc()) {
                        $status = ($char_row['Activated'] == 1) ? "Aktif" : "Tidak Aktif";
                        echo "<tr>
                                <td>" . htmlspecialchars($char_row['char_name']) . "</td>
                                <td>" . htmlspecialchars($char_row['Level']) . "</td>
                                <td>" . htmlspecialchars($status) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Anda belum memiliki karakter.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <h3>10 Karakter Teratas Berdasarkan Level Tertinggi</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Karakter</th>
                    <th>Level</th>
                    <th>Pemilik</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($top_chars_result->num_rows > 0) {
                    while ($top_char_row = $top_chars_result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($top_char_row['char_name']) . "</td>
                                <td>" . htmlspecialchars($top_char_row['Level']) . "</td>
                                <td>" . htmlspecialchars($top_char_row['owner']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Tidak ada karakter yang ditemukan.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <h3>Daftar Admin</h3>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Lama Akun</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($admin_list_result->num_rows > 0) {
                    while ($admin_row = $admin_list_result->fetch_assoc()) {
                        $registerDate = $admin_row['RegisterDate'];
                        echo "<tr>
                                <td>" . htmlspecialchars($admin_row['Username']) . "</td>
                                <td><span id='adminAge_" . htmlspecialchars($admin_row['Username']) . "'></span></td>
                              </tr>";
                        echo "<script>
                                function calculateAdminAge() {
                                    var registerDate = new Date('$registerDate').getTime();
                                    var now = new Date().getTime();
                                    var duration = now - registerDate;
                                    document.getElementById('adminAge_" . htmlspecialchars($admin_row['Username']) . "').innerText = formatDuration(duration);
                                }
                                setInterval(calculateAdminAge, 1000);
                              </script>";
                    }
                } else {
                    echo "<tr><td colspan='2'>Tidak ada admin yang ditemukan.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <button onclick="toggleForm()">Tambah Karakter</button>

        <div id="addCharacterForm" class="hidden-form">
            <h3>Tambah Karakter Baru</h3>
            <form method="POST" action="add_character.php">
                <label for="char_name">Nama Karakter:</label>
                <input type="text" id="char_name" name="char_name" placeholder="Nama_Karakter" required>
                <button type="submit">Tambah Karakter</button>
            </form>
        </div>

        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
