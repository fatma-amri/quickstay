<?php
// Start session at the very beginning - Moved to index.php
// session_start();
// In config.php, after session_start()
// Load environment variables with a fallback
$envFilePath = __DIR__ . '/../.env';
$env = file_exists($envFilePath) ? parse_ini_file($envFilePath, true) : [];
define('DB_HOST', $env['DB_HOST'] ?? '127.0.0.1');
define('DB_PORT', $env['DB_PORT'] ?? '3307');
define('DB_NAME', $env['DB_NAME'] ?? 'quickstay');
define('DB_USER', $env['DB_USER'] ?? 'root');
define('DB_PASS', $env['DB_PASS'] ?? '');
define('DB_CHARSET', $env['DB_CHARSET'] ?? 'utf8mb4');

// Définir l'URL de base de l'application (ajuster si l'application n'est pas à la racine du serveur web)
define('BASE_URL', '/quickstay2/');

// Error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false // Improve security
        ]
    );
} catch (PDOException $e) {
    $_SESSION['flash_message'] = "Erreur de connexion à la base de données. Veuillez réessayer plus tard.";
    $_SESSION['flash_type'] = "error";
    redirect("index.php?route=error");
}

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function isLoggedIn(): bool {
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

function isAdmin(): bool {
    if (!isLoggedIn()) return false;
    $user = $_SESSION['user'];
    return is_object($user) ? $user->role === 'admin' : (is_array($user) && isset($user['role']) && $user['role'] === 'admin');
}

function isUser(): bool {
    if (!isLoggedIn()) return false;
    $user = $_SESSION['user'];
    return is_object($user) ? $user->role === 'user' : (is_array($user) && isset($user['role']) && $user['role'] === 'user');
}

function redirect(string $url): void {
    // Check for CSRF token in POST requests if applicable
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $_SESSION['flash_message'] = "Requête invalide. Veuillez réessayer.";
        $_SESSION['flash_type'] = "error";
        $url = "index.php?route=home";
    }
    header('Location: ' . $url);
    exit;
}

// Helper function for prepared statements
function dbQuery($sql, $params = []) {
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}
?>
