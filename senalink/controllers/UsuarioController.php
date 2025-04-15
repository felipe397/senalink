<?php
// controllers/UsuarioController.php
// Controlador para CRUD de usuarios

require_once __DIR__ . '/../config/db.php';

class UsuarioController {
    public function listarUsuarios() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM usuarios");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearUsuario($nombre, $email, $password, $rol) {
        global $pdo;
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$nombre, $email, $hashedPassword, $rol]);
    }
}
?>
