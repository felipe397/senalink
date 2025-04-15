<?php
// config/db.php
// Conexión a la base de datos usando PDO

$host = 'localhost';     // Cambiar por el host de tu base de datos (ej. 127.0.0.1 o IP remota)
$dbname = 'senalink';    // Nombre de la base de datos
$user = 'root';          // Usuario de la base de datos
$pass = '';              // Contraseña del usuario de base de datos

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Este archivo se debe incluir en los controladores para usar la variable $pdo
?>
