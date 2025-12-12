<?php
/**
 * ============================================================
 * 🔹 ARCHIVO: db.php
 * 📋 Descripción:
 * Contiene todas las funciones relacionadas con la conexión
 * a la base de datos y las operaciones CRUD para los archivos
 * y usuarios del sistema de gestión escolar.
 * ============================================================
 */

/**
 * 🧩 Conexión a la base de datos MySQL
 * @return mysqli Devuelve la conexión activa a la base de datos
 */
function conexion() {
    $conexion = mysqli_connect('localhost', 'root', '', 'rol');
    if (!$conexion) {
        die("❌ Error de conexión: " . mysqli_connect_error());
    }
    return $conexion;
}

/**
 * 📄 Listar todos los archivos
 * @param mysqli $conexion Conexión activa a la BD
 * @return mysqli_result Resultado de la consulta
 */
function listar($conexion) {
    $sql = "SELECT * FROM archivo";
    return mysqli_query($conexion, $sql);
}

/**
 * 📤 Insertar un nuevo archivo en la base de datos
 * @param mysqli $conexion Conexión activa
 * @param int $id_usuario ID del usuario propietario
 * @param string $nombre Nombre del archivo
 * @param string $categoria Extensión o tipo de archivo (pdf, jpg, etc.)
 * @param string $fecha Fecha de subida
 * @param string $tipo Tipo MIME del archivo
 * @param string $archivoBLOB Contenido binario del archivo
 * @return bool True si se insertó correctamente, False si falló
 */
function insertar($conexion, $id_usuario, $nombre, $categoria, $fecha, $tipo, $archivoBLOB) {
    $sql = "INSERT INTO archivo(id_usuario, nombre, categoria, fecha, tipo, archivo)
            VALUES ('$id_usuario', '$nombre', '$categoria', '$fecha', '$tipo', '$archivoBLOB')";
    return mysqli_query($conexion, $sql);
}

/**
 * 🗑️ Eliminar un archivo por su ID
 * @param mysqli $conexion Conexión activa
 * @param int $id ID del archivo
 * @return bool True si se eliminó correctamente
 */
function eliminar($conexion, $id) {
    $sql = "DELETE FROM archivo WHERE id=$id";
    return mysqli_query($conexion, $sql);
}

/**
 * 📋 Obtener los datos de un archivo específico
 * @param mysqli $conexion Conexión activa
 * @param int $id ID del archivo
 * @return array|null Devuelve los datos del archivo o null si no existe
 */
function datos($conexion, $id) {
    $sql = "SELECT * FROM archivo WHERE id=$id";
    $query = mysqli_query($conexion, $sql);
    return mysqli_fetch_assoc($query);
}

/**
 * ✏️ Editar solo el nombre del archivo
 * @param mysqli $conexion Conexión activa
 * @param int $id ID del archivo
 * @param string $nombre Nuevo nombre
 * @return bool True si se actualizó correctamente
 */
function editar_nombre($conexion, $id, $nombre) {
    $sql = "UPDATE archivo SET nombre='$nombre' WHERE id=$id";
    return mysqli_query($conexion, $sql);
}

/**
 * 🔁 Actualizar el archivo físico (contenido BLOB, tipo y fecha)
 * @param mysqli $conexion Conexión activa
 * @param int $id ID del archivo
 * @param string $categoria Nueva extensión
 * @param string $tipo Tipo MIME
 * @param string $fecha Fecha de modificación
 * @param string $archivoBLOB Contenido binario del archivo
 * @return bool True si se actualizó correctamente
 */
function editar_archivo($conexion, $id, $categoria, $tipo, $fecha, $archivoBLOB) {
    $sql = "UPDATE archivo 
            SET categoria='$categoria', tipo='$tipo', fecha='$fecha', archivo='$archivoBLOB' 
            WHERE id=$id";
    return mysqli_query($conexion, $sql);
}

/**
 * ✍️ Editar tanto el nombre como el contenido del archivo
 * @param mysqli $conexion Conexión activa
 * @param int $id ID del archivo
 * @param string $nombre Nuevo nombre
 * @param string $categoria Nueva categoría
 * @param string $tipo Tipo MIME
 * @param string $fecha Fecha de modificación
 * @param string $archivoBLOB Contenido del archivo
 * @return bool True si se actualizó correctamente
 */
function editar($conexion, $id, $nombre, $categoria, $tipo, $fecha, $archivoBLOB) {
    $sql = "UPDATE archivo 
            SET nombre='$nombre', categoria='$categoria', tipo='$tipo', fecha='$fecha', archivo='$archivoBLOB' 
            WHERE id=$id";
    return mysqli_query($conexion, $sql);
}

/* ============================================================
 * 🧪 (OBSOLETO / EN DESUSO)
 * Esta función fue usada para insertar créditos manualmente.
 * ============================================================ */

/**
 * ⚠️ Insertar créditos (NO USAR)
 * @param mysqli $conexion
 * @param int $creditos
 * @return bool
 */
function subir_creditos($conexion, $creditos) {
    $sql = "INSERT INTO usuarios(creditos) VALUES ('$creditos')";
    return mysqli_query($conexion, $sql);
}

/**
 * 🔗 Relacionar un archivo con un usuario
 * @param mysqli $conexion Conexión activa
 * @param int $id_usuario ID del usuario
 * @param int $id_archivo ID del archivo
 * @return bool True si se creó el vínculo correctamente
 */
function enlace_usuario($conexion, $id_usuario, $id_archivo) {
    $sql = "INSERT INTO usuario_archivo(id_usuario, id_archivo) VALUES ('$id_usuario','$id_archivo')";
    return mysqli_query($conexion, $sql);
}
?>
