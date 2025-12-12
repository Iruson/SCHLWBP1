<?php
// eliminar_estudiante.php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once('../../db.php');
$conexion = conexion();

// Restricción: Solo el administrador (cargo 1) puede eliminar
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_cargo'] != 1) {
    header("Location: ../login.php");
    exit();
}

// 1. Obtener el ID del estudiante a eliminar desde la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../ver_estudiantes.php");
    exit();
}

$id_estudiante = $_GET['id'];

// 2. Preparar y Ejecutar la consulta DELETE
$sql_delete = "DELETE FROM estudiantes WHERE id_estudiante = ?";
$stmt = $conexion->prepare($sql_delete);

// 'i' para id_estudiante (entero)
$stmt->bind_param("i", $id_estudiante);

if ($stmt->execute()) {
    // Éxito: Redirigir de vuelta a la lista de estudiantes
    $mensaje = "El estudiante fue eliminado exitosamente.";
} else {
    // Fallo: Mostrar el error
    $mensaje = "Error al eliminar el estudiante: " . $stmt->error;
}

$stmt->close();
$conexion->close();

// Usar un script para mostrar el mensaje y redirigir
?>
<script>
    alert("<?php echo $mensaje; ?>");
    window.location.href = "../ver_estudiantes.php";
</script>
<?php
exit();