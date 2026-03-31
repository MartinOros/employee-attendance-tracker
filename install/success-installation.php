<?php


if (!file_exists('../db_connection.php')) {
    header("Location: install.php");
    exit;
}
// Establish database connection
include "../db_connection.php";

// Admin-Benutzer abrufen
$adminQuery = "SELECT username FROM users WHERE id = 1";
$adminResult = $conn->query($adminQuery);
$adminRow = $adminResult->fetch_assoc();
$adminUsername = $adminRow['username'];

// Employee-Benutzer abrufen
$employeeQuery = "SELECT username FROM users_app WHERE id = 1";
$employeeResult = $conn->query($employeeQuery);
$employeeRow = $employeeResult->fetch_assoc();
$employeeUsername = $employeeRow['username'];

?>

<!DOCTYPE html>
<html>

<head>
    <title>Installation Successful - ZSI QR · Attendance Tracker</title>
    <meta name="robots" content="noindex, nofollow">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #333;
            color: #fff;
            padding: 10px;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            margin-right: 10px;
        }

        h1 {
            margin-top: 40px;
            text-align: center;
        }

        .container {
            width: 80%;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #333;
            color: #fff;
            text-align: left;
        }

        .footer {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
            margin-top: 30px;
        }

        .footer a {
            color: white;
            text-decoration: none;
        }

        .footer a:hover {
            color: #ddd;
        }

        .delete-install {
            background-color: #f2dede;
            color: #a94442;
            padding: 10px;
            border: 1px solid #ebccd1;
            border-radius: 4px;
            margin-top: 20px;
        }

        .logo {
            width: 50%;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="container">
            <a href="/" style="display:flex;align-items:center;gap:10px;text-decoration:none;"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="3.5" y="3.5" width="2" height="2" fill="#2563eb" stroke="none"/><rect x="14.5" y="3.5" width="2" height="2" fill="#2563eb" stroke="none"/><rect x="3.5" y="14.5" width="2" height="2" fill="#2563eb" stroke="none"/><rect x="14" y="14" width="3" height="3" rx=".5" fill="#2563eb" stroke="none"/><rect x="18" y="14" width="3" height="3" rx=".5" fill="#2563eb" stroke="none"/><rect x="14" y="18" width="3" height="3" rx=".5" fill="#2563eb" stroke="none"/><rect x="18" y="18" width="3" height="3" rx=".5" fill="#2563eb" stroke="none"/></svg><span style="color:#fff;font-size:17px;font-weight:700;letter-spacing:.02em;">ZSI<span style="color:#2563eb;">QR</span></span></a>
        </div>
    </div>

    <div class="container">
        <h1>Installation was Successful</h1>
        <p>Thank you for using ZSI QR · Attendance Tracker!</p>
        <p class="delete-install"><b>Please make sure to delete the /install folder for security purposes.</b></p>

        <h2>Admin Area:</h2>
        <table>
            <tr>
                <th>URL:</th>
                <td><a href="/admin/index.php">/admin/index.php</a></td>
            </tr>
            <tr>
                <th>Username:</th>
                <td><?php echo $adminUsername; ?></td>
            </tr>
            <tr>
                <th>Password:</th>
                <td>[Hashed Password]</td>
            </tr>
        </table>

        <h2>Employee Area:</h2>
        <table>
            <tr>
                <th>URL:</th>
                <td><a href="/index.php">/index.php</a></td>
            </tr>
            <tr>
                <th>Username:</th>
                <td><?php echo $employeeUsername; ?></td>
            </tr>
            <tr>
                <th>Password:</th>
                <td>[Hashed Password]</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        &copy; <?php echo date(chr(34)."Y".chr(34)); ?> ZSI QR · Attendance Tracker
    </div>
</body>

</html>