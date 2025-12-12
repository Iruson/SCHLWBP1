<?php
// registrar_usuario.php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once('../db.php');
$conexion = conexion();

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_cargo'] != 1) {
    header("Location: ../login.php");
    exit();
}

// =========================================================================
// BLOQUE ÚNICO DE PROCESAMIENTO POST (CORREGIDO)
// =========================================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // 1. Recolección y Preparación de Datos del USUARIO/REPRESENTANTE
    $id_usuario = $_POST['id'];
    $nombre_usuario = $_POST['nombre'];
    $usuario = $_POST['usuario'];
    $contraseña_hasheada = password_hash($_POST['contraseña'], PASSWORD_BCRYPT);
    $id_cargo = 3; 

    // 2. Preparación de la Sentencia para USUARIOS
    // CORRECCIÓN 1: El nombre de la columna en SQL debe ser 'id', no 'id_usuario' (según tu esquema)
    $sql_usuario = "INSERT INTO usuarios (id, nombre, usuario, contraseña, id_cargo) VALUES (?, ?, ?, ?, ?)";
    $stmt_usuario = $conexion->prepare($sql_usuario);
    
    // Se usa 'i' para id (entero)
    $stmt_usuario->bind_param("isssi", $id_usuario, $nombre_usuario, $usuario, $contraseña_hasheada, $id_cargo); 

    
    
$sql_verificar = "SELECT id FROM usuarios WHERE id = ?";
$stmt_verificar = $conexion->prepare($sql_verificar);

// Asumimos que 'id' es la columna donde guardas la cédula/ID
$stmt_verificar->bind_param("s", $id_usuario); 
$stmt_verificar->execute();
$resultado_verificar = $stmt_verificar->get_result();

if ($resultado_verificar->num_rows > 0) {
    // ¡La cédula ya está registrada! Detenemos la ejecución.
    ?>
    
    <script>
        alert("⚠️ La cédula ya está registrada. Por favor, utiliza una diferente.");
        window.location.href = "registrar_usuario.php";
    </script>
<?php

    
    // Redirigimos al usuario de vuelta al formulario para que corrija la cédula.
   
    exit();
}

$stmt_verificar->close();
    // =====================================================================
    // INSERCIÓN DE USUARIO/REPRESENTANTE
    // =====================================================================
    if ($stmt_usuario->execute()) {
        
        // Cierre de la sentencia del usuario (buena práctica)
        $stmt_usuario->close();


        $foto_ruta = NULL;

// el peo con la FOTO
if (isset($_FILES['foto_estudiante']) && $_FILES['foto_estudiante']['error'] == 0) {
    
    // Carpeta destino es 'fotos/' dentro de 'upload/' (que es donde estás parado con este script)
    $carpeta_destino = '../../images/fotos/'; 
    
    // Si la carpeta no existe, la crea (permisos 0777 para asegurar que funcione en XAMPP)
    if (!is_dir($carpeta_destino)) {
        mkdir($carpeta_destino, 0777, true);
    }

    $nombre_base = basename($_FILES['foto_estudiante']['name']);
    $extension = pathinfo($nombre_base, PATHINFO_EXTENSION);
    
    // Generamos un nombre único y seguro para evitar conflictos:
    // ID_USUARIO_TIMESTAMP.extension
    $nombre_final = $id_usuario . '_' . time() . '.' . $extension;
    $ruta_completa_servidor = $carpeta_destino . $nombre_final;

    // Intentar mover el archivo temporal a la ruta final
    if (move_uploaded_file($_FILES['foto_estudiante']['tmp_name'], $ruta_completa_servidor)) {
        // Esta es la ruta que guardaremos en la BD, relativa a la raíz del proyecto
        $foto_ruta = $ruta_completa_servidor; 
    }
}



        // -----------------------------------------------------------------
        // Inserción del ESTUDIANTE (SOLO si el USUARIO fue exitoso)
        // -----------------------------------------------------------------
        
        // 3. Recolección de Datos del ESTUDIANTE y FAMILIA
        $est_nombre = $_POST['est_nombre'];
        $est_apellido = $_POST['est_apellido'];
        $est_edad = $_POST['est_edad'];
        $est_fecha_nac = $_POST['est_fecha_nac'];
        $id_grado = $_POST['id_grado'];
        $est_alergia = $_POST['est_alergia'];
        $est_patologia = $_POST['est_patologia'];

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

        // 4. Preparación de la Sentencia para ESTUDIANTES
        // La columna id_usuario aquí es la llave foránea
        $sql_estudiante = "INSERT INTO estudiantes (id_usuario, nombre, apellido, edad, fecha_de_nac, 
        id_grado, alergia, pat_clinica, nombre_mama, apellido_mama, direccion_mama, telefono_mama, 
        trabajo_mama, nombre_papa, apellido_papa, telefono_papa, direccion_papa, trabajo_papa, 
        personas_aut1, personas_aut2, personas_aut3, num_emergencia1, num_emergencia2, num_emergencia3, foto_ruta) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


        $stmt_estudiante = $conexion->prepare($sql_estudiante);
        
        // CORRECCIÓN 2: Cadena de tipos corregida (i para id_usuario, est_edad, id_grado)
        // i, s, s, i, s, i, s, s, s, s, s, s, s, s, s, s, s, s, s, s, s, s, s, s (24 variables)
        $stmt_estudiante->bind_param("isississsssssssssssssssss", $id_usuario, $est_nombre, 
        $est_apellido, $est_edad, $est_fecha_nac, $id_grado, $est_alergia, $est_patologia, 
        $mama_nombre, $mama_apellido, $mama_direccion, $mama_telefono, $mama_trabajo, $papa_nombre, 
        $papa_apellido, $papa_telefono, $papa_direccion, $papa_trabajo, $aut1, $aut2, $aut3, $num1, $num2, $num3, $foto_ruta);
        
        
        // 5. Ejecución Única y Manejo de Errores para ESTUDIANTE
        if ($stmt_estudiante->execute()) {
            // Éxito total
            ?><script>
                alert("Usuario y datos del estudiante registrados exitosamente.");
                window.location.href = "principal_upload.php";</script><?php
        } else {
            // Fallo en la inserción del estudiante (posiblemente Foreign Key)
            ?><script>
                alert("Error al registrar los datos del estudiante: <?php echo $stmt_estudiante->error; ?>");
                window.location.href = "registrar_usuario.php";</script><?php
        }
        
        $stmt_estudiante->close();
        
    } else {
        // Fallo en la inserción del USUARIO (Aquí ocurre el 'Duplicate Entry')
        ?> <script>
            alert("Error al registrar el usuario: <?php echo $stmt_usuario->error; ?>");
            window.location.href = "registrar_usuario.php";</script><?php
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css2/registro_style.css">
    </head>

<body>

<div class="container mt-5">
 
    
    <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: auto; max-height: auto;">
        <div class="card-header text-center bg-primary text-white fw-bold fs-4 py-3">
            🧾 Registrar nuevo represenante
        </div>

        <div class="card-body bg-light px-4 py-4">
            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label fw-semibold text-primary">Cédula</label>
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

            
          <h3 class="text-secondary mt-4">Información del Estudiante</h3>

                    

                    <div class="mb-3"><label class="form-label">Nombres</label><input type="text" name="est_nombre" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Apellidos</label><input type="text" name="est_apellido" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Edad</label><input type="number" name="est_edad" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Fecha de nacimiento</label><input type="date" name="est_fecha_nac" class="form-control"></div>
    <br>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-success">Grado <span class="text-danger">*</span></label>
                        <select name="id_grado" class="form-select border-success shadow-sm" required>
                            <option value="">Seleccione…</option>
                            <?php
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

                <div class="mb-3">
                    <label class="form-label">Foto del Estudiante</label>
                    <input type="file" name="foto_estudiante" class="form-control" accept="image/*">
                </div>

                    <div class="mb-3"><label class="form-label">Alergia</label><input type="text" name="est_alergia" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Patología clínica</label><input type="text" name="est_patologia" class="form-control"></div>

                    <h3 class="text-secondary mt-4">Información de la madre</h3>
                    <div class="mb-3"><label class="form-label">Nombre</label><input type="text" name="mama_nombre" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Apellido</label><input type="text" name="mama_apellido" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Dirección</label><input type="text" name="mama_direccion" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Teléfono</label><input type="text" name="mama_telefono" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Trabajo</label><input type="text" name="mama_trabajo" class="form-control"></div>

                    <h3 class="text-secondary mt-4">Información del padre</h3>
                    <div class="mb-3"><label class="form-label">Nombre</label><input type="text" name="papa_nombre" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Apellido</label><input type="text" name="papa_apellido" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Dirección</label><input type="text" name="papa_direccion" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Teléfono</label><input type="text" name="papa_telefono" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Trabajo</label><input type="text" name="papa_trabajo" class="form-control"></div>

                    <h3 class="text-secondary mt-4">Personas autorizadas</h3>
                    <input type="text" name="aut1" class="form-control mb-2" placeholder="Persona 1">
                    <input type="text" name="aut2" class="form-control mb-2" placeholder="Persona 2">
                    <input type="text" name="aut3" class="form-control mb-2" placeholder="Persona 3">

                    <h3 class="text-secondary mt-4">Números de emergencia</h3>
                    <input type="text" name="num1" class="form-control mb-2" placeholder="Número 1">
                    <input type="text" name="num2" class="form-control mb-2" placeholder="Número 2">
                    <input type="text" name="num3" class="form-control mb-2" placeholder="Número 3">

                
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary px-4">
                        💾 Registrar Usuario y Datos
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