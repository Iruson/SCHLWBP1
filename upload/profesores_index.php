<?php
// principal_profesor.php
// ============================================================
// 1. INICIO DE SESIÓN Y CONEXIÓN
// ============================================================
session_start();
include('../db.php');
$conexion = conexion();

// Verifica que haya sesión activa
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

$id_usuario_profesor = $_SESSION['id_usuario'];

// Asumiremos que el Rol de Profesor es 2 (Según tu ejemplo)
if ($_SESSION['id_cargo'] != 2) {
    // Si no es profesor, lo redirige o muestra un error.
    header("Location: ../login.php"); 
    exit();
}





// ============================================================
// 2. OBTENER ID_GRADO DEL PROFESOR
// ============================================================
// Consultamos el grado asignado al profesor logueado
$sql_grado = "SELECT id_grado FROM profesores WHERE id_usuario = ?";
$stmt_grado = $conexion->prepare($sql_grado);
$stmt_grado->bind_param("i", $id_usuario_profesor);
$stmt_grado->execute();
$resultado_grado = $stmt_grado->get_result();

if ($resultado_grado->num_rows === 0) {
    // Si el usuario con id_cargo=2 no está registrado en la tabla profesores
    die("Error: Este usuario no tiene un grado asignado.");
}

$datos_profesor = $resultado_grado->fetch_assoc();
$id_grado_profesor = $datos_profesor['id_grado'];
$stmt_grado->close();



$sql_nombre_grado = "SELECT nombre FROM grados WHERE id_grado = ?";
$stmt_nombre = $conexion->prepare($sql_nombre_grado);
$stmt_nombre->bind_param("i", $id_grado_profesor);
$stmt_nombre->execute();
$resultado_nombre = $stmt_nombre->get_result();

if ($resultado_nombre->num_rows > 0) {
    $datos_grado = $resultado_nombre->fetch_assoc();
    $nombre_grado = htmlspecialchars($datos_grado['nombre']); // Nombre real del grado
} else {
    $nombre_grado = "Grado ID: " . $id_grado_profesor; // Fallback
}
$stmt_nombre->close();
// ============================================================
// 3. CONSULTA DE ARCHIVOS POR GRADO (JOIN TRES TABLAS)
// ============================================================
/*
LÓGICA:
1. Buscar todos los estudiantes que pertenezcan al $id_grado_profesor.
2. Obtener su id_usuario (que es la cédula del representante).
3. Obtener los archivos asociados a esos id_usuario.
*/
$sql_archivos_grado = "
    SELECT 
        a.id, a.nombre AS nombre_archivo, a.categoria, a.fecha, a.archivo, a.id_usuario,
        u.usuario, 
        e.nombre AS nombre_estudiante, e.apellido AS apellido_estudiante
    FROM archivo a
    JOIN usuarios u ON a.id_usuario = u.id 
    JOIN estudiantes e ON u.id = e.id_usuario 
    WHERE e.id_grado = ? 
    ORDER BY a.fecha DESC
";

$stmt_archivos = $conexion->prepare($sql_archivos_grado);
$stmt_archivos->bind_param("i", $id_grado_profesor);
$stmt_archivos->execute();
$query = $stmt_archivos->get_result();





// Usamos $query en el HTML para mostrar los resultados.
$contador = 0;
// Note: mysqli_num_rows($query) funcionará si $query es el resultado de get_result()
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Profesor - Archivos del Grado: <?= $nombre_grado?></title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css2/uploado.css">
    <style>
        /* Estilos CSS (Puedes reutilizar los del administrador, ajustando colores) */
        body { background-color: #f7f9fb; }
        .navbar { background-color: #28a745 !important; } /* Color verde para profesores */
        .navbar-brand { color: #fff !important; font-weight: bold; }
        .container-main { margin-top: 40px; }
        h2.section-title { color: #28a745; margin-bottom: 25px; border-left: 5px solid #28a745; padding-left: 10px; }
        .table th, .table td { vertical-align: middle !important; }
        #searchInput { max-width: 400px; }
        footer { margin-top: 40px; background-color: #212529; color: white; padding: 10px 0; text-align: center; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
   <div class="container-fluid">
       <span class="navbar-brand">📚 Profesor: <?= htmlspecialchars($_SESSION['nombre_usuario']) ?></span>
       <div class="d-flex">
           <span class="navbar-text me-3 text-white">
               Grado Asignado: <strong><?= $nombre_grado ?></strong>
           </span>
           <a class="btn btn-light" href="../login.php" style="background-color: #dc3545;                                      
            color: white; border-radius: 5px; padding: 5px 10px; text-decoration: none;
            margin-top: 10px;
            ">
               <i class="fas fa-sign-out-alt"></i> Salir
           </a>
       </div>
   </div>
</nav>

<div class="container container-main">
    <h2 class="section-title">Subir Documento</h2>
    <form method="POST" enctype="multipart/form-data" action="acciones/insertar.php" class="border rounded p-4 bg-white shadow-sm">
        
        <div class="mb-3">
            <label class="form-label">Nombre del archivo</label>
            <input type="text" class="form-control" name="nombre_archivo" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Seleccione un archivo</label>
            <input type="file" class="form-control" name="archivo" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Cédula del Representante (ID Estudiante de este Grado)</label>
            <input type="number" class="form-control" name="id_usuario" required placeholder="Solo IDs de su salón">
        </div>

        <br>
        <div class="d-flex justify-content-between">
            <button class="btn btn-success">Subir Archivo</button>
        </div>
    </form>
</div>

<div class="container mt-5">
    <h2 class="section-title">Archivos de Estudiantes del Grado <?= $nombre_grado ?></h2>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Buscar por nombre de estudiante o documento...">
        <span class="text-muted ms-2">Total: <?= mysqli_num_rows($query) ?> archivos</span>
    </div>

    <div class="table-responsive">
      <table class="table table-sm table-striped align-middle shadow-sm border" id="tablaArchivos">
          <thead class="table-success text-center">
              <tr>
                  <th>#</th>
                  <th>Estudiante</th> <th>Cédula Rep.</th>
                  <th>Nombre Archivo</th>
                  <th>Tipo</th>
                  <th>Archivo</th>
                  <th>Fecha</th>
                  <th>Acciones</th>
              </tr>
          </thead>

          <tbody class="text-center">
              <?php 
              while($datos = mysqli_fetch_assoc($query)) {
                  $contador++;
                  $id = $datos['id']; // id del archivo
                  $nombre_archivo_mostrar = $datos['nombre_archivo'];
                  $categoria = $datos['categoria'];
                  $fecha = $datos['fecha'];
                  $id_usuario_archivo = $datos['id_usuario'];
                  $nombre_estudiante = $datos['nombre_estudiante'] . ' ' . $datos['apellido_estudiante'];

                  // Lógica de íconos
                  $iconos = [
                      'jpg' => 'imagenes.png', 'jpeg' => 'imagenes.png', 'png' => 'imagenes.png',
                      'pdf' => 'pdf.png', 'xlsx' => 'excel.png', 'docx' => 'word.png',
                      'mp4' => 'video.png', 'rar' => 'winrar.png', 'mp3' => 'musica.png'
                  ];
                  $icono = isset($iconos[$categoria]) ? $iconos[$categoria] : 'file.png';
              ?>

              <tr>
                  <td><?= $contador ?></td>
                  <td class="text-start fw-bold"><?= htmlspecialchars($nombre_estudiante) ?></td>
                  <td><?= $id_usuario_archivo ?></td> <td><?= htmlspecialchars($nombre_archivo_mostrar) ?></td>
                  <td><?= strtoupper($categoria) ?></td>
                  <td>
                      <a href="cargar.php?id=<?= $id ?>" class="btn btn-outline-primary btn-sm">
                          <img width="30" src="../images/imagenes_upload/<?= $icono ?>"> Descargar
                      </a>
                  </td>
                  <td><?= $fecha ?></td>
                  <td>
                      <a class="btn btn-primary btn-sm" href="editar.php?id=<?= $id ?>">Editar</a>
                      <a class="btn btn-danger btn-sm" href="acciones/eliminar.php?id=<?= $id ?>">Eliminar</a>
                  </td>
              </tr>

              <?php } ?>
          </tbody>
      </table>
    </div>
</div>

<footer>
    <p>📚 Sistema de Gestión de Archivos — Profesor | <?= date('Y') ?></p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById("searchInput").addEventListener("keyup", function() {
    const filtro = this.value.toLowerCase();
    const filas = document.querySelectorAll("#tablaArchivos tbody tr");

    filas.forEach(fila => {
        // Buscamos en todo el texto de la fila
        const textoFila = fila.textContent.toLowerCase(); 
        fila.style.display = textoFila.includes(filtro) ? "" : "none";
    });
});
</script>

</body>
</html>