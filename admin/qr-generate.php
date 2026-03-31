<?php

if (!file_exists('../db_connection.php')) {
    header("Location: ../install/install.php");
    exit;
}

// Start session
session_start();

// Check if the user is not authenticated or not from the "users" table, redirect them to the login page
if (
    !isset($_SESSION['authenticated']) ||
    !$_SESSION['authenticated'] ||
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'users'
) {
    header("Location: login.php");
    exit;
}

// Generate CSRF token if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check CSRF token when submitting the form
function checkCSRFToken()
{
    if (
        isset($_GET['csrf_token']) &&
        isset($_SESSION['csrf_token']) &&
        $_GET['csrf_token'] === $_SESSION['csrf_token']
    ) {
        return true;
    } else {
        return false;
    }
}

// Establish database connection
include "../db_connection.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use chillerlan\QRCode\{QRCode, QROptions};

// Lade die QR Code Generator Library
require_once "../vendor/autoload.php";
require_once "../vendor/chillerlan/php-qrcode/src/QROptions.php";
require_once "../vendor/chillerlan/php-qrcode/src/QRCode.php";

// Wenn das Formular abgeschickt wurde, generiere den QR-Code
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];

    // Prüfe, ob der Name nicht leer ist
    if (!empty($name)) {
        // Definiere den "Content Text" für den QR-Code
        $contentText = $name;

        // Pfad zum Speichern des QR-Codes (unique per employee name)
        $qrCodeImagePath = "qrcodepng/qr_" . preg_replace('/[^a-zA-Z0-9äöüßÄÖÜ_\-]/', '_', $name) . ".png";

        // Konfigurationsoptionen für den QR-Code
        $options = new chillerlan\QRCode\QROptions([
            'outputType' => 'png',
            'eccLevel' => QRCode::ECC_L,
            'scale' => intval(6),
        ]);

        // Generiere den QR-Code
        $qrCode = new QRCode($options);
        $qrCode->render($contentText, $qrCodeImagePath);
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>QR Code Generator</title>
    <link rel="stylesheet" type="text/css" href="../adminstyle.css">

    <style>
        /* Stil für die Visitenkarte */

        .container {
            padding: 20px;
        }

        h1 {
            text-align: center;
        }

        h2 {
            margin-top: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .success-message {
            display: none;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
        }

        .downloadModal {
            display: none;
            background-color: #f2f2f2;
            padding: 20px;
            border: 1px solid #ccc;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
        }

        .downloadModal p {
            margin: 0;
            margin-bottom: 10px;
        }

        .downloadModal button {
            cursor: pointer;
            margin-top: 10px;
        }

        .download-button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .print-button {
            background-color: #4CAF50;
            margin-top: 20px;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .card {
            max-width: 9cm;
            max-height: 5.4cm;
            margin: 20px auto;
            padding: 20px;
            border: 2px solid #ccc;
            border-radius: 5px;
        }

        .card h2 {
            font-size: 12px;
            /* Kleinerer Schriftgrad für den Namen */
        }

        .qr-code {
            max-width: 100%;
            /* QR-Code wird so groß wie möglich sein */
            margin: 10px auto;
            /* Etwas Abstand oben und unten */
        }

        /* Verstecke den Rest der Seite beim Drucken */
        @media print {
            body * {
                visibility: hidden;
            }

            .card,
            .card * {
                visibility: visible;
            }

            .qr-code {
                margin: 0;
            }

            /* Verstecke die Druck- und Herunterladen-Buttons beim Drucken */
            .print-button,
            .download-button {
                display: none;
            }
        }
    </style>
    </style>
    <!-- Skript für das Herunterladen der kompletten Visitenkarte als PNG-Datei -->
    <script src="html2canvas.min.js"></script>
</head>

<body>
    <div class="navbar">
        <div class="logo">
            <a href="/admin" style="display:flex;align-items:center;gap:10px;text-decoration:none;"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="3.5" y="3.5" width="2" height="2" fill="#2563eb" stroke="none"/><rect x="14.5" y="3.5" width="2" height="2" fill="#2563eb" stroke="none"/><rect x="3.5" y="14.5" width="2" height="2" fill="#2563eb" stroke="none"/><rect x="14" y="14" width="3" height="3" rx=".5" fill="#2563eb" stroke="none"/><rect x="18" y="14" width="3" height="3" rx=".5" fill="#2563eb" stroke="none"/><rect x="14" y="18" width="3" height="3" rx=".5" fill="#2563eb" stroke="none"/><rect x="18" y="18" width="3" height="3" rx=".5" fill="#2563eb" stroke="none"/></svg><span style="color:#fff;font-size:17px;font-weight:700;letter-spacing:.02em;">ZSI<span style="color:#2563eb;">QR</span></span></a>
        </div>
        <div class="menu-links"><span style="color:#fff;font-weight:700;font-size:15px;letter-spacing:.03em;">ZSI QR &mdash; Attendance Tracker</span></div>
        <div>
            <a class="back-button" href="/admin">Back to Dashboard</a>
            <a class="back-button" href="logout.php">Logout</a>
        </div>
    </div>

    <h1>QR Code Generator</h1>
    <form method="post">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required style="width: 500px;">
        <button type="submit" class="print-button">Generate QR-Code</button>
    </form>

    <?php
    // Zeige den QR-Code und den Namen auf einer Visitenkarte an, wenn er generiert wurde
    if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($name)) {
    ?>
        <!-- Verstecke den Rest der Seite beim Drucken -->
        <div class="card">
            <h2>Name: <?php echo $name; ?></h2>
            <div class="qr-code">
                <img src="<?php echo htmlspecialchars($qrCodeImagePath); ?>" alt="QR Code">
            </div>
            <!-- Button zum Drucken -->
            <button onclick="window.print()" class="print-button">Print</button>
            <!-- Button zum Herunterladen -->
            <button onclick="downloadVisitenkarte()" class="download-button">Download PNG File</button>
        </div>
        <!-- Skript für das Herunterladen der kompletten Visitenkarte als PNG-Datei -->
        <script>
            function downloadVisitenkarte() {
                const card = document.querySelector('.card');
                html2canvas(card).then(canvas => {
                    const link = document.createElement('a');
                    link.download = 'visitenkarte.png';
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                });
            }
        </script>
    <?php
    }
    ?>

    <h2>Generated QR Codes</h2>
    <?php
    $qrFiles = glob(__DIR__ . '/qrcodepng/qr_*.png');
    if (!empty($qrFiles)) {
        echo '<div style="display:flex;flex-wrap:wrap;gap:20px;margin:20px 0;">';
        foreach ($qrFiles as $file) {
            $filename = basename($file);
            $employeeName = str_replace(['qr_', '.png', '_'], ['', '', ' '], $filename);
            $employeeName = trim($employeeName);
            echo '<div style="text-align:center;border:1px solid #ccc;padding:10px;border-radius:5px;">';
            echo '<img src="qrcodepng/' . htmlspecialchars($filename) . '" style="width:120px;height:120px;" alt="' . htmlspecialchars($employeeName) . '">';
            echo '<p style="margin:5px 0;">' . htmlspecialchars($employeeName) . '</p>';
            echo '<a href="qrcodepng/' . htmlspecialchars($filename) . '" download class="download-button" style="font-size:12px;padding:3px 8px;">Download</a>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>No QR codes generated yet.</p>';
    }
    ?>

    <footer>
        <p>&copy; <?php echo date(chr(34)."Y".chr(34)); ?> ZSI QR · Attendance Tracker</p>
    </footer>

    <div class="success-message" id="success-message">Record deleted successfully</div>
    <div class="downloadModal" id="downloadModal">
        <p>Last generated Excel file:</p>
        <p id="downloadFileName"></p>
        <a id="downloadLink" href="#" class="download-button">Download</a>
        <button onclick="closeDownloadModal()">Close</button>
    </div>

</body>

</html>