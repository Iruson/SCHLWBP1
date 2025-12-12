<?php
// generar_hash.php
$password_plana = 'asdf';
$hash_seguro = password_hash($password_plana, PASSWORD_DEFAULT);

echo "Contraseña plana: " . $password_plana . "<br>";
echo "Hash generado para la BD: " . $hash_seguro . "<br>";
?>