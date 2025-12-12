<?php


include("db.php");

$USUARIO=$_POST['usuario'];
$CONTRASEÑA=$_POST['contraseña'];



if ($USUARIO == "" || $CONTRASEÑA == "") {
   ?> <script> alert("complete todos los campos!"); </script>   <?php
}

$consulta = "SELECT * FROM usuarios WHERE usuario = '$USUARIO' AND contraseña = '$CONTRASEÑA'";

$resultado = mysqli_query($conn, $consulta);

$filas = mysqli_fetch_array($resultado);


    if ($filas['id_cargo'] == 1) {
        header("location:indexadmin.php");
        exit;
    } else if ($filas['id_cargo'] == 2) {
        header("location:index.php");
        exit;
    } else {
        echo "<script>alert('Usuario o contraseña incorrecta'); window.location.href='login.php';</script>";
        exit;}

mysqli_free_result($resultado);
mysqli_close($conn);