<?php 
$id=$_GET['id'];
include '../db.php';
$conexion=conexion();
$datos=datos($conexion, $id);
$nombre=$datos['nombre'];
$categoria=$datos['categoria'];
$titulo=$nombre.'.'.$categoria;
$tipo=$datos['tipo'];
$archivo=$datos['archivo'];

?>

<!DOCTYPE html>
<html lang="es">
<head>
        <link rel="stylesheet" href="../css/bootstrap.css" />

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>editar</title>
</head>
<body>

<div class="container">
   <form method="POST" class='m-auto w-50 mt-2 mb-2' enctype="multipart/form-data" action="acciones/editar.php">

<input type="hidden" name="id" value="<?php echo $id;?>">

    <h3 class="text-center"><?php echo $titulo; ?></h3>
        <div class='mb-2'>
        <label class="form-label"> Nombre del archivo </label>
        <input type="text" class='form-control form-control-sm' name="nombre_archivo" value="<?php echo $nombre; ?>">
        </div>
    
        <div class='mb-2'>
        <label class="form-label"> Seleccione un archivo para subir </label>
        <input type="file" class='form-control ' name="archivo">
        </div>
    <button  class="btn btn-primary btn-sm">Actualizar Archivo</button>
    <a class='btn btn-warning btn-sm' href="principal_upload.php">Regresar</a>
</form>

<div class="m-auto w-75 mt-2 text-center">

<?php 

$valor='';

if ($categoria == 'pdf') {
    $valor = "<iframe src='ver_pdf.php?id=$id' 
              width='100%' height='600px'></iframe>";
}

if ($categoria == 'png' || $categoria == 'jpg') {
    $valor = "<img width='400px' height='auto' src='data:".$tipo.";base64,".base64_encode($archivo)."'>";
}


if ($categoria == 'mp4' || $categoria == 'mp3') {
    $valor = "<video controls='true' class='m-auto' src='data:".$tipo.";base64,".base64_encode($archivo)."'></video>";
}



echo $valor;
?>



</div>


</div>

<script src="js/bootstrap.min.js"></script>

</body>

</html>