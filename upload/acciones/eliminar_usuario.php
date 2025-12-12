<?php
session_start();
include('../../db.php');
$conexion = conexion();

// 1. Verificación de acceso (solo admins)
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_cargo'] != 1) {
    header("Location: ../login.php");
    exit();
}

// 2. Obtener el ID del usuario a eliminar
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('❌ ID de usuario no válido.'); window.location='../gestionar_usuarios.php';</script>";
    exit();
}

$id_a_eliminar = $_GET['id'];

// 3. Preparar la eliminación de datos relacionados (Estudiante, si existe)
// Es una buena práctica eliminar primero los registros que dependen de la llave foránea
$sql_estudiante = "DELETE FROM estudiantes WHERE id_usuario = ?";
$stmt_estudiante = mysqli_prepare($conexion, $sql_estudiante);
mysqli_stmt_bind_param($stmt_estudiante, 'i', $id_a_eliminar);
mysqli_stmt_execute($stmt_estudiante);
mysqli_stmt_close($stmt_estudiante);


// 4. Preparar la eliminación del Usuario
$sql_usuario = "DELETE FROM usuarios WHERE Id = ?";
$stmt_usuario = mysqli_prepare($conexion, $sql_usuario);
mysqli_stmt_bind_param($stmt_usuario, 'i', $id_a_eliminar);

// 5. Ejecutar y notificar
if (mysqli_stmt_execute($stmt_usuario)) {
    // Éxito
    echo "<script>alert('✅ Usuario con ID {$id_a_eliminar} y datos relacionados eliminados correctamente.'); window.location='../gestionar_usuarios.php';</script>";
} else {
    // Error
    echo "<script>alert('❌ Error al eliminar usuario: " . mysqli_stmt_error($stmt_usuario) . "'); window.location='../gestionar_usuarios.php';</script>";
}

mysqli_stmt_close($stmt_usuario);
mysqli_close($conexion);
?>