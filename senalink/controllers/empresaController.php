<?php
include('../Config/conexion.php'); // Incluir conexión centralizada


// Clase para manejar las empresas
class EmpresaController {
    private $conn;

    // Constructor para inicializar la conexión a la base de datos
    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Método para registrar una nueva empresa en el sistema
     *
     * @param string $nit Número de identificación tributaria de la empresa
     * @param string $nombre Nombre de la empresa
     * @param string $telefono Teléfono de la empresa
     * @param string $correo Correo electrónico de la empresa
     * @param string $direccion Dirección de la empresa
     * @param string $actividad_economica Actividad económica de la empresa
     * @param string $contrasena Contraseña de la empresa
     *
     * @return string Mensaje de éxito o error
     */
    public function registrarEmpresa($nit, $nombre, $telefono, $correo, $direccion, $actividad_economica, $contrasena) {
        try {
            // Verifica que todos los campos requeridos estén completos
            if (empty($nit) || empty($nombre) || empty($telefono) || empty($correo) || empty($direccion) || empty($actividad_economica) || empty($contrasena)) {
                // Si algún campo está vacío, lanza una excepción
                throw new Exception("Todos los campos son obligatorios.");
            }

            // Verifica si la empresa ya está registrada (por NIT o correo)
            $consulta = "SELECT id FROM empresas WHERE nit = ? OR correo = ?";
            // Prepara la consulta SQL
            $stmt = $this->conn->prepare($consulta);
            // Vincula los parámetros a la consulta
            $stmt->bind_param("ss", $nit, $correo);
            // Ejecuta la consulta
            $stmt->execute();
            // Obtiene el resultado de la consulta
            $resultado = $stmt->get_result();

            // Verifica si la consulta se ejecutó correctamente
            if (!$resultado) {
                throw new Exception("Error al verificar si la empresa ya está registrada: " . $this->conn->error);
            }

            // Si encuentra resultados, la empresa ya existe
            if ($resultado->num_rows > 0) {
                // Devuelve un mensaje de error
                return "Error: La empresa ya está registrada.";
            }

            // Encripta la contraseña antes de guardarla en la base de datos
            $contrasena = password_hash($contrasena, PASSWORD_DEFAULT);
            // Obtiene la fecha y hora actual
            $fecha_creacion = date("Y-m-d H:i:s");

            // Prepara la consulta SQL para insertar la nueva empresa
            $sql = "INSERT INTO empresas (nit, nombre, telefono, correo, direccion, actividad_economica, contrasena, estado, fecha_creacion) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'Activo', ?)";
            // Prepara la consulta SQL
            $stmt = $this->conn->prepare($sql);
            // Vincula los parámetros a la consulta
            $stmt->bind_param("ssssssss", $nit, $nombre, $telefono, $correo, $direccion, $actividad_economica, $contrasena, $fecha_creacion);

            // Ejecuta la consulta y verifica el resultado
            if ($stmt->execute()) {
                // Redirige a la página index.html con la ruta correcta
                header('Location: ../html/Empresa/index.html');
                exit;
            } else {
                // Si la consulta falla, lanza una excepción
                throw new Exception("Error al registrar la empresa: " . $stmt->error);
            }
        } catch (Exception $e) {
            // Captura y devuelve cualquier error que ocurra
            return "Error: " . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Crea una instancia de la clase EmpresaController
    $empresaController = new EmpresaController($conn);

    // Obtiene los datos del formulario
    $nit = $_POST['nit'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $actividad_economica = $_POST['actividad_economica'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    // Registra la empresa con los datos recibidos
    $resultado = $empresaController->registrarEmpresa($nit, $nombre, $telefono, $correo, $direccion, $actividad_economica, $contrasena);

    // Muestra el resultado o redirige según sea necesario
    echo $resultado;
} else {
    // Si no es una solicitud POST, mostrar un mensaje o redirigir
    echo "Método no permitido.";
}

// Cierra la conexión a la base de datos
$conn->close();
?>
