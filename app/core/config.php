// Simple .env loader
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
    }
}
loadEnv(__DIR__ . '/../../.env');

try {
    $host = getenv('DB_HOST') ?: 'localhost';
    $dbname = getenv('DB_NAME') ?: 'pcbuilding';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';

    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    exit("Connection failed: " . $e->getMessage());
}

session_start();

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    
    if (!$stmt->fetchColumn()) {
        session_unset();
        session_destroy();
        echo "<script>location.reload();</script>";
        exit();
    }
}
?>