<?php 
session_start();
$id_cargo = $_SESSION['id_cargo'];
$id=$_POST['id'];
$nombre = $_POST['nombre_archivo'];
$archivo = $_FILES['archivo'];

$nombre_antiguo=$datos['nombre'];

include '../../db.php';
$conexion=conexion();
$datos=datos($conexion, $id);


if (($archivo['size']==0 && $nombre=='') || ($archivo['size']==0 && $nombre==$nombre_antiguo) ){ 
        header("location:../editar.php?id=$id");
}

if (($archivo['size']==0 && $nombre!='') || ($archivo['size']==0 && $nombre!==$nombre_antiguo) ){ 
      $query=editar_nombre($conexion, $id, $nombre);
      header("location:../editar.php?id=$id&&editar=success");
}

$tipo=$archivo['type'];
$categoria=explode('.',$archivo['name'])[1];

$fecha=date('y-m-d h:i:s');
 
$tmp_name=$archivo['tmp_name'];
$contenido_archivo=file_get_contents($tmp_name);
$archivoBLOB=addslashes($contenido_archivo); 



if(($archivo['size']>0 && $nombre=='') || ($archivo['size']>0 && $nombre==$nombre_antiguo)) {
 $query=editar_archivo($conexion, $id, $categoria, $tipo, $fecha, $archivoBLOB);
    header("location:../editar.php?id=$id&&editar=success");
}

if(($archivo['size']>0 && $nombre!=='') || ($archivo['size']>0 && $nombre!=$nombre_antiguo))  {
$query=editar($conexion, $id, $nombre, $categoria, $tipo, $fecha, $archivoBLOB);
header("location:../editar.php?id=$id&&editar=success");

}



?>