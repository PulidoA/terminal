<?php
// /inc/db.php
$DB_HOST = '127.0.0.1';
$DB_PORT = '8889';
$DB_NAME = 'terminal_db';
$DB_USER = 'root';
$DB_PASS = 'root';

$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);

if (!$conn) {
    http_response_code(500);
    die("Error de conexión a la base de datos: " . htmlspecialchars(mysqli_connect_error()));
}

mysqli_set_charset($conn, "utf8mb4");
?>