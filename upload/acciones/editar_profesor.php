<?php
// acciones/editar_profesor.php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once('../../db.php'); 
$conexion = conexion();

// Restricción de acceso
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_cargo'] != 1) {
    header("Location: ../../login.php");
    exit();
}

// =========================================================================
// 1. PROCESAR ACTUALIZACIÓN (POST)
// =========================================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Recolectar datos del formulario
    $id_profesor = $_POST['id_profesor'];
    $nombre_profesor = $_POST['nombre_profesor']; // Esto irá a la tabla USUARIOS
    $especialidad = $_POST['especialidad'];
    $numero = $_POST['telefono']; // En tu BD se llama 'numero'
    $id_grado = $_POST['id_grado'];

    // PASO 1: Obtener el ID_USUARIO asociado a este profesor
    // Necesitamos saber qué usuario editar
    $sql_get_user = "SELECT id_usuario FROM profesores WHERE id_profesor = ?";
    $stmt_get = $conexion->prepare($sql_get_user);
    $stmt_get->bind_param("i", $id_profesor);
    $stmt_get->execute();
    $res_get = $stmt_get->get_result();
    
    if ($row_user = $res_get->fetch_assoc()) {
        $id_usuario = $row_user['id_usuario'];
    } else {
        die("Error: No se encontró el usuario asociado al profesor.");
    }
    $stmt_get->close();

    // PASO 2: Actualizar el NOMBRE en la tabla USUARIOS
    $sql_update_user = "UPDATE usuarios SET nombre = ? WHERE id = ?";
    $stmt_user = $conexion->prepare($sql_update_user);
    if ($stmt_user === false) { die("Error SQL Usuarios: " . $conexion->error); }
    
    $stmt_user->bind_param("si", $nombre_profesor, $id_usuario);
    $nombre_actualizado = $stmt_user->execute();
    $stmt_user->close();

    // PASO 3: Actualizar los DATOS ESPECÍFICOS en la tabla PROFESORES
    // (Quitamos 'nombre' de aquí porque no existe en esta tabla)
    $sql_update_prof = "UPDATE profesores SET 
        especialidad = ?, 
        numero = ?, 
        id_grado = ? 
        WHERE id_profesor = ?";
        
    $stmt_prof = $conexion->prepare($sql_update_prof);
    if ($stmt_prof === false) { die("Error SQL Profesores: " . $conexion->error); }
    
    // ssii: String(especialidad), String(numero), Int(id_grado), Int(id_profesor)
    $stmt_prof->bind_param("ssii", 
        $especialidad, 
        $numero, 
        $id_grado, 
        $id_profesor
    );

    $datos_actualizados = $stmt_prof->execute();
    $error_prof = $stmt_prof->error;
    $stmt_prof->close();

    // Verificar si todo salió bien
    if ($nombre_actualizado && $datos_actualizados) {
        $mensaje = "Profesor actualizado exitosamente (Nombre y Datos).";
        $redir = "window.location.href = '../ver_profesores.php';";
    } else {
        $mensaje = "Hubo un error al actualizar: " . $error_prof;
        $redir = "history.back();";
    }

    echo "<script>alert('$mensaje'); $redir</script>";
    exit();
} 

// =========================================================================
// 2. CARGAR DATOS EXISTENTES (GET)
// =========================================================================
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../ver_profesores.php");
    exit();
}

$id_profesor = $_GET['id'];

// Hacemos JOIN con usuarios para traer el nombre correcto al formulario
$sql_select = "SELECT P.id_profesor, P.especialidad, P.numero, P.id_grado, U.nombre 
               FROM profesores P 
               JOIN usuarios U ON P.id_usuario = U.id 
               WHERE P.id_profesor = ?";

$stmt_select = $conexion->prepare($sql_select);
$stmt_select->bind_param("i", $id_profesor);
$stmt_select->execute();
$resultado = $stmt_select->get_result();

if ($resultado->num_rows === 0) {
    header("Location: ../ver_profesores.php");
    exit();
}

$datos = $resultado->fetch_assoc();
$stmt_select->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Profesor</title>
    <link rel="stylesheet" href="../../css/bootstrap.css">
    <link rel="stylesheet" href="../../css/estilos_profesores.css"> 
</head>
<body>

<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 600px;">
        <div class="card-header text-center bg-primary text-white fw-bold fs-4 py-3">
            ✏️ Editar Datos del Profesor
        </div>

        <div class="card-body bg-light px-4 py-4">
            <form method="POST">
                <input type="hidden" name="id_profesor" value="<?php echo htmlspecialchars($datos['id_profesor']); ?>">
                
                <h3 class="text-primary">Datos del Profesor</h3>
                
                <div class="mb-3">
                    <label class="form-label">Nombre del Profesor (Usuario)</label>
                    <input type="text" name="nombre_profesor" class="form-control" value="<?php echo htmlspecialchars($datos['nombre']); ?>" required>
                    <small class="text-muted">Nota: Esto cambiará el nombre del usuario asociado.</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold text-success">Especialidad</label>
                    <select name="especialidad" class="form-select border-success shadow-sm">
                        <option value="">Ninguna</option>
                        <?php 
                        $esp = ["Informática", "Educación Física", "Inglés", "Portugués", "Música", "Matemáticas", "Lengua"];
                        foreach($esp as $e) {
                            $sel = ($datos['especialidad'] == $e) ? 'selected' : '';
                            echo "<option value='$e' $sel>$e</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($datos['numero'] ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-success">Grado Impartido</label>
                    <select name="id_grado" class="form-select border-success shadow-sm">
                        <option value="">Ninguno / Seleccione…</option>
                        <?php
                        // Asegúrate que la conexión no se haya cerrado arriba
                        // Si da error aquí, mueve el $conexion->close() al final absoluto del archivo
                        $g = mysqli_query($conexion, "SELECT id_grado, nombre FROM grados");
                        if ($g) {
                            while ($row = mysqli_fetch_assoc($g)) {
                                $selected = ($row['id_grado'] == $datos['id_grado']) ? 'selected' : '';
                                echo "<option value='{$row['id_grado']}' {$selected}>{$row['nombre']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-primary px-4">
                        💾 Guardar Cambios
                    </button>
                    <a href="../ver_profesores.php" class="btn btn-warning px-4">
                        ⬅️ Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
<?php $conexion->close(); ?>