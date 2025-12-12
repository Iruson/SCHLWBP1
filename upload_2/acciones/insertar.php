<?php 
session_start();
#captura los datos
$nombre = $_POST['nombre_archivo'];
$archivo = $_FILES['archivo'];


#categoria
$tipo=$archivo['type'];
$categoria=explode('.',$archivo['name'])[1];


#fecha

$fecha=date('y-m-d h:i:s');

 
$tmp_name=$archivo['tmp_name'];
$contenido_archivo=file_get_contents($tmp_name);
$archivoBLOB=addslashes($contenido_archivo); 

include '../../db.php';

$conexion = conexion();
$query=insertar($conexion,$nombre,$categoria,$fecha,$tipo,$archivoBLOB);

if($query){
    if ($_SESSION['id_cargo'] === 1) {
        header('location:../principal_upload.php?insertar=success');
    } else ($_SESSION['id_cargo'] === 2) {
        header('location:../profesores_index.php?insertar=success');
    }
   
   
} else{
    echo "Error al insertar el archivo.";
}
    




?>



