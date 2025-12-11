<?php
// --------------------------------------------------
// File: config.php
// Purpose: Database + common helpers
// --------------------------------------------------

// Update DB credentials if necessary
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'if0_40463160_nextgen_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Stripe keys placeholder
define('STRIPE_SECRET_KEY', 'sk_test_xxx');
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_xxx');

session_start();

function getPDO(){
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $opts = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opts);
    }
    return $pdo;
}

function isLoggedIn(){
    return !empty($_SESSION['user']);
}

function requireLogin(){
    if (!isLoggedIn()){
        header('Location: login.php');
        exit;
    }
}

function requireRole($role){
    requireLogin();
    if ($_SESSION['user']['role'] !== $role) {
        http_response_code(403);
        echo 'Forbidden - insufficient role';
        exit;
    }
}

function flash_set($k,$v){ $_SESSION['_flash'][$k]=$v; }
function flash_get($k){ if(!empty($_SESSION['_flash'][$k])){ $v=$_SESSION['_flash'][$k]; unset($_SESSION['_flash'][$k]); return $v;} return null; }

// sanitize helper
function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

?>
