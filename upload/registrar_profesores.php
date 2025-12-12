<?php
// registrar_profesor.php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once('../db.php');
$conexion = conexion();

// Restricción: Solo el administrador (cargo 1) puede registrar profesores.
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_cargo'] != 1) {
    header("Location: ../login.php");
    exit();
}

// =========================================================================
// BLOQUE DE PROCESAMIENTO POST PARA REGISTRO DE PROFESOR
// =========================================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // 1. Recolección y Preparación de Datos del USUARIO/PROFESOR
    $id_usuario = $_POST['id'];
    $nombre_usuario = $_POST['nombre'];
    $usuario = $_POST['usuario'];
    $contraseña_hasheada = password_hash($_POST['contraseña'], PASSWORD_BCRYPT);
    $id_cargo = 2; // Rol de Profesor
    
    // 2. Preparación de la Sentencia para USUARIOS
    $sql_usuario = "INSERT INTO usuarios (id, nombre, usuario, contraseña, id_cargo) VALUES (?, ?, ?, ?, ?)";
    $stmt_usuario = $conexion->prepare($sql_usuario);
    $stmt_usuario->bind_param("isssi", $id_usuario, $nombre_usuario, $usuario, $contraseña_hasheada, $id_cargo); 
    
    // =====================================================================
    // INSERCIÓN DE USUARIO/PROFESOR
    // =====================================================================
    if ($stmt_usuario->execute()) {
        
        $stmt_usuario->close();

        // -----------------------------------------------------------------
        // Inserción del PROFESOR (SOLO si el USUARIO fue exitoso)
        // -----------------------------------------------------------------
        
        // 3. Recolección de Datos Específicos del PROFESOR
        // Si el valor viene vacío (opcional), lo establecemos como NULL (si la columna en DB lo permite).
        $id_grado = empty($_POST['id_grado']) ? NULL : $_POST['id_grado']; 
        $especialidad = empty($_POST['especialidad']) ? NULL : $_POST['especialidad']; 
        $numero = $_POST['numero_profesor']; 

        // 4. Preparación de la Sentencia para PROFESORES
        // La consulta SQL funciona con NULLs, pero debemos usar 'i' y 's' en bind_param.
        // Si la columna 'id_grado' o 'especialidad' en tu DB *NO* acepta NULLs, esta lógica fallará
        $sql_profesor = "INSERT INTO profesores (id_usuario, id_grado, especialidad, numero) VALUES (?, ?, ?, ?)";
        $stmt_profesor = $conexion->prepare($sql_profesor);
        
        // Vinculación de Parámetros: id_usuario (i), id_grado (i o null), especialidad (s o null), numero (s)
        // Para manejar NULL, usaremos 'i' y 's', pero nos aseguramos de que el valor sea NULL/vacío si no existe.
        // NOTA: Si $id_grado es NULL, mysqli_stmt::bind_param requiere que sea 'i' si la columna es INT.
        // PHP no maneja bien NULLs directamente en bind_param. Si tu columna es INT, es mejor enviarlo como 0 si es opcional.
        
        // ALTERNATIVA: Si tu columna id_grado es INT y no permite NULLs, cámbiala a:
        // $id_grado = empty($_POST['id_grado']) ? 0 : (int)$_POST['id_grado'];
        // Y verifica que la tabla 'profesores' en el campo 'id_grado' permita 0 o NULL.
        
        // Usaremos el tipo de dato que permite la entrada:
        $stmt_profesor->bind_param("iiss", $id_usuario, $id_grado, $especialidad, $numero);
        
        
        // 5. Ejecución Única y Manejo de Errores para PROFESOR
        if ($stmt_profesor->execute()) {
            // Éxito total
            ?><script>
                alert("¡Profesor registrado exitosamente!");
                window.location.href = "principal_upload.php";</script><?php
        } else {
            // Fallo en la inserción del profesor (posiblemente Foreign Key)
            ?><script>
                alert("Error al registrar los datos del profesor: <?php echo $stmt_profesor->error; ?>");
                window.location.href = "registrar_profesor.php";</script><?php
        }
        
        $stmt_profesor->close();
        
    } else {
        // Fallo en la inserción del USUARIO (Duplicate Entry)
        ?><script>
            alert("Error al registrar el usuario: <?php echo $stmt_usuario->error; ?>");
            window.location.href = "registrar_profesor.php";</script><?php
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Profesor</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css2/registro_style.css">
</head>

<body>

<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 600px;">
        <div class="card-header text-center bg-success text-white fw-bold fs-4 py-3" style="color: #fff;">
            👨‍🏫 Registrar Nuevo Profesor
        </div>

        <div class="card-body bg-light px-4 py-4">
            <form method="POST">

                <h3 class="text-primary mt-2">Datos de Usuario</h3>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-primary">Cédula (ID)</label>
                    <input type="text" minlength="8" maxlength="8" name="id" class="form-control border-primary shadow-sm" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-primary">Nombre completo</label>
                    <input type="text" name="nombre" class="form-control border-primary shadow-sm" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-primary">Usuario</label>
                    <input type="text" name="usuario" class="form-control border-primary shadow-sm" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-primary">Contraseña</label>
                    <input type="password" name="contraseña" class="form-control border-primary shadow-sm" required>
                </div>
                
                <h3 class="text-success mt-4">Datos del Profesor</h3>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-success">Grado Asignado (Opcional)</label>
                    <select name="id_grado" id="id_grado" class="form-select border-success shadow-sm" required>
                        <option value="">N/A (Sin Asignar)</option> <?php
                        // Nota: El PHP para cargar grados se mantiene igual
                        $g = mysqli_query($conexion, "SELECT id_grado, nombre FROM grados");
                        if ($g && mysqli_num_rows($g) > 0) {
                            while ($row = mysqli_fetch_assoc($g)) {
                                echo "<option value='{$row['id_grado']}'>{$row['nombre']}</option>";
                            }
                        } else {
                            echo "<option value=''>No hay grados disponibles</option>";
                        }
                        ?>
                    </select>
                </div>
                            <br><br>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-success">Especialidad (Opcional)</label>
                    <select name="especialidad" class="form-select border-success shadow-sm">
                        <option value="">Ninguna</option> <option value="Informática">Informática</option>
                        <option value="Educación Física">Educación Física</option>
                        <option value="Inglés">Inglés</option>
                        <option value="Portugués">Portugués</option>
                        <option value="Música">Música</option> <option value="Matemáticas">Matemáticas</option>
                        <option value="Lengua">Lengua</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-success">Número de Contacto</label>
                    <input type="text" name="numero_profesor" class="form-control border-success shadow-sm" required>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success px-4">
                        💾 Registrar Profesor
                    </button>
                    <a href="principal_upload.php" class="btn btn-warning px-4">
                        ⬅️ Volver
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>