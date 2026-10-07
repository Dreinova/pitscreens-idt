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
mysqli_set_charset($conexion, 'utf8mb4');

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

// Sin etiqueta de cierre PHP a propósito: es un archivo de solo inclusión,
// y cualquier espacio/salto de línea después de esa etiqueta se envía como
// salida HTML real — eso rompía session_start()/header() en las páginas
// que lo incluyen (el archivo de producción, generado aparte, tenía justo
// ese problema: "headers already sent" en contenido.php/editar_frame.php).
// (Ojo: no escribir el cierre PHP literal dentro de este comentario — PHP
// corta el modo PHP ahí mismo aunque esté en un comentario de una línea, y
// el resto de este bloque se filtra como salida HTML cruda — exactamente
// el bug que esta nota describe, detectado en app/tiempo.php en 2026-10-07.)