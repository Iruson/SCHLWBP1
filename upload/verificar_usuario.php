<?php
// verificar_usuario.php
include_once('../db.php');
$conexion = conexion();

header('Content-Type: application/json');

if (isset($_GET['id']) ) {
    $id = $_GET['id'];
    
    $stmt = $conexion->prepare("SELECT nombre FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode(['exists' => true, 'name' => $row['nombre']]);
    } else {
        echo json_encode(['exists' => false]);
    }
    $stmt->close();
} else {
    echo json_encode(['exists' => false]);
}
$conexion->close();
?>