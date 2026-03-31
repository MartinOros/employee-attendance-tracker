<?php


if (!file_exists('db_connection.php')) {
    header("Location: install/install.php");
    exit;
}


// Establish database connection
include "db_connection.php";

session_start();

// Überprüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: login-app.php");
    exit;
}


// Generate a CSRF token if none is present
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

date_default_timezone_set('Europe/Bratislava');

// Check-in / Check-out logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify the CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }

    // Employee ID — strip legacy "Name: " prefix if present
    $employeeId = isset($_POST['employee_id']) ? htmlspecialchars(preg_replace('/^Name:\s*/i', '', trim($_POST['employee_id']))) : '';

    // Check if the employee has already checked in
    $stmt = $conn->prepare("SELECT * FROM attendance WHERE employee_id = ? AND DATE(check_in_time) = CURDATE() ORDER BY check_in_time DESC LIMIT 1");
    $stmt->bind_param("s", $employeeId);
    $stmt->execute();
    $checkResult = $stmt->get_result();
    $checkData = $checkResult->fetch_assoc();
    $checkOutTime = $checkData ? $checkData['check_out_time'] : null;

    $message = '';


    if (isset($_POST['checkin']) && $_POST['checkin'] === 'Check-in' && preg_match("/^[a-zA-ZäöüßÄÖÜ\s\-_]+$/u", $employeeId)) {
        // Check if the employee has already checked in and checked out
        if ($checkResult->num_rows === 0 || $checkOutTime !== null) {
            // Check-in process
            $currentTime = date('Y-m-d H:i:s');
            // Insert check-in record into the database
            $stmt = $conn->prepare("INSERT INTO attendance (employee_id, check_in_time) VALUES (?, ?)");
            $stmt->bind_param("ss", $employeeId, $currentTime);
            if ($stmt->execute() === TRUE) {
                $message = "Check-in successful for Employee: " . $employeeId;
                header("Refresh:0");
            } else {
                $message = "Error during check-in for Employee: " . $employeeId . " Error: " . $conn->error;
                header("Refresh:0");
            }
        } else {
            $message = "The employee: " . $employeeId . " has already checked in and not checked out.";
            header("Refresh:0");
        }
    } elseif (isset($_POST['checkout']) && $_POST['checkout'] === 'Check-out' && preg_match("/^[a-zA-ZäöüßÄÖÜ\s\-_]+$/u", $employeeId)) {
        // Check if the employee has already checked in and not checked out
        if ($checkResult->num_rows > 0 && $checkOutTime === null) {
            // Check-out process
            $currentTime = date('Y-m-d H:i:s');
            // Insert check-out record into the database
            $stmt = $conn->prepare("UPDATE attendance SET check_out_time = ? WHERE employee_id = ? AND check_out_time IS NULL");
            $stmt->bind_param("ss", $currentTime, $employeeId);
            if ($stmt->execute() === TRUE) {
                $message = "Check-out successful for Employee: " . $employeeId;
                header("Refresh:0");
            } else {
                $message = "Error during check-out for Employee: " . $employeeId . " Error: " . $conn->error;
                header("Refresh:0");
            }
        } else {
            $message = "The employee: " . $employeeId . " has already checked out or not checked in.";
            header("Refresh:0");
        }
    } else {
        $message = "No QR Code recognized.";
        header("Refresh:0");
    }
}

?>

<html>

<head>
    <title>ZSI QR · Attendance Tracker</title>
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="refresh" content="30">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="jsQR.js"></script>
</head>

<body>


    <div class="navbar">
        <div class="logo">
            <a href="/" style="display:flex;align-items:center;gap:10px;text-decoration:none;"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="3.5" y="3.5" width="2" height="2" fill="#2563eb" stroke="none"/><rect x="14.5" y="3.5" width="2" height="2" fill="#2563eb" stroke="none"/><rect x="3.5" y="14.5" width="2" height="2" fill="#2563eb" stroke="none"/><rect x="14" y="14" width="3" height="3" rx=".5" fill="#2563eb" stroke="none"/><rect x="18" y="14" width="3" height="3" rx=".5" fill="#2563eb" stroke="none"/><rect x="14" y="18" width="3" height="3" rx=".5" fill="#2563eb" stroke="none"/><rect x="18" y="18" width="3" height="3" rx=".5" fill="#2563eb" stroke="none"/></svg><span style="color:#fff;font-size:17px;font-weight:700;letter-spacing:.02em;">ZSI<span style="color:#2563eb;">QR</span></span></a>
        </div>
        <div class="menu-links"><span style="color:#fff;font-weight:700;font-size:15px;letter-spacing:.03em;">ZSI QR &mdash; Attendance Tracker</span></div>
        <div>
            <a class="back-button" href="/logout-app.php">Logout</a>
            <a class="back-button" href="/admin">Admin Area</a>
        </div>
    </div>

    <div class="page-content">

    <form method="post" action="" id="attendanceForm">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" id="employee_id" name="employee_id">
        <input type="hidden" id="action_type" name="action_type">
        <button type="button" id="scanQR">Scan QR Code</button>
        <input type="submit" name="checkin" id="btn_checkin" value="Check-in" style="display:none;">
        <input type="submit" name="checkout" id="btn_checkout" value="Check-out" style="display:none;">
    </form>

    <div id="popup" class="popup">
        <div class="popup-content">
            <span class="close">&times;</span>
            <p>Scanning QR code, please wait...</p>
            <canvas id="qr-canvas"></canvas>
            <button type="button" class="popup-checkin" id="popup-checkin" style="display:none;">Check-in</button>
            <button type="button" class="popup-checkout" id="popup-checkout" style="display:none;">Check-out</button>
        </div>
    </div>

    <?php
    $stmt = $conn->prepare("SELECT employee_id, check_in_time, check_out_time FROM attendance ORDER BY check_in_time DESC LIMIT 50");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h2>Attendance History</h2>";
        echo "<div class='table-wrap'><table>";
        echo "<thead><tr><th>Employee</th><th>Check-in</th><th>Check-out</th><th>Working Time</th><th>Status</th></tr></thead><tbody>";

        while ($row = $result->fetch_assoc()) {
            $employeeId = htmlspecialchars($row['employee_id']);
            $checkInTime = $row['check_in_time'] ?? '-';
            $checkOutTime = $row['check_out_time'];

            if ($checkOutTime !== null) {
                $totalTime = strtotime($checkOutTime) - strtotime($checkInTime);
                $hours = floor($totalTime / 3600);
                $minutes = floor(($totalTime % 3600) / 60);
                $workingTime = "$hours h $minutes min";
                $status = "<span style='background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;'>Done</span>";
            } else {
                $workingTime = '—';
                $checkOutTime = '—';
                $status = "<span style='background:#fef9c3;color:#a16207;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;'>Active</span>";
            }

            echo "<tr><td><strong>$employeeId</strong></td><td>$checkInTime</td><td>$checkOutTime</td><td>$workingTime</td><td>$status</td></tr>";
        }

        echo "</tbody></table></div>";
    } else {
        echo "<p style='text-align:center;color:#64748b;padding:40px;'>No attendance records yet.</p>";
    }
    ?>

    </div>
    <script>
        let video = document.createElement("video");
        let canvasElement = document.getElementById("qr-canvas");
        let canvas = canvasElement.getContext("2d");
        let scanned = false;

        function enableCamera() {
            navigator.mediaDevices.getUserMedia({
                video: { facingMode: "user" }
            }).then(function(stream) {
                video.srcObject = stream;
                video.setAttribute("playsinline", true);
                video.play();
                requestAnimationFrame(tick);
            });
        }

        function tick() {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvasElement.hidden = false;
                canvasElement.height = video.videoHeight;
                canvasElement.width = video.videoWidth;
                canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
                let imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
                let code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: "dontInvert",
                });
                if (code && !scanned) {
                    scanned = true;
                    document.getElementById('employee_id').value = code.data;
                    qrMessage.innerText = 'Welcome ' + code.data + ' — choose Check-in or Check-out';
                    document.getElementById('popup-checkin').style.display = 'inline-block';
                    document.getElementById('popup-checkout').style.display = 'inline-block';
                }
            }
            if (!scanned) requestAnimationFrame(tick);
        }

        document.getElementById('scanQR').addEventListener('click', function() {
            scanned = false;
            document.getElementById('popup-checkin').style.display = 'none';
            document.getElementById('popup-checkout').style.display = 'none';
            document.getElementById('popup').style.display = 'block';
            enableCamera();
        });

        document.getElementById('popup-checkin').addEventListener('click', function() {
            document.getElementById('btn_checkin').click();
        });

        document.getElementById('popup-checkout').addEventListener('click', function() {
            document.getElementById('btn_checkout').click();
        });

        document.getElementsByClassName('close')[0].addEventListener('click', function() {
            document.getElementById('popup').style.display = 'none';
            scanned = false;
            qrMessage.innerText = 'Please Scan your QR Code';
        });

        let qrMessage = document.querySelector('.popup-content p');
        qrMessage.innerText = 'Please Scan your QR Code';
    </script>
    <script>
        let message = <?php echo json_encode(isset($message) ? $message : ''); ?>;
        if (message) {
            alert(message);
        }
    </script>

</body>

<footer>
    <p>&copy; <?php echo date(chr(34)."Y".chr(34)); ?> ZSI QR · Attendance Tracker</p>
</footer>

</html>