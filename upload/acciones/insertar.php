<?php
session_start();
include('../../db.php');
$conexion = conexion();

// Captura de datos del formulario
$nombre = $_POST['nombre_archivo'];
$archivo = $_FILES['archivo'];
$id_usuario = $_POST['id_usuario']; // Cedula o id manual que puso el admin

// Validar si ese id_usuario existe en la base de datos
$sql = "SELECT * FROM usuarios WHERE id = '$id_usuario'";
$result = mysqli_query($conexion, $sql);

if (mysqli_num_rows($result) == 0) {
    // No existe esa cédula / id
    echo "<script>
        alert('Cédula no encontrada');
        window.location.href='../principal_upload.php';
    </script>";
    exit();
}

// Si existe, seguimos con la subida del archivo
$tipo = $archivo['type'];
$categoria = pathinfo($archivo['name'], PATHINFO_EXTENSION);
$fecha = date('Y-m-d H:i:s');

$tmp_name = $archivo['tmp_name'];
$contenido_archivo = file_get_contents($tmp_name);
$archivoBLOB = addslashes($contenido_archivo);

// Insertamos usando la función del db.php
$query = insertar($conexion, $id_usuario, $nombre, $categoria, $fecha, $tipo, $archivoBLOB);


if($query){
    if ($_SESSION['id_cargo'] === 1) {
        header('location:../principal_upload.php?insertar=success');
    } elseif ($_SESSION['id_cargo'] === 2) {
        header('location:../profesores_index.php?insertar=success');
    }

} elseif(!$query){
    if ($_SESSION['id_cargo'] === 1) {
        header('location:../principal_upload.php?insertar=error');
    } elseif ($_SESSION['id_cargo'] === 2) {
        header('location:../profesores_index.php?insertar=error');
    }
}
?>
