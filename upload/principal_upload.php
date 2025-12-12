<?php
// ===============================
// INICIO DE SESIÓN Y VERIFICACIÓN
// ===============================
session_start();
include('../db.php');
$conexion = conexion();

// Verifica que haya sesión activa
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

// ===============================
// CONSULTA DE ARCHIVOS
// ===============================
$query = listar($conexion);
$contador = 0;
?>





<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - Archivos</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css2/uploado.css">
    <style>
        /* ===============================
           ESTILOS PERSONALIZADOS
        =============================== */
        body {
            background-color: #f7f9fb;
        }

        .navbar {
            background-color: #212529 !important;
        }

        .navbar-brand {
            color: #fff !important;
            font-weight: bold;
        }

        .container-main {
            margin-top: 40px;
        }

        h2.section-title {
            color: #333;
            margin-bottom: 25px;
            border-left: 5px solid #007bff;
            padding-left: 10px;
        }

        table img {
            display: block;
            margin: 0 auto;
        }

        footer {
            margin-top: 40px;
            background-color: #212529;
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        .table th, .table td {
            vertical-align: middle !important;
        }

        #searchInput {
            max-width: 400px;
        }
    </style>
</head>
<body>

<!-- ===============================
     NAVBAR
=============================== -->
<nav class="navbar navbar-expand-lg navbar-dark">
 <div class="container-fluid">
 <span class="navbar-brand">📁 Panel de Administración</span>
 
 <div class="d-flex">

             <div class="dropdown me-2" style="margin-left: 10px;">
           <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            ⚙️ Administrar Sistema
           </button>
           <ul class="dropdown-menu dropdown-menu-dark">
             <li><h6 class="dropdown-header">GESTIÓN DE USUARIOS</h6></li>
            <li><a class="dropdown-item" href="registrar_usuario.php">➕ Registrar Nuevo Usuario</a></li>
            <li><a class="dropdown-item" href="gestionar_usuarios.php">Ver Todos los Usuarios</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><h6 class="dropdown-header">GESTIÓN DE ESTUDIANTES</h6></li>
            <li><a class="dropdown-item" href="ver_estudiantes.php">Ver Estudiantes</a></li>
            <li><a class="dropdown-item" href="anadir_estudiante.php">➕ Añadir Estudiante a Cédula Existente</a></li> 
            <li><hr class="dropdown-divider"></li>
            <li><h6 class="dropdown-header">GESTIÓN DE PERSONAL</h6></li>
            <li><a class="dropdown-item" href="ver_profesores.php">Ver Profesores</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="btn btn-danger btn-sm" href="../login.php" 
           style="height: 50px; 
            width: 100px; 
            margin-left: 10px;
            font-size: 20px;
            text-align: auto;
            color: white;">Salir</a>
            </li> 
          </ul>
       </div>

        
 </div>
 </div>
</nav>

<!-- ===============================
     FORMULARIO DE SUBIDA
=============================== -->
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
          <label class="form-label">Cédula del usuario (ID)</label>
          <input type="number" class="form-control" name="id_usuario" required>
      </div>

      <br>
      <div class="d-flex justify-content-between">
          <button class="btn btn-primary">Subir Archivo</button>
      </div>
  </form>
</div>

<!-- ===============================
     TABLA DE ARCHIVOS CON BUSCADOR
=============================== -->
<div class="container mt-5">
  <h2 class="section-title">Archivos Subidos</h2>

  <!-- 🔍 BARRA DE BÚSQUEDA -->
  <div class="d-flex justify-content-between align-items-center mb-3">
      <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Buscar por nombre, usuario o cédula...">
      <span class="text-muted ms-2">Total: <?= mysqli_num_rows($query) ?> archivos</span>
  </div>

  <div class="table-responsive">
    <table class="table table-sm table-striped align-middle shadow-sm border" id="tablaArchivos">
      <thead class="table-dark text-center">
        <tr>
            <th>#</th>
            <th>Cédula</th>
            <th>Usuario</th>
            <th>Nombre</th>
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
            $id = $datos['id'];
            $nombre = $datos['nombre'];
            $categoria = $datos['categoria'];
            $fecha = $datos['fecha'];
            $archivo = $datos['archivo'];
            $id_usuario_archivo = $datos['id_usuario'];

            // Obtener nombre del usuario relacionado
            $sql_usuario = "SELECT usuario FROM usuarios WHERE id = '$id_usuario_archivo'";
            $res_usuario = mysqli_query($conexion, $sql_usuario);
            $usuario_info = mysqli_fetch_assoc($res_usuario);
            $nombre_usuario = $usuario_info ? $usuario_info['usuario'] : 'Desconocido';

            // Icono según tipo de archivo
            $iconos = [
                'jpg' => 'imagenes.png', 'jpeg' => 'imagenes.png', 'png' => 'imagenes.png',
                'pdf' => 'pdf.png', 'xlsx' => 'excel.png', 'docx' => 'word.png',
                'mp4' => 'video.png', 'rar' => 'winrar.png', 'mp3' => 'musica.png'
            ];
            $icono = isset($iconos[$categoria]) ? $iconos[$categoria] : 'file.png';
        ?>

        <tr>
          <td><?= $contador ?></td>
          <td><?= $id ?></td>
          <td><?= htmlspecialchars($nombre_usuario) ?></td>
          <td><?= htmlspecialchars($nombre) ?></td>
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

<!-- ===============================
     PIE DE PÁGINA
=============================== -->
<footer>
  <p>📚 Sistema de Gestión de Archivos — Administrador | <?= date('Y') ?></p>
</footer>

<script src="../js/bootstrap.bundle.min.js"></script>
<!-- ===============================
     SCRIPT DE BÚSQUEDA FILTRADA
=============================== -->
<script>
// 🔍 Filtra las filas de la tabla por texto
document.getElementById("searchInput").addEventListener("keyup", function() {
  const filtro = this.value.toLowerCase();
  const filas = document.querySelectorAll("#tablaArchivos tbody tr");

  filas.forEach(fila => {
    const textoFila = fila.textContent.toLowerCase();
    fila.style.display = textoFila.includes(filtro) ? "" : "none";
  });
});
</script>

</body>
</html>
