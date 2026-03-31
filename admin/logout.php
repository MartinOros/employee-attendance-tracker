<?php

session_start();

$_SESSION = array();

if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], !empty($p['secure']), $p['httponly']);
}

session_destroy();

$year = date('Y');
?>
<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="utf-8">
    <title>ZSI QR · Odhlásenie administrátora</title>
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
            <div class="auth-logout-badge" aria-hidden="true">✓</div>
            <h1>Administrátor bol odhlásený</h1>
            <p class="auth-sub">Relácia bola ukončená. Pre pokračovanie sa znova prihlás alebo vráť do aplikácie pre zamestnancov.</p>
            <a class="auth-button" href="login.php">Prihlásiť sa znova</a>
            <a class="auth-button" href="/index.php" style="margin-top:12px;background:#475569;box-shadow:none;">Späť do aplikácie</a>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo htmlspecialchars($year, ENT_QUOTES, 'UTF-8'); ?> ZSI — Attendance Tracker</p>
    </footer>
</body>

</html>
