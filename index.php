<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <?php if (isset($_SESSION['username'])): ?>
                        <li><a href="dashboard_<?php echo htmlspecialchars($_SESSION['role']); ?>.php">Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>
    
    <main>
        <div class="container">
            <h1>UCP Street Classic</h1>
            <p>Explore the features by navigating through the menu above. Whether you are looking to log in, register, or access your dashboard, we've got you covered.</p>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Street Classic. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
