<?php 
$id = $_GET['id'];
include '../db.php';
$conexion = conexion();
$datos = datos($conexion, $id);

$nombre = $datos['nombre'];
$categoria = $datos['categoria'];
$titulo = $nombre . '.' . $categoria;
$tipo = $datos['tipo'];
$archivo = $datos['archivo'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar archivo</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css2/uploado.css"> <!-- 🎨 Estilos personalizados -->
</head>
<body>

<div class="container mt-4 mb-5">
    <h2 class="text-center mb-4">📁 Editar archivo</h2>
    
    <!-- 🔹 Formulario de edición -->
    <form method="POST" enctype="multipart/form-data" action="acciones/editar.php" class="shadow-lg p-4 bg-light rounded-4">

        <!-- ID oculto -->
        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <!-- Nombre del archivo -->
        <div class="mb-3">
            <label class="form-label fw-bold">Nombre del archivo</label>
            <input type="text" class="form-control" name="nombre_archivo" value="<?php echo $nombre; ?>" required>
        </div>

        <!-- Subir nuevo archivo -->
        <div class="mb-3">
            <label class="form-label fw-bold">Seleccionar un nuevo archivo (opcional)</label>
            <input type="file" class="form-control" name="archivo">
        </div>

        <!-- Botones -->
        <div class="text-center mt-4">
            <button class="btn btn-primary btn-lg me-2">💾 Actualizar archivo</button>
            <a href="profesores_index.php" class="btn btn-warning btn-lg">⬅️ Volver</a>
        </div>
    </form>

    <!-- 🔸 Sección de vista previa -->
    <div class="card shadow-lg mt-5 border-0">
        <div class="card-header text-center fw-bold bg-warning text-dark fs-5">
            Vista previa del archivo
        </div>
        <div class="card-body text-center">

            <?php 
            $valor = '';

            // Mostrar vista previa según tipo de archivo
            if ($categoria == 'pdf') {
                $valor = "<iframe src='ver_pdf.php?id=$id' width='100%' height='600px' class='rounded'></iframe>";
            } 
            elseif ($categoria == 'png' || $categoria == 'jpg' || $categoria == 'jpeg') {
                $valor = "<img width='500px' src='data:$tipo;base64,".base64_encode($archivo)."' class='rounded shadow-sm'>";
            } 
            elseif ($categoria == 'mp4' || $categoria == 'mp3') {
                $valor = "<video controls class='rounded shadow-sm' width='500px'>
                            <source src='data:$tipo;base64,".base64_encode($archivo)."'>
                          </video>";
            } 
            else {
                $valor = "<p class='text-muted'>No hay vista previa disponible para este tipo de archivo.</p>";
            }

            echo $valor;
            ?>
        </div>
    </div>
</div>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
