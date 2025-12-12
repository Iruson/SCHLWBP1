<?php
include '../db.php';
$conexion = conexion();

// obtén el ID del PDF
$id = $_GET['id'];

// busca el PDF en la base de datos
$sql = "SELECT archivo, tipo FROM archivo WHERE id=$id";
$result = mysqli_query($conexion, $sql);
$datos = mysqli_fetch_assoc($result);

// cabeceras para que el navegador lo trate como PDF
header("Content-type: application/pdf");
header("Content-Disposition: inline; filename=documento.pdf"); 
echo $datos['archivo']; // <- aquí va el binario tal cual