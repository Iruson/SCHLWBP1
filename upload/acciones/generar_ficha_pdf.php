<?php
// acciones/generar_ficha_pdf.php

// 1. Incluir Dompdf
// ASUME QUE LA CARPETA dompdf ESTÁ EN ../../dompdf (Raíz del proyecto)
require_once '../../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

if (session_status() === PHP_SESSION_NONE) session_start();
include_once('../../db.php');
$conexion = conexion();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../ver_estudiantes.php");
    exit();
}

$id_estudiante = $_GET['id'];

// --- 2. Extracción de Datos (Igual que la ficha de visualización) ---
$sql_ficha = "SELECT 
    E.*, 
    U.id AS cedula_representante,
    U.nombre AS nombre_representante,
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
$ficha = $resultado->fetch_assoc();
$stmt->close();
$conexion->close();

if (!$ficha) {
    header("Location: ../ver_estudiantes.php");
    exit();
}

// --- 3. Generar el Contenido HTML para el PDF ---
$nombre_archivo = "Ficha_" . $ficha['nombre'] . "_" . $ficha['apellido'] . ".pdf";

// Puedes usar CSS de Bootstrap, pero Dompdf lo procesa de forma limitada.
// Es mejor usar CSS incrustado simple.
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ficha de ' . htmlspecialchars($ficha['nombre'] . ' ' . $ficha['apellido']) . '</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; margin: 0; padding: 0; }
        .header { background-color: #007bff; color: white; padding: 15px; text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16pt; }
        .section { margin-bottom: 15px; border-left: 5px solid #17a2b8; padding-left: 10px; }
        .section h2 { color: #17a2b8; font-size: 13pt; border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-top: 10px; }
        .data-row { margin-bottom: 5px; }
        .data-label { font-weight: bold; color: #555; display: inline-block; width: 150px; }
        .half-col { width: 48%; display: inline-block; vertical-align: top; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Ficha de Registro del Estudiante</h1>
    </div>

    <div class="section" style="border-color: #007bff;">
        <h2>Datos del Representante</h2>
        <div class="data-row"><span class="data-label">Cédula (Usuario):</span> <strong>' . htmlspecialchars($ficha['cedula_representante']) . '</strong></div>
        <div class="data-row"><span class="data-label">Nombre Repr.:</span>' . htmlspecialchars($ficha['nombre_representante']) . '</div>
    </div>

     
<div class="section" style="border-color: #17a2b8;">
    <h2>Datos Personales del Estudiante</h2>
    

        
        <div style="float: left; width: auto;">
            <div class="data-row"><span class="data-label">Nombre Completo:</span> <strong>' . htmlspecialchars($ficha['nombre'] . ' ' . $ficha['apellido']) . '</strong></div>
            <div class="data-row"><span class="data-label">Edad:</span>' . htmlspecialchars($ficha['edad']) . ' años</div>
            <div class="data-row"><span class="data-label">F. Nacimiento:</span>' . htmlspecialchars($ficha['fecha_de_nac']) . '</div>
            <div class="data-row"><span class="data-label">Grado Asignado:</span> <strong>' . htmlspecialchars($ficha['nombre_grado'] ?? 'Sin Asignar') . '</strong></div>
            <div class="data-row"><span class="data-label">Alergias:</span>' . htmlspecialchars($ficha['alergia'] ?? 'Ninguna') . '</div>
            <div class="data-row"><span class="data-label">Patología Clínica:</span>' . htmlspecialchars($ficha['pat_clinica'] ?? 'Ninguna') . '</div>
        </div>
        
    </div>

</div>

        <div class="data-row"><span class="data-label">Nombre Completo:</span> <strong>' . htmlspecialchars($ficha['nombre'] . ' ' . $ficha['apellido']) . '</strong></div>
        <div class="data-row"><span class="data-label">Edad:</span>' . htmlspecialchars($ficha['edad']) . ' años</div>
        <div class="data-row"><span class="data-label">F. Nacimiento:</span>' . htmlspecialchars($ficha['fecha_de_nac']) . '</div>
        <div class="data-row"><span class="data-label">Grado Asignado:</span> <strong>' . htmlspecialchars($ficha['nombre_grado'] ?? 'Sin Asignar') . '</strong></div>
        <div class="data-row"><span class="data-label">Alergias:</span>' . htmlspecialchars($ficha['alergia'] ?? 'Ninguna') . '</div>
        <div class="data-row"><span class="data-label">Patología Clínica:</span>' . htmlspecialchars($ficha['pat_clinica'] ?? 'Ninguna') . '</div>
    </div>

    <div class="half-col">
        <div class="section" style="border-color: #ffc107;">
            <h2>Información de la Madre</h2>
            <div class="data-row"><span class="data-label">Nombre:</span>' . htmlspecialchars($ficha['nombre_mama'] . ' ' . $ficha['apellido_mama']) . '</div>
            <div class="data-row"><span class="data-label">Teléfono:</span>' . htmlspecialchars($ficha['telefono_mama']) . '</div>
            <div class="data-row"><span class="data-label">Dirección:</span>' . htmlspecialchars($ficha['direccion_mama']) . '</div>
            <div class="data-row"><span class="data-label">Trabajo:</span>' . htmlspecialchars($ficha['trabajo_mama']) . '</div>
        </div>
    </div>
    <div class="half-col" style="margin-left: 10px;">
        <div class="section" style="border-color: #28a745;">
            <h2>Información del Padre</h2>
            <div class="data-row"><span class="data-label">Nombre:</span>' . htmlspecialchars($ficha['nombre_papa'] . ' ' . $ficha['apellido_papa']) . '</div>
            <div class="data-row"><span class="data-label">Teléfono:</span>' . htmlspecialchars($ficha['telefono_papa']) . '</div>
            <div class="data-row"><span class="data-label">Dirección:</span>' . htmlspecialchars($ficha['direccion_papa']) . '</div>
            <div class="data-row"><span class="data-label">Trabajo:</span>' . htmlspecialchars($ficha['trabajo_papa']) . '</div>
        </div>
    </div>

    <div class="section" style="border-color: #dc3545; clear: both;">
        <h2>Autorizados y Emergencias</h2>
        <div class="half-col">
            <div class="data-row"><span class="data-label">Autorizado 1:</span>' . htmlspecialchars($ficha['personas_aut1']) . '</div>
            <div class="data-row"><span class="data-label">Autorizado 2:</span>' . htmlspecialchars($ficha['personas_aut2']) . '</div>
            <div class="data-row"><span class="data-label">Autorizado 3:</span>' . htmlspecialchars($ficha['personas_aut3']) . '</div>
        </div>
        <div class="half-col" style="margin-left: 10px;">
            <div class="data-row"><span class="data-label">Emergencia 1:</span>' . htmlspecialchars($ficha['num_emergencia1']) . '</div>
            <div class="data-row"><span class="data-label">Emergencia 2:</span>' . htmlspecialchars($ficha['num_emergencia2']) . '</div>
            <div class="data-row"><span class="data-label">Emergencia 3:</span>' . htmlspecialchars($ficha['num_emergencia3']) . '</div>
        </div>
    </div>

</body>
</html>
';

// --- 4. Inicializar y Generar PDF ---
$options = new Options();
$options->set('defaultFont', 'Arial');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// Renderizar HTML a PDF
$dompdf->render();

// Forzar la descarga
$dompdf->stream($nombre_archivo, ["Attachment" => true]);

exit();