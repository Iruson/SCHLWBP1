<?php
// acciones/verificar_usuario.php
include('../../db.php'); // ruta según tu estructura
$conexion = conexion();

$id_usuario = $_POST['id_usuario'] ?? null;

if (!$id_usuario) {
    echo "no_existe";
    exit();
}

$sql = "SELECT id FROM usuarios WHERE id = '$id_usuario'";
$result = mysqli_query($conexion, $sql);

echo (mysqli_num_rows($result) > 0) ? "existe" : "no_existe";