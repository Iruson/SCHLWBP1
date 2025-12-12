<?php
/**
 * ---------------------------------------------------------
 * Página: gestionar_usuarios.php
 * Descripción:
 * Panel de administración para visualizar, editar y actualizar
 * usuarios registrados en el sistema. Solo accesible por administradores.
 * 
 * Características:
 *  - Permite editar datos directamente en la tabla.
 *  - Filtro de búsqueda dinámico por cédula, nombre o usuario.
 *  - Control de créditos: si supera 60, el usuario podrá descargar archivos.
 *  - Diseño con colores institucionales (azul rey y amarillo).
 * ---------------------------------------------------------
 */

session_start();
include('../db.php');
$conexion = conexion();

// 🧱 Verificación de acceso (solo admins)
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_cargo'] != 1) {
    header("Location: ../login.php");
    exit();
}

// 🧾 Actualización de datos de usuario (si se envía el formulario)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    
    // 1. Recoger datos (con la 'id' minúscula corregida)
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña']; // Contraseña nueva (si se escribió)
    $id_cargo = $_POST['id_cargo'];
    $creditos = $_POST['creditos'];

    // 2. Lógica de contraseña
    if (!empty($contraseña)) {
        // Si el admin escribió una contraseña nueva, la hasheamos
        $hash_contraseña = password_hash($contraseña, PASSWORD_DEFAULT);
        
        // Consulta CON cambio de contraseña
        $sql = "UPDATE usuarios SET nombre=?, usuario=?, contraseña=?, id_cargo=?, creditos=? WHERE Id=?";
        $stmt = mysqli_prepare($conexion, $sql);
        // 'ssssii' significa: String, String, String, String, Int, Int
        mysqli_stmt_bind_param($stmt, 'ssssii', $nombre, $usuario, $hash_contraseña, $id_cargo, $creditos, $id);

    } else {
        // Si el admin dejó la contraseña vacía, NO la actualizamos
        
        // Consulta SIN cambio de contraseña
        $sql = "UPDATE usuarios SET nombre=?, usuario=?, id_cargo=?, creditos=? WHERE Id=?";
        $stmt = mysqli_prepare($conexion, $sql);
        // 'ssiii' significa: String, String, Int, Int, Int
        mysqli_stmt_bind_param($stmt, 'ssiii', $nombre, $usuario, $id_cargo, $creditos, $id);
    }

    // 3. Ejecutar la consulta preparada
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('✅ Usuario actualizado correctamente'); window.location='gestionar_usuarios.php';</script>";
    } else {
        echo "<script>alert('❌ Error al actualizar usuario: " . mysqli_stmt_error($stmt) . "');</script>";
    }
    
    mysqli_stmt_close($stmt);

} // Fin del bloque POST

// 📊 Consulta de todos los usuarios
$sql = "SELECT * FROM usuarios ORDER BY id_cargo, nombre";
$result = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css2/gestionar_usuarios.css">
    
</head>

<body>

<div class="container">
    <h2>Gestión de Usuarios</h2>

    <!-- 🔙 Enlace de retorno y buscador -->
    <div class="d-flex justify-content-between mb-3">
        <a href="principal_upload.php" class="btn btn-volver">⬅ Volver al panel principal</a>

        <div class="search-box">
            <input type="text" id="buscar" placeholder="🔍 Buscar por nombre, usuario o cédula...">
        </div>
    </div>

    <!-- 📋 Tabla editable de usuarios -->
    <table class="table table-striped table-bordered align-middle text-center" id="tablaUsuarios">
        <thead>
            <tr>
                <th>Cédula</th>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Contraseña</th>
                <th>Tipo</th>
                <th>Créditos</th>
                <th>Estado</th>
                <th>Actualizar</th>
                <th>Eliminar</th>
            </tr>
        </thead>

        <tbody>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <form method="POST" action="">
        <tr>
            <td>
                <span style="font-weight:bold;"><?= htmlspecialchars($row['Id']); ?></span>
                <input type="hidden" name="id" value="<?= $row['Id']; ?>">
            </td>

            <td><input type="text" name="nombre" value="<?= htmlspecialchars($row['nombre']); ?>"></td>

            <td><input type="text" name="usuario" value="<?= htmlspecialchars($row['usuario']); ?>"></td>

            <td><input type="password" name="contraseña" placeholder="●●●●●● (Dejar vacío para no cambiar)"></td>

            <td>
                <select name="id_cargo">
                    <option value="1" <?= $row['id_cargo']==1?'selected':'' ?>>Admin</option>
                    <option value="2" <?= $row['id_cargo']==2?'selected':'' ?>>Profesor</option>
                    <option value="3" <?= $row['id_cargo']==3?'selected':'' ?>>Represenante</option>
                </select>
            </td>

            <td>
                <input type="number" name="creditos" value="<?= $row['creditos']; ?>">
            </td>

            <td>
                <?php if ($row['creditos'] >= 60): ?>
                    <span class="habilitado">🟢 Habilitado</span>
                <?php else: ?>
                    <span class="no-habilitado">🔴 No habilitado</span>
                <?php endif; ?>
            </td>

            <td>
                <button type="submit" name="update" class="btn btn-editar btn-sm">Guardar</button>
            </td>
            <td>
                <a class="btn btn-danger" href="acciones/eliminar_usuario.php?id=<?= $row['Id']; ?>" class="btn btn-eliminar btn-sm" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
        </tr>
    </form>
<?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- 🧭 Script buscador dinámico -->
<script>
document.getElementById("buscar").addEventListener("keyup", function() {
    let filtro = this.value.toLowerCase();
    let filas = document.querySelectorAll("#tablaUsuarios tbody tr");

    filas.forEach(fila => {
        let texto = fila.innerText.toLowerCase();
        fila.style.display = texto.includes(filtro) ? "" : "none";
    });
});
</script>

</body>
</html>
