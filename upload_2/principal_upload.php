<?php
session_start();
include('../db.php');

$conexion = conexion();
//Verificar que el usuario tenga el rol adecuado (por ejemplo, rol 2 para usuarios regulares)
if ($_SESSION['id_cargo'] != 3) {
    header("Location: ../login.php");
    exit();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}




$id_usuario = $_SESSION['id_usuario'];

// Obtener solo los archivos que pertenecen a este usuario
$sql = "SELECT * FROM archivo WHERE id_usuario = '$id_usuario'";
$query = mysqli_query($conexion, $sql);

if (!$query) {
    die("Error en la consulta SQL: " . mysqli_error($conexion));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css2/upload2_main.css">
    <link rel="stylesheet" href="../css2/bootstrap.min.css">
    <title>Archivos Recibidos</title>
</head>
<body>

<div class="container mt-4" align="center">
    <div class="titular">
    <h2 class="archiveeero"><img src="../images/libreria-digital.png" style="width: 50px; height: auto;"> Archivos Recibidos</h2>
    <a href="../index.php" class="volveratras">VOLVER</a>
    </div>
    <br> <br> <br> <br>

    <?php if (mysqli_num_rows($query) === 0): ?>
        <div class="alert alert-info text-center">
            No tienes archivos recibidos todavía.
        </div>
    <?php else: ?>
        <table class="table table-striped table-sm align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Archivo</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody class="text-center" align="center">
                <?php 
                $contador = 0;
                while($datos = mysqli_fetch_assoc($query)) {
                    $contador++;
                    $id = $datos['id'];
                    $nombre = $datos['nombre'];
                    $categoria = $datos['categoria'];
                    $fecha = $datos['fecha'];
                    $archivo = $datos['archivo'];

                    // Mostrar icono según tipo
                    $valor = '';
                    switch($categoria) {
                        case 'jpg':
                        case 'jpeg':
                        case 'png':
                            $valor = "<img width='50' src='../images/imagenes_upload/imagenes.png'>";
                            break;
                        case 'pdf':
                            $valor = "<img width='50' src='../images/imagenes_upload/pdf.png'>";
                            break;
                        case 'xlsx':
                            $valor = "<img width='50' src='../images/imagenes_upload/excel.png'>";
                            break;
                        case 'docx':
                            $valor = "<img width='50' src='../images/imagenes_upload/word.png'>";
                            break;
                        case 'mp4':
                            $valor = "<img width='50' src='../images/imagenes_upload/video.png'>";
                            break;
                        case 'rar':
                            $valor = "<img width='50' src='../images/imagenes_upload/winrar.png'>";
                            break;
                        case 'mp3':
                            $valor = "<img width='50' src='../images/imagenes_upload/musica.png'>";
                            break;
                        default:
                            $valor = "<img width='50' src='../images/imagenes_upload/file.png'>";
                            break;
                    }
                ?>
                <tr>
                    <td><?= $contador; ?></td>
                    <td><?= htmlspecialchars($nombre); ?></td>
                    <td><?= htmlspecialchars($categoria); ?></td>
                    <td>
                        <a href="cargar.php?id=<?= $id; ?>" class="descarga">
                            <?= $valor; ?> DESCARGAR
                        </a>
                    </td>
                    <td><?= htmlspecialchars($fecha); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="../js/bootstrap.min.js"></script>
</body>
</html>