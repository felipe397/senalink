<?php
session_start();

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar si los campos existen
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        die("Por favor complete todos los campos");
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
<?php
// Configuración de la conexión a la base de datos
define('DB_HOST', 'localhost:3307');
define('DB_NAME', 'senalink');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    // Crear conexión PDO
    $conexion = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        )
    );
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Función para cerrar la conexión
function cerrarConexion(&$conexion) {
    $conexion = null;
}
        $conexion = new \Dba\Connection();
        $conn = $conexion->connect();
        
        // Usar consulta preparada con MySQLi
        $sql = 'SELECT * FROM usuarios WHERE username = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];

            if ($user['role_id'] == 1) {
                header('Location: ../home/dashboard.php');
            } elseif ($user['role_id'] == 3) {
                header('Location: ../home/dashboard_empresa.php');
            } else {
                echo 'Acceso denegado';
            }
            exit();
        } else {
            echo "Usuario o contraseña incorrectos";
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>