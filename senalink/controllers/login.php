<?php

use Dba\Connection;
require_once '../config/conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $conexion = new Connection();
        $conn = $conexion->connect();
        $sql = 'SELECT * FROM usuarios WHERE username = :username';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];

            if ($user['role_id'] == 1) {
                header('Location:../home/dashboard.php');
            }
            elseif ($user['role_id'] == 3) {
                header('Location:../home/dashboard_empresa.php');
            }
            else {
                echo 'Acceso denegado';
            }
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>