<?php
include 'config.php';
include 'helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['table'] ?? null;

    if ($type) {
        $buildset = getBuildset($pdo);

        $buildset_array = parseBuildset($buildset);
        if (isset($buildset_array[$type])) {
            unset($buildset_array[$type]);
            $updatedBuildset = formatBuildset($buildset_array);
            saveBuildset($updatedBuildset, $pdo);
        }
    }
}

header('Location: ../home.php'); // adjusted path to root home.php
exit;
?>