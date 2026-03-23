<?php
include 'config.php';
include 'helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['table'] ?? null;

    if ($type) {
        $userId = $_SESSION['user_id'] ?? null;
        $buildset = '';

        if ($userId) {
            $stmt = $pdo->prepare("SELECT buildset FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $buildset = $stmt->fetchColumn() ?: '';
        } else {
            $buildset = $_COOKIE['buildset'] ?? '';
        }

        $buildset_array = parseBuildset($buildset);
        if (isset($buildset_array[$type])) {
            unset($buildset_array[$type]);
            $updatedBuildset = formatBuildset($buildset_array);

            if ($userId) {
                $stmt = $pdo->prepare("UPDATE users SET buildset = ? WHERE user_id = ?");
                $stmt->execute([$updatedBuildset, $userId]);
            } else {
                setcookie('buildset', $updatedBuildset, time() + 3600 * 24 * 30, '/');
            }
        }
    }
}

header('Location: ../home.php'); // adjusted path to root home.php
exit;
?>