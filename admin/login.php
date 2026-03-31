<?php

if (!file_exists('../db_connection.php')) {
    header("Location: ../install/install.php");
    exit;
}
// Start the session
session_start();

// Establish database connection
include "../db_connection.php";

// Check if the login form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the username and password from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Authenticate the user
    if (authenticateUser($username, $password, $conn)) {
        // User is authenticated, set the session variable
        $_SESSION['authenticated'] = true;

        // Redirect the user to the protected page
        header("Location: index.php");
        exit;
    } else {
        // Invalid login credentials, display an error message
        $error = "Invalid login credentials. Please try again.";
    }
}

// Function to authenticate the user
function authenticateUser($username, $password, $conn)
{
    // Execute a database query to check the user in the "users" table
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a result was found
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $storedPassword = $user['password'];

        // Verify the password hash
        if (password_verify($password, $storedPassword)) {
            // User was found and is authenticated
            $_SESSION['user_type'] = 'users'; // Set the user role
            return true;
        }
    }

    // User was not found or is not authenticated
    return false;
}

?>
<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="utf-8">
    <title>ZSI QR · Prihlásenie administrátora</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" type="text/css" href="../style.css">
</head>

<body>
    <div class="navbar">
        <div class="logo">
            <a href="/admin/" style="display:flex;align-items:center;gap:10px;text-decoration:none;"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="3.5" y="3.5" width="2" height="2" fill="#2563eb" stroke="none"/><rect x="14.5" y="3.5" width="2" height="2" fill="#2563eb" stroke="none"/><rect x="3.5" y="14.5" width="2" height="2" fill="#2563eb" stroke="none"/><rect x="14" y="14" width="3" height="3" rx=".5" fill="#2563eb" stroke="none"/><rect x="18" y="14" width="3" height="3" rx=".5" fill="#2563eb" stroke="none"/><rect x="14" y="18" width="3" height="3" rx=".5" fill="#2563eb" stroke="none"/><rect x="18" y="18" width="3" height="3" rx=".5" fill="#2563eb" stroke="none"/></svg><span style="color:#fff;font-size:17px;font-weight:700;letter-spacing:.02em;">ZSI<span style="color:#2563eb;">QR</span></span></a>
        </div>
        <div class="menu-links"><span style="color:#fff;font-weight:700;font-size:15px;letter-spacing:.03em;">ZSI QR &mdash; Administrácia</span></div>
        <div>
            <a class="back-button" href="/index.php">Back to App</a>
        </div>
    </div>

    <div class="page-content">
        <div class="auth-card">
            <h1>Prihlásenie administrátora</h1>
            <p class="auth-sub">Prístup k filtrom dochádzky, exportu a generovaniu QR kódov.</p>
            <?php if (!empty($error)) { ?>
                <p class="auth-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php } ?>
            <form method="post" action="">
                <label for="username">Používateľské meno</label>
                <input type="text" name="username" id="username" required autocomplete="username">

                <label for="password">Heslo</label>
                <input type="password" name="password" id="password" required autocomplete="current-password">

                <input type="submit" value="Prihlásiť sa">
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> ZSI — Attendance Tracker</p>
    </footer>
</body>

</html>
