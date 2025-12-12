<?php
// eliminar_profesor.php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once('../../db.php');
$conexion = conexion();

// Restricción: Solo el administrador (cargo 1) puede eliminar
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_cargo'] != 1) {
    header("Location: ../login.php");
    exit();
}

// 1. Obtener el ID del profesor a eliminar desde la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../ver_profesores.php");
    exit();
}

$id_profesor = $_GET['id'];

// Nota Importante: La tabla 'profesores' tiene una FK 'id_usuario'. 
// Si la clave foránea no tiene la propiedad CASCADE, primero deberás eliminar el profesor
// y luego el usuario (si ya no tiene otras dependencias, como ser representante).
// Aquí solo eliminaremos el registro de la tabla 'profesores'.

// 2. Preparar y Ejecutar la consulta DELETE
$sql_delete = "DELETE FROM profesores WHERE id_profesor = ?";
$stmt = $conexion->prepare($sql_delete);

// 'i' para id_profesor (entero)
$stmt->bind_param("i", $id_profesor);

if ($stmt->execute()) {
    // Éxito: Redirigir de vuelta a la lista de profesores
    $mensaje = "El profesor fue eliminado exitosamente.";
} else {
    // Fallo: Mostrar el error
    $mensaje = "Error al eliminar el profesor: " . $stmt->error;
}

$stmt->close();
$conexion->close();

// Usar un script para mostrar el mensaje y redirigir
?>
<script>
    alert("<?php echo $mensaje; ?>");
    window.location.href = "../ver_profesores.php";
</script>
<?php
exit();