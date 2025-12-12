
<?php
// ver_profesores.php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once('../db.php');
$conexion = conexion();

// Restricción de acceso
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_cargo'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Consulta SQL para obtener datos combinados
// Obtenemos los datos del profesor, y los enlazamos con los datos del usuario (nombre, usuario)
// y el nombre del grado.
$sql_profesores = "SELECT 
    P.id_profesor,
    U.id AS cedula_usuario,
    U.nombre AS nombre_usuario,
    G.nombre AS nombre_grado,
    P.especialidad,
    P.numero
FROM 
    profesores P
JOIN 
    usuarios U ON P.id_usuario = U.id
LEFT JOIN 
    grados G ON P.id_grado = G.id_grado"; // LEFT JOIN para grados opcionales

$resultado = mysqli_query($conexion, $sql_profesores);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Profesores</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css2/estilos_profesores.css">
    </head>
<body>

<div class="container mt-5">
    <h2 align="center" style="font-family: fantasy;">📝 Lista de Profesores Registrados</h2>
    <a href="principal_upload.php" class="btn btn-warning mb-3" style="margin-left: 10px;">⬅️ Volver</a>
    <a href="registrar_profesores.php" class="btn btn-success mb-3" style="margin-left: 10px;">➕ Registrar Nuevo Profesor</a>
    <br><br><br>
    <div class="search-box">
            <input type="text" style="width: 80%;" id="buscar" placeholder="🔍 Buscar por nombre, usuario o cédula...">
        </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover border">
            <thead class="bg-success text-white">
                <tr>
                    <th>ID Profesor</th>
                    <th>Cédula (Usuario)</th>
                    <th>Nombre de Usuario</th>
                    <th>Grado Asignado</th>
                    <th>Especialidad</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultado && mysqli_num_rows($resultado) > 0) {
                    while ($fila = mysqli_fetch_assoc($resultado)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($fila['id_profesor']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['cedula_usuario']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['nombre_usuario']) . "</td>";
                        // Muestra N/A si el grado es NULL (debido al LEFT JOIN)
                        echo "<td>" . htmlspecialchars($fila['nombre_grado'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($fila['especialidad'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($fila['numero']) . "</td>";
                        echo '<td>
                            <a href="acciones/editar_profesor.php?id=' . $fila['id_profesor'] . '" class="btn btn-sm btn-info me-2">✏️ Editar</a>
                            <a href="acciones/eliminar_profesor.php?id=' . $fila['id_profesor'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'¿Estás seguro de eliminar este profesor?\')">🗑️ Eliminar</a>
                        </td>';
                        echo "</tr>";
                    }
                } else {
                    echo '<tr><td colspan="7" class="text-center">No hay profesores registrados.</td></tr>';
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