<?php
// Iniciar la sesión para manejar variables de sesión
session_start();
// Incluir el archivo de conexión a la base de datos
include('../Config/conexion.php'); // Ruta relativa actualizada a Config/conexion.php

// Verificar si la solicitud es de tipo POST (envío de formulario)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si la conexión a la base de datos está establecida
    if (!$conn) {
        // Si no hay conexión, muestra un mensaje de error y detiene la ejecución
        die("Error: No se pudo establecer la conexión con la base de datos.");
    }

    // Capturar y limpiar los datos del formulario de inicio de sesión
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');
    $rol = trim($_POST['rol'] ?? '');

    // Verificar que todos los campos requeridos estén completos
    if (empty($correo) || empty($contrasena) || empty($rol)) {
        // Si algún campo está vacío, muestra un mensaje de error y detiene la ejecución
        die("Error: Todos los campos son obligatorios.");
    }

    // Preparar la consulta SQL para validar el usuario según su rol
    $sql = "SELECT id, nombre, correo, contrasena, rol, estado FROM usuarios WHERE correo = ? AND rol = ?";
    // Prepara la consulta SQL
    $stmt = $conn->prepare($sql);
    // Verificar si la preparación de la consulta fue exitosa
    if (!$stmt) {
        // Si la preparación falla, muestra un mensaje de error y detiene la ejecución
        die("Error en la preparación de la consulta.");
    }

    // Vincular los parámetros a la consulta preparada
    $stmt->bind_param("ss", $correo, $rol);
    // Ejecutar la consulta
    $stmt->execute();
    // Obtener el resultado de la consulta
    $result = $stmt->get_result();
    // Obtener los datos del usuario como un array asociativo
    $user = $result->fetch_assoc();

    // Verificar si se encontró un usuario con las credenciales proporcionadas
    if ($user) {
        // Verificar si el usuario está activo
        if ($user['estado'] !== 'Activo') {
            // Si el usuario no está activo, muestra un mensaje de error y detiene la ejecución
            die("Error: Usuario suspendido o desactivado.");
        }

        // Verificar si la contraseña proporcionada coincide con la almacenada
        if (password_verify($contrasena, $user['contrasena'])) {
            // Guardar información del usuario en variables de sesión
            $_SESSION['id'] = $user['id'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['rol'] = $user['rol'];

            // Redireccionar al usuario según su rol
            switch ($user['rol']) {
                case 'SuperAdmin':
                    // Redirecciona al dashboard del SuperAdmin
                    header("Location: ../dashboards/dashboard_superadmin.php");
                    break;
                case 'AdminSENA':
                    // Redirecciona al dashboard del AdminSENA
                    header("Location: ../dashboards/dashboard_admin.php");
                    break;
                case 'empresas': // Coincide con la base de datos
                    // Redirecciona al dashboard de la empresa
                    header("Location: ../dashboards/dashboard_empresa.php");
                    break;
                default:
                    // Si el rol es desconocido, muestra un mensaje de error y detiene la ejecución
                    die("Error: Rol desconocido.");
            }
            exit;
        } else {
            // Mostrar mensaje de error si la contraseña es incorrecta
            echo "<script>alert('Contraseña incorrecta.'); window.location.href = '../public/login.php';</script>";
            exit;
        }
    } else {
        // Mostrar mensaje de error si el correo o rol son incorrectos
        echo "<script>alert('Correo o rol incorrectos.'); window.location.href = '../public/login.php';</script>";
        exit;
    }
}
?>
