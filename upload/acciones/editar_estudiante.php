<?php
// acciones/editar_estudiante.php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once('../../db.php'); // Dos niveles arriba para alcanzar db.php
$conexion = conexion();

// Restricción de acceso
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_cargo'] != 1) {
    header("Location: ../../login.php");
    exit();
}

// =========================================================================
// 1. PROCESAR ACTUALIZACIÓN (Si el formulario se envió por POST)
// =========================================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Recolectar datos del formulario
    $id_estudiante = $_POST['id_estudiante'];
    $id_usuario = $_POST['id_usuario']; // ID del representante (para updatear usuario si es necesario)
    
    // Datos del Estudiante
    $est_nombre = $_POST['est_nombre'];
    $est_apellido = $_POST['est_apellido'];
    $est_edad = $_POST['est_edad'];
    $est_fecha_nac = $_POST['est_fecha_nac'];
    $id_grado = $_POST['id_grado'];
    $est_alergia = $_POST['est_alergia'];
    $est_patologia = $_POST['est_patologia'];

    // Datos de Familiares y Autorizados
    $mama_nombre = $_POST['mama_nombre'];
    $mama_apellido = $_POST['mama_apellido'];
    $mama_direccion = $_POST['mama_direccion'];
    $mama_telefono = $_POST['mama_telefono'];
    $mama_trabajo = $_POST['mama_trabajo'];
    $papa_nombre = $_POST['papa_nombre'];
    $papa_apellido = $_POST['papa_apellido'];
    $papa_direccion = $_POST['papa_direccion'];
    $papa_telefono = $_POST['papa_telefono'];
    $papa_trabajo = $_POST['papa_trabajo'];
    
    $aut1 = $_POST['aut1']; $aut2 = $_POST['aut2']; $aut3 = $_POST['aut3']; 
    $num1 = $_POST['num1']; $num2 = $_POST['num2']; $num3 = $_POST['num3']; 

    // Sentencia UPDATE para la tabla estudiantes
    $sql_update = "UPDATE estudiantes SET 
        nombre = ?, apellido = ?, edad = ?, fecha_de_nac = ?, id_grado = ?, alergia = ?, pat_clinica = ?,
        nombre_mama = ?, apellido_mama = ?, direccion_mama = ?, telefono_mama = ?, trabajo_mama = ?,
        nombre_papa = ?, apellido_papa = ?, direccion_papa = ?, telefono_papa = ?, trabajo_papa = ?,
        personas_aut1 = ?, personas_aut2 = ?, personas_aut3 = ?, num_emergencia1 = ?, num_emergencia2 = ?, num_emergencia3 = ?
        WHERE id_estudiante = ?";
        
    $stmt = $conexion->prepare($sql_update);
    
    // Cadena de tipos para 23 campos (sin contar el ID que va al final) + el ID al final (24 variables)
    // s, s, i, s, i, s, s, (estudiante/grado)
    // s, s, s, s, s, (mama)
    // s, s, s, s, s, (papa)
    // s, s, s, s, s, s (autorizados/numeros)
    // i (ID del estudiante)
    $stmt->bind_param("ssisissssssssssssssssssi", 
        $est_nombre, $est_apellido, $est_edad, $est_fecha_nac, $id_grado, $est_alergia, $est_patologia,
        $mama_nombre, $mama_apellido, $mama_direccion, $mama_telefono, $mama_trabajo,
        $papa_nombre, $papa_apellido, $papa_direccion, $papa_telefono, $papa_trabajo,
        $aut1, $aut2, $aut3, $num1, $num2, $num3,
        $id_estudiante
    );

    if ($stmt->execute()) {
        $mensaje = "Datos del estudiante actualizados exitosamente.";
    } else {
        $mensaje = "Error al actualizar los datos del estudiante: " . $stmt->error;
    }

    $stmt->close();
    
    // Redirigir y mostrar mensaje
    ?><script>
        alert("<?php echo $mensaje; ?>");
        window.location.href = "../ver_estudiantes.php"; // Redirección correcta
    </script><?php
    exit();
} 

// =========================================================================
// 2. CARGAR DATOS EXISTENTES (Si se accede por GET)
// =========================================================================
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../ver_estudiantes.php");
    exit();
}

$id_estudiante = $_GET['id'];

// Consulta para obtener todos los datos del estudiante
$sql_select = "SELECT * FROM estudiantes WHERE id_estudiante = ?";
$stmt_select = $conexion->prepare($sql_select);
$stmt_select->bind_param("i", $id_estudiante);
$stmt_select->execute();
$resultado = $stmt_select->get_result();

if ($resultado->num_rows === 0) {
    header("Location: ../ver_estudiantes.php");
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
    <title>Editar Estudiante</title>
    <link rel="stylesheet" href="../../css/bootstrap.css">
    <link rel="stylesheet" href="../../css2/registro_style.css"> 
</head>
<body>

<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: auto; max-height: auto;">
        <div class="card-header text-center bg-primary text-white fw-bold fs-4 py-3">
            ✏️ Editar Datos del Estudiante
        </div>

        <div class="card-body bg-light px-4 py-4">
            <form method="POST">
                <input type="hidden" name="id_estudiante" value="<?php echo htmlspecialchars($datos['id_estudiante']); ?>">
                <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($datos['id_usuario']); ?>">
                
                <h3 class="text-primary">Datos del Estudiante</h3>
                <div class="mb-3"><label class="form-label">Nombre</label><input type="text" name="est_nombre" class="form-control" value="<?php echo htmlspecialchars($datos['nombre']); ?>"></div>
                <div class="mb-3"><label class="form-label">Apellido</label><input type="text" name="est_apellido" class="form-control" value="<?php echo htmlspecialchars($datos['apellido']); ?>"></div>
                <div class="mb-3"><label class="form-label">Edad</label><input type="number" name="est_edad" class="form-control" value="<?php echo htmlspecialchars($datos['edad']); ?>"></div>
                <div class="mb-3"><label class="form-label">Fecha de nacimiento</label><input type="date" name="est_fecha_nac" class="form-control" value="<?php echo htmlspecialchars($datos['fecha_de_nac']); ?>"></div>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold text-success">Grado</label>
                    <select name="id_grado" class="form-select border-success shadow-sm">
                        <option value="">Seleccione…</option>
                        <?php
                        // Cargar y seleccionar el grado actual
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
                
                <div class="mb-3"><label class="form-label">Alergia</label><input type="text" name="est_alergia" class="form-control" value="<?php echo htmlspecialchars($datos['alergia']); ?>"></div>
                <div class="mb-3"><label class="form-label">Patología clínica</label><input type="text" name="est_patologia" class="form-control" value="<?php echo htmlspecialchars($datos['pat_clinica']); ?>"></div>

                <h3 class="text-secondary mt-4">Información de la madre</h3>
                <div class="mb-3"><label class="form-label">Nombre</label><input type="text" name="mama_nombre" class="form-control" value="<?php echo htmlspecialchars($datos['nombre_mama']); ?>"></div>
                <div class="mb-3"><label class="form-label">Apellido</label><input type="text" name="mama_apellido" class="form-control" value="<?php echo htmlspecialchars($datos['apellido_mama']); ?>"></div>
                <div class="mb-3"><label class="form-label">Dirección</label><input type="text" name="mama_direccion" class="form-control" value="<?php echo htmlspecialchars($datos['direccion_mama']); ?>"></div>
                <div class="mb-3"><label class="form-label">Teléfono</label><input type="text" name="mama_telefono" class="form-control" value="<?php echo htmlspecialchars($datos['telefono_mama']); ?>"></div>
                <div class="mb-3"><label class="form-label">Trabajo</label><input type="text" name="mama_trabajo" class="form-control" value="<?php echo htmlspecialchars($datos['trabajo_mama']); ?>"></div>

                <h3 class="text-secondary mt-4">Información del padre</h3>
                <div class="mb-3"><label class="form-label">Nombre</label><input type="text" name="papa_nombre" class="form-control" value="<?php echo htmlspecialchars($datos['nombre_papa']); ?>"></div>
                <div class="mb-3"><label class="form-label">Apellido</label><input type="text" name="papa_apellido" class="form-control" value="<?php echo htmlspecialchars($datos['apellido_papa']); ?>"></div>
                <div class="mb-3"><label class="form-label">Dirección</label><input type="text" name="papa_direccion" class="form-control" value="<?php echo htmlspecialchars($datos['direccion_papa']); ?>"></div>
                <div class="mb-3"><label class="form-label">Teléfono</label><input type="text" name="papa_telefono" class="form-control" value="<?php echo htmlspecialchars($datos['telefono_papa']); ?>"></div>
                <div class="mb-3"><label class="form-label">Trabajo</label><input type="text" name="papa_trabajo" class="form-control" value="<?php echo htmlspecialchars($datos['trabajo_papa']); ?>"></div>

                <h3 class="text-secondary mt-4">Personas autorizadas</h3>
                <input type="text" name="aut1" class="form-control mb-2" placeholder="Persona 1" value="<?php echo htmlspecialchars($datos['personas_aut1']); ?>">
                <input type="text" name="aut2" class="form-control mb-2" placeholder="Persona 2" value="<?php echo htmlspecialchars($datos['personas_aut2']); ?>">
                <input type="text" name="aut3" class="form-control mb-2" placeholder="Persona 3" value="<?php echo htmlspecialchars($datos['personas_aut3']); ?>">

                <h3 class="text-secondary mt-4">Números de emergencia</h3>
                <input type="text" name="num1" class="form-control mb-2" placeholder="Número 1" value="<?php echo htmlspecialchars($datos['num_emergencia1']); ?>">
                <input type="text" name="num2" class="form-control mb-2" placeholder="Número 2" value="<?php echo htmlspecialchars($datos['num_emergencia2']); ?>">
                <input type="text" name="num3" class="form-control mb-2" placeholder="Número 3" value="<?php echo htmlspecialchars($datos['num_emergencia3']); ?>">

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary px-4">
                        💾 Guardar Cambios
                    </button>
                    <a href="../ver_estudiantes.php" class="btn btn-warning px-4">
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