<?php
// acciones/ver_ficha_estudiante.php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once('../../db.php'); // Dos niveles arriba para alcanzar db.php
$conexion = conexion();

// Restricción de acceso
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_cargo'] != 1) {
    header("Location: ../../login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../ver_estudiantes.php");
    exit();
}

$id_estudiante = $_GET['id'];

// Consulta para obtener TODOS los datos del estudiante y su representante (usuario)
$sql_ficha = "SELECT 
    E.*, 
    U.id AS cedula_representante,
    U.nombre AS nombre_representante,
    U.usuario AS usuario_representante,
    G.nombre AS nombre_grado
    
FROM 
    estudiantes E
JOIN 
    usuarios U ON E.id_usuario = U.id
LEFT JOIN 
    grados G ON E.id_grado = G.id_grado
WHERE 
    E.id_estudiante = ?";
    
$stmt = $conexion->prepare($sql_ficha);
$stmt->bind_param("i", $id_estudiante);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    $conexion->close();
    header("Location: ../ver_estudiantes.php");
    exit();
}

$ficha = $resultado->fetch_assoc();
$stmt->close();
$conexion->close();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de <?php echo htmlspecialchars($ficha['nombre']) . ' ' . htmlspecialchars($ficha['apellido']); ?></title>
    <link rel="stylesheet" href="../../css/bootstrap.css">
    <style>
        .ficha-header { background-color: #f8f9fa; border-bottom: 3px solid #007bff; padding: 15px; }
        .ficha-section { border-left: 5px solid #17a2b8; padding-left: 15px; margin-top: 20px; }
        .data-label { font-weight: bold; color: #6c757d; }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="card shadow-lg">
        <div class="card-header ficha-header text-center">
            <h1>📄 Ficha del Estudiante</h1>
            
        </div>
        
        <div class="card-body">
            
            <div class="row">
                <div class="col-12 mb-4">
                    <h3 class="text-primary border-bottom pb-2">Datos del Representante</h3>
                    <div class="row">
                        <div class="col-md-6"><span class="data-label">Cédula (Usuario):</span> <strong><?php echo htmlspecialchars($ficha['cedula_representante']); ?></strong></div>
                        <div class="col-md-6"><span class="data-label">Nombre Repr.:</span> <?php echo htmlspecialchars($ficha['nombre_representante']); ?></div>
                    </div>
                </div>
                <div class="col-12 mb-4 ficha-section">
                    <h3 class="text-info border-bottom pb-2">Datos Personales del Estudiante</h3>
                    <div class="row">

                    <div class="card-body">
 
<?php if (!empty($ficha['foto_ruta'])): ?>
    
    <img src="/SCHLWBP01/<?php echo htmlspecialchars($ficha['foto_ruta']); ?>" 
        alt="Foto del Estudiante" 
        style="max-width: 200px; height: auto; border: 3px solid #007bff; border-radius: 5px; margin-bottom: 15px;">
        
<?php else: ?>
    <p class="text-muted">No hay foto disponible.</p> 
<?php endif; ?>
    
</div>
                        <div class="col-md-4"><span class="data-label">Nombre Completo:</span> <strong><?php echo htmlspecialchars($ficha['nombre'] . ' ' . $ficha['apellido']); ?></strong></div>
                        <div class="col-md-4"><span class="data-label">Edad:</span> <?php echo htmlspecialchars($ficha['edad']); ?> años</div>
                        <div class="col-md-4"><span class="data-label">F. Nacimiento:</span> <?php echo htmlspecialchars($ficha['fecha_de_nac']); ?></div>
                        <div class="col-md-4"><span class="data-label">Grado Asignado:</span> <strong><?php echo htmlspecialchars($ficha['nombre_grado'] ?? 'Sin Asignar'); ?></strong></div>
                        <div class="col-md-4"><span class="data-label">Alergias:</span> <?php echo htmlspecialchars($ficha['alergia'] ?? 'Ninguna'); ?></div>
                        <div class="col-md-4"><span class="data-label">Patología Clínica:</span> <?php echo htmlspecialchars($ficha['pat_clinica'] ?? 'Ninguna'); ?></div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4 ficha-section" style="border-color: #ffc107;">
                    <h4 class="text-warning border-bottom pb-2">Información de la Madre</h4>
                    <p><span class="data-label">Nombre:</span> <?php echo htmlspecialchars($ficha['nombre_mama']); ?></p>
                    <p><span class="data-label">Teléfono:</span> <?php echo htmlspecialchars($ficha['telefono_mama']); ?></p>
                    <p><span class="data-label">Dirección:</span> <?php echo htmlspecialchars($ficha['direccion_mama']); ?></p>
                    <p><span class="data-label">Trabajo:</span> <?php echo htmlspecialchars($ficha['trabajo_mama']); ?></p>
                </div>
                
                <div class="col-md-6 mb-4 ficha-section" style="border-color: #28a745;">
                    <h4 class="text-success border-bottom pb-2">Información del Padre</h4>
                    <p><span class="data-label">Nombre:</span> <?php echo htmlspecialchars($ficha['nombre_papa']); ?></p>
                    <p><span class="data-label">Teléfono:</span> <?php echo htmlspecialchars($ficha['telefono_papa']); ?></p>
                    <p><span class="data-label">Dirección:</span> <?php echo htmlspecialchars($ficha['direccion_papa']); ?></p>
                    <p><span class="data-label">Trabajo:</span> <?php echo htmlspecialchars($ficha['trabajo_papa']); ?></p>
                </div>

                <div class="col-12 ficha-section" style="border-color: #dc3545;">
                    <h4 class="text-danger border-bottom pb-2">Autorizados y Emergencias</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><span class="data-label">Persona Autorizada 1:</span> <?php echo htmlspecialchars($ficha['personas_aut1']); ?></p>
                            <p><span class="data-label">Persona Autorizada 2:</span> <?php echo htmlspecialchars($ficha['personas_aut2']); ?></p>
                            <p><span class="data-label">Persona Autorizada 3:</span> <?php echo htmlspecialchars($ficha['personas_aut3']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><span class="data-label">Teléfono Emergencia 1:</span> <?php echo htmlspecialchars($ficha['num_emergencia1']); ?></p>
                            <p><span class="data-label">Teléfono Emergencia 2:</span> <?php echo htmlspecialchars($ficha['num_emergencia2']); ?></p>
                            <p><span class="data-label">Teléfono Emergencia 3:</span> <?php echo htmlspecialchars($ficha['num_emergencia3']); ?></p>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        
        <div class="card-footer text-center">
            <a href="../ver_estudiantes.php" class="btn btn-warning me-3">⬅️ Volver a la Lista</a>
            <a href="generar_ficha_pdf.php?id=<?php echo htmlspecialchars($ficha['id_estudiante']); ?>" class="btn btn-danger">⬇️ Descargar Ficha PDF</a>
        </div>
    </div>
</div>

</body>
</html>