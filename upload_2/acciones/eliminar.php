<?php

$id=$_GET['id'];



include '../../db.php';


$conexion=conexion();

$query=eliminar($conexion,$id);


if ($query) {
    header ('location:../principal_upload.php?eliminar=success');
} else {
    header ('location:../principal_upload.php?eliminar=error');
}




?>