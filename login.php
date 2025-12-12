<?php
/**
 * ============================================================
 * 🔹 ARCHIVO: login.php
 * 📋 Descripción:
 * Sistema de inicio de sesión seguro usando sentencias preparadas
 * y verificación de hash de contraseña (password_verify).
 * ============================================================
 */

session_start();
// Asegúrate de que tu archivo db.php use mysqli_connect y la conexión esté abierta
include 'db.php'; 

$conexion = conexion();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Obtener datos del formulario
    $usuario_ingresado = $_POST['usuario'];
    $contraseña_ingresada = $_POST['contraseña'];

    // 2. Sentencia preparada: Buscamos al usuario por su nombre de usuario.
    // **USAMOS SENTENCIAS PREPARADAS POR SEGURIDAD (ANTI-SQL Injection)**
    $sql = "SELECT Id, nombre, id_cargo, contraseña, creditos FROM usuarios WHERE usuario = ?";
    
    // Preparamos la sentencia
    if ($stmt = $conexion->prepare($sql)) {
        
        // Vinculamos el parámetro 's' = string
        $stmt->bind_param("s", $usuario_ingresado);
        
        // Ejecutamos la consulta
        $stmt->execute();
        
        // Obtenemos el resultado
        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 1) {
            $usuario = $resultado->fetch_assoc();
            
            // 3. Verificamos la Contraseña hasheada
            // Comparamos la contraseña ingresada con el hash guardado en la BD
            if (password_verify($contraseña_ingresada, $usuario['contraseña'])) {
                
                // === CREDENCIALES CORRECTAS ===
                
                // Guardamos los datos del usuario en la sesión
                $_SESSION['id_usuario'] = $usuario['Id']; 
                $_SESSION['nombre_usuario'] = $usuario['nombre'];
                $_SESSION['id_cargo'] = $usuario['id_cargo'];
                $_SESSION['creditos'] = $usuario['creditos'];

                // Redirigimos según su rol
                if ($usuario['id_cargo'] == 1) {
                    header("Location: upload/principal_upload.php"); // Admin
                } elseif ($usuario['id_cargo'] == 2) {
                    header("Location: upload/profesores_index.php"); // Profesor
                } elseif ($usuario['id_cargo'] == 3) {
                    header("Location: index.php"); // Representante
                } else {
                    echo "<script>alert('⚠️ Rol de usuario desconocido');</script>";
                }
                exit();
                
            } else {
                // Falla la verificación del hash
                echo "<script>alert('⚠️ Usuario o contraseña incorrectos');</script>";
            }
        } else {
            // Usuario no encontrado
            echo "<script>alert('⚠️ Usuario o contraseña incorrectos');</script>";
        }
        $stmt->close();
    } else {
        // Error en la preparación de la consulta
        echo "<script>alert('⚠️ Error interno del sistema. Intente más tarde.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Ingreso - UEP Luisa Cáceres de Arismendi</title>
    <link rel="stylesheet" href="css2/login_style.css" />
</head>

<body>
  <div class="contenedor">
      <h1 class="titulo" style="font-family: verdana; color:#F5EE20;" ><span style="color:white;">UEP</span> LUISA CÁCERES DE ARISMENDI</h1>
      <form action="login.php" method="post" id="formLogin" class="fade-in">
          <h1 style="font-family: fantasy;">Ingresar</h1>
          <input type="text" placeholder="Usuario" name="usuario" id="usuario" required />
          <input type="password" placeholder="Contraseña" name="contraseña" id="contraseña" required />
          <button name="ingresar" id="ingresar" type="submit">Ingresar</button>
      </form>
  </div>
</body>
</html>