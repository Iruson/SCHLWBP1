<?php

session_start();


$id=$_GET['id'];



include '../../db.php';


$conexion=conexion();

$query=eliminar($conexion,$id);


if ($query) {
    
    if ($_SESSION['id_cargo'] === 1) { 
    
    header ('location:../principal_upload.php?eliminar=success');
    }elseif ($_SESSION['id_cargo'] === 2) {
            
            header ('location:../profesores_index.php?eliminar=success');
        }
} else {

     if ($_SESSION['id_cargo'] === 1) { 
    
    header ('location:../principal_upload.php?eliminar=error');
    }elseif ($_SESSION['id_cargo'] === 2) {
            
            header ('location:../profesores_index.php?eliminar=error');
        }
}




?>