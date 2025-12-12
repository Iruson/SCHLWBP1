<?php 
session_start();

    $id=$_GET['id'];
    include "../db.php";
    $conexion=conexion();
    $datos=datos($conexion, $id);
    $id_usuario=$_SESSION['id_usuario'];
    $tipo=$datos['tipo'];
    $categoria=$datos['categoria'];
    $nombre=$datos['nombre'];
    $archivo=$datos['archivo'];
    $valor_tipo="Content-type:$tipo";
    header("Content-type:$tipo");
    header("Content-Disposition:inline;filename=$nombre.$categoria");
    echo $archivo;
?>