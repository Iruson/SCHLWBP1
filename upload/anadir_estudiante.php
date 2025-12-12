<?php
// anadir_estudiante.php

// =========================================================================
// 1. SESIÓN Y CONEXIÓN
// =========================================================================
if (session_status() === PHP_SESSION_NONE) session_start();
include_once('../db.php'); 
$conexion = conexion();

// Seguridad: Verificar que sea Administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_cargo'] != 1) {
    header("Location: ../login.php");
    exit();
}

// =========================================================================
// 2. PROCESAMIENTO POST (SOLO INSERTAR ESTUDIANTE)
// =========================================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // A. Recibir ID del Representante
    $id_usuario = $_POST['id_representante']; 

    // B. Verificar que el representante exista (Doble seguridad)
    $check_user = $conexion->prepare("SELECT id FROM usuarios WHERE id = ?");
    $check_user->bind_param("i", $id_usuario);
    $check_user->execute();
    if ($check_user->get_result()->num_rows === 0) {
        echo "<script>alert('Error: La cédula del representante no existe.'); window.history.back();</script>";
        exit();
    }
    $check_user->close();

    // C. Recibir Datos del Estudiante
    $est_nombre = $_POST['est_nombre'];
    $est_apellido = $_POST['est_apellido'];
    $est_edad = $_POST['est_edad'];
    $est_fecha_nac = $_POST['est_fecha_nac'];
    $id_grado = $_POST['id_grado'];
    $est_alergia = $_POST['est_alergia'];
    $est_patologia = $_POST['est_patologia'];

    // Info Madre
    $mama_nombre = $_POST['mama_nombre'];
    $mama_apellido = $_POST['mama_apellido'];
    $mama_direccion = $_POST['mama_direccion'];
    $mama_telefono = $_POST['mama_telefono'];
    $mama_trabajo = $_POST['mama_trabajo'];

    // Info Padre
    $papa_nombre = $_POST['papa_nombre'];
    $papa_apellido = $_POST['papa_apellido'];
    $papa_direccion = $_POST['papa_direccion'];
    $papa_telefono = $_POST['papa_telefono'];
    $papa_trabajo = $_POST['papa_trabajo'];

    // Autorizados y Emergencia
    $aut1 = $_POST['aut1']; $aut2 = $_POST['aut2']; $aut3 = $_POST['aut3']; 
    $num1 = $_POST['num1']; $num2 = $_POST['num2']; $num3 = $_POST['num3']; 

    // D. Manejo de Foto
    $foto_ruta = NULL;
    if (isset($_FILES['foto_estudiante']) && $_FILES['foto_estudiante']['error'] == 0) {
        $carpeta_destino_fisica = '../../images/fotos/'; // Ruta física
        $carpeta_destino_web = 'images/fotos/';          // Ruta web

        if (!is_dir($carpeta_destino_fisica)) {
            mkdir($carpeta_destino_fisica, 0777, true);
        }

        $nombre_base = basename($_FILES['foto_estudiante']['name']);
        $extension = pathinfo($nombre_base, PATHINFO_EXTENSION);
        // Nombre único: ID_REP_TIMESTAMP.ext
        $nombre_final = $id_usuario . '_' . time() . '.' . $extension;
        $ruta_completa_servidor = $carpeta_destino_fisica . $nombre_final;

        if (move_uploaded_file($_FILES['foto_estudiante']['tmp_name'], $ruta_completa_servidor)) {
            $foto_ruta = $carpeta_destino_web . $nombre_final; 
        }
    }

    // E. Insertar Estudiante
    // NOTA: Si corregiste la BD, esto permitirá múltiples estudiantes con el mismo id_usuario
    $sql_estudiante = "INSERT INTO estudiantes (id_usuario, nombre, apellido, edad, fecha_de_nac, 
    id_grado, alergia, pat_clinica, nombre_mama, apellido_mama, direccion_mama, telefono_mama, 
    trabajo_mama, nombre_papa, apellido_papa, telefono_papa, direccion_papa, trabajo_papa, 
    personas_aut1, personas_aut2, personas_aut3, num_emergencia1, num_emergencia2, num_emergencia3, foto_ruta) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt_estudiante = $conexion->prepare($sql_estudiante);
    
    // Binding (Orden exacto de las variables)
    $stmt_estudiante->bind_param("isississsssssssssssssssss", 
        $id_usuario, $est_nombre, $est_apellido, $est_edad, $est_fecha_nac, 
        $id_grado, $est_alergia, $est_patologia, 
        $mama_nombre, $mama_apellido, $mama_direccion, $mama_telefono, $mama_trabajo, 
        $papa_nombre, $papa_apellido, $papa_telefono, $papa_direccion, $papa_trabajo, 
        $aut1, $aut2, $aut3, $num1, $num2, $num3, $foto_ruta
    );

    if ($stmt_estudiante->execute()) {
        ?>
        <script>
            alert("¡Estudiante añadido exitosamente al Representante: <?php echo $id_usuario; ?>!");
            window.location.href = "ver_estudiantes.php"; 
        </script>
        <?php
    } else {
        // Aquí capturamos el error si la BD sigue bloqueada
        ?>
        <script>
            alert("Error al registrar: <?php echo $stmt_estudiante->error; ?> (Revisa que la tabla 'estudiantes' permita id_usuario duplicados)");
        </script>
        <?php
    }

    $stmt_estudiante->close();
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Estudiante a ID Existente</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css2/registro_style.css">
    <style>
        #mensaje_cedula { font-weight: bold; margin-top: 5px; font-size: 0.9em; }
        .valid-user { color: green; }
        .invalid-user { color: red; }
    </style>
</head>

<body>

<div class="container mt-5">
    
    <div class="card shadow-lg border-0 rounded-4 mx-auto">
        <div class="card-header text-center bg-warning text-dark fw-bold fs-4 py-3">
            🎓 Añadir Estudiante a Representante Existente
        </div>

        <div class="card-body bg-light px-4 py-4">
            
            <form method="POST" enctype="multipart/form-data" id="formEstudiante">

                <div class="alert alert-info">
                    <strong>Paso 1:</strong> Ingrese la Cédula del Representante que <b>ya está registrado</b>.
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-primary">Cédula del Representante (ID)</label>
                    <input type="number" name="id_representante" id="id_representante" class="form-control border-primary shadow-sm" required placeholder="Ingrese cédula aquí...">
                    <div id="mensaje_cedula"></div>
                </div>

                <hr>

                <h3 class="text-secondary mt-4">Información del Estudiante</h3>

                <div class="mb-3"><label class="form-label">Nombre</label><input type="text" name="est_nombre" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Apellido</label><input type="text" name="est_apellido" class="form-control" required></div>
                
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Edad</label><input type="number" name="est_edad" class="form-control" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Fecha de Nacimiento</label><input type="date" name="est_fecha_nac" class="form-control" required></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-success">Grado <span class="text-danger">*</span></label>
                    <select name="id_grado" class="form-select border-success shadow-sm" required>
                        <option value="">Seleccione el Grado...</option>
                        <?php
                        // Abrimos una conexión temporal solo para el select
                        $conexion_select = conexion(); 
                        $g = mysqli_query($conexion_select, "SELECT id_grado, nombre FROM grados");
                        if ($g && mysqli_num_rows($g) > 0) {
                            while ($row = mysqli_fetch_assoc($g)) {
                                echo "<option value='{$row['id_grado']}'>{$row['nombre']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Foto del Estudiante</label>
                    <input type="file" name="foto_estudiante" class="form-control" accept="image/*">
                </div>

                <div class="mb-3"><label class="form-label">Alergias</label><input type="text" name="est_alergia" class="form-control"></div>
                <div class="mb-3"><label class="form-label">Patología Clínica</label><input type="text" name="est_patologia" class="form-control"></div>

                <h3 class="text-secondary mt-4">Información de la Madre</h3>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Nombre</label><input type="text" name="mama_nombre" class="form-control"></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Apellido</label><input type="text" name="mama_apellido" class="form-control"></div>
                </div>
                <div class="mb-3"><label class="form-label">Dirección</label><input type="text" name="mama_direccion" class="form-control"></div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Teléfono</label><input type="text" name="mama_telefono" class="form-control"></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Trabajo</label><input type="text" name="mama_trabajo" class="form-control"></div>
                </div>

                <h3 class="text-secondary mt-4">Información del Padre</h3>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Nombre</label><input type="text" name="papa_nombre" class="form-control"></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Apellido</label><input type="text" name="papa_apellido" class="form-control"></div>
                </div>
                <div class="mb-3"><label class="form-label">Dirección</label><input type="text" name="papa_direccion" class="form-control"></div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Teléfono</label><input type="text" name="papa_telefono" class="form-control"></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Trabajo</label><input type="text" name="papa_trabajo" class="form-control"></div>
                </div>

                <h3 class="text-secondary mt-4">Personas Autorizadas</h3>
                <input type="text" name="aut1" class="form-control mb-2" placeholder="Persona 1">
                <input type="text" name="aut2" class="form-control mb-2" placeholder="Persona 2">
                <input type="text" name="aut3" class="form-control mb-2" placeholder="Persona 3">

                <h3 class="text-secondary mt-4">Números de Emergencia</h3>
                <input type="text" name="num1" class="form-control mb-2" placeholder="Número 1">
                <input type="text" name="num2" class="form-control mb-2" placeholder="Número 2">
                <input type="text" name="num3" class="form-control mb-2" placeholder="Número 3">

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success px-5 py-2 fw-bold" id="btnSubmit">
                        💾 Registrar Estudiante
                    </button>
                    <a href="principal_upload.php" class="btn btn-warning px-4 py-2 ms-2">
                        ⬅️ Volver
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById('id_representante').addEventListener('blur', function() {
    let id = this.value;
    let feedback = document.getElementById('mensaje_cedula');
    let btn = document.getElementById('btnSubmit');

    if (id.length > 5) {
        // Hacemos petición al archivo verificar_usuario.php
        fetch('verificar_usuario.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    feedback.innerHTML = '<span class="valid-user">✅ Usuario encontrado: ' + data.name + '</span>';
                    btn.disabled = false;
                } else {
                    feedback.innerHTML = '<span class="invalid-user">❌ Usuario NO encontrado. Verifique la cédula.</span>';
                    btn.disabled = true; // Desactivar botón si no existe
                }
            })
            .catch(error => {
                console.error('Error:', error);
                feedback.innerHTML = '<span class="text-warning">⚠️ Error verificando ID.</span>';
            });
    } else {
        feedback.innerHTML = '';
    }
});
</script>

</body>
</html>