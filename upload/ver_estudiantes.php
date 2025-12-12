<?php
// ver_estudiantes.php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once('../db.php');
$conexion = conexion();

// Restricción de acceso
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_cargo'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Consulta SQL para obtener datos combinados
// Obtenemos datos del estudiante, la cédula del usuario y el nombre del grado.
$sql_estudiantes = "SELECT 
    E.id_estudiante,
    E.nombre AS est_nombre,
    E.apellido AS est_apellido,
    U.id AS cedula_representante,
    G.nombre AS nombre_grado,
    E.edad,
    E.alergia
FROM 
    estudiantes E
JOIN 
    usuarios U ON E.id_usuario = U.id
JOIN 
    grados G ON E.id_grado = G.id_grado
ORDER BY 
    E.apellido ASC";

$resultado = mysqli_query($conexion, $sql_estudiantes);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Estudiantes</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
        <link rel="stylesheet" href="../css2/estilos_estudiantes.css">

</head>
<body>

<div class="container mt-5">
    <h2 align="center" style="font-family: fantasy;">Lista de Estudiantes Registrados</h2>
    <a href="principal_upload.php" class="btn btn-warning mb-3" style="margin-left: 10px;">⬅️ Volver</a>
    <a href="registrar_usuario.php" class="btn btn-success mb-3" style="margin-left: 10px;">➕ Registrar Nuevo Estudiante</a>
<br><br><br>
    <div class="search-box" >
            <input type="text" style="width: 80%;" id="buscar" placeholder="🔍 Buscar por nombre, usuario o cédula...">
        </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover border">
            <thead class="bg-primary text-white">
                <tr>
                    <th>ID Estudiante</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Cédula Repr.</th>
                    <th>Grado</th>
                    <th>Edad</th>
                    <th>Alergias</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultado && mysqli_num_rows($resultado) > 0) {
                    while ($fila = mysqli_fetch_assoc($resultado)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($fila['id_estudiante']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['est_nombre']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['est_apellido']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['cedula_representante']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['nombre_grado']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['edad']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['alergia'] ?? 'Ninguna') . "</td>";
                        echo '<td>
                            <a href="acciones/ver_ficha_estudiante.php?id=' . $fila['id_estudiante'] . '" class="btn btn-sm btn-info me-2">👁️ Ver Ficha</a>
                            <a href="acciones/editar_estudiante.php?id=' . $fila['id_estudiante'] . '" class="btn btn-sm btn-info me-2">✏️ Editar</a>
                            <a href="acciones/eliminar_estudiantes.php?id=' . $fila['id_estudiante'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'¿Estás seguro de eliminar este estudiante?\')">🗑️ Eliminar</a>
                        </td>';
                        echo "</tr>";
                    }
                } else {
                    echo '<tr><td colspan="8" class="text-center">No hay estudiantes registrados.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
document.getElementById("buscar").addEventListener("keyup", function() {
    // 1. Obtener el valor de búsqueda y convertirlo a minúsculas
    const filtro = this.value.toLowerCase().trim();
    
    // 2. Seleccionar todas las filas del cuerpo de la tabla
    // Asumiendo que tu tabla es la única en la página, o tiene un ID si hubiera varias.
    // Usaremos el selector general para tbody tr.
    const filas = document.querySelectorAll(".table tbody tr"); 

    // 3. Iterar sobre cada fila y aplicar el filtro
    filas.forEach(fila => {
        // Obtenemos todo el texto visible dentro de la fila (incluye todas las columnas: nombre, cédula, grado, etc.)
        const textoFila = fila.textContent.toLowerCase(); 
        
        // Si el texto de la fila incluye el filtro, mostrar la fila (display: "")
        // Si no, ocultarla (display: "none")
        fila.style.display = textoFila.includes(filtro) ? "" : "none";
    });
});
</script>
</body>
</html>