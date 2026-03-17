<?php
/**
 * Conexion_DB.php
 *
 * Archivo de configuración y conexión a la base de datos MySQL del sistema IDT App.
 * Este archivo debe incluirse en todos los scripts PHP que requieran acceso
 * a la base de datos usando: include 'assets/php/Conexion_DB.php';
 *
 * Base de datos: idt_app
 * Tablas principales:
 *   - usuarios            : Usuarios del panel administrativo
 *   - funciones_usuario   : Roles de usuario (Administrador, Colaborador)
 *   - lista-reproduccion  : Listas de reproducción / programaciones
 *   - contenido           : Archivos multimedia (imágenes y videos)
 *   - frame               : URL externa para visualización en iframe
 *   - modulos             : Pantallas/kioscos registrados en el sistema
 *   - visitantes          : Registro de visitantes del punto de información
 *   - reco                : Estadísticas de uso (edad, género, tiempo)
 */

/* --- Parámetros de conexión a la base de datos --- */
$host_db    = "localhost"; // Host del servidor MySQL
$usuario_db = "root";      // Usuario de la base de datos
$clave_db   = "";          // Contraseña del usuario (vacía en entorno local)
$nombre_db  = "idt_app";   // Nombre de la base de datos del sistema

// Crear la conexión usando la extensión MySQLi
$conexion = mysqli_connect($host_db, $usuario_db, $clave_db, $nombre_db);

/* Bloque de depuración (descomentar para diagnosticar errores de conexión):
if (!$conexion) {
    echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
    echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
    echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
echo "Éxito: Se realizó una conexión apropiada a MySQL!" . PHP_EOL;
echo "Información del host: " . mysqli_get_host_info($conexion) . PHP_EOL;
*/

/* --- Fin conexión a base de datos --- */
?>