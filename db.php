<?php
/* New connection (for reference):
 "mysql:host=tikg4.h.filess.io;port=3307;dbname=pcbuilding_unhappyask", "pcbuilding_unhappyask", "17a9b05f17daab08c8e2af8ed427c52647e47aae" */

try {
    $pdo = new PDO("mysql:host=localhost;dbname=pcbuilding", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    exit("Connection failed: " . $e->getMessage());
}
session_start();
?>