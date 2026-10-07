<?php 

/* conexion a base de datos */

$host_db = "localhost"; // Host de la BD
$usuario_db = "root"; // Usuario de la BD
$clave_db = ""; // Contraseña de la BD
$nombre_db = "idt_app"; // Nombre de la BD

$conexion = mysqli_connect($host_db, $usuario_db, $clave_db, $nombre_db);
mysqli_set_charset($conexion, 'utf8mb4');

/*if (!$conexion) {
    echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
    echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
    echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

echo "Éxito: Se realizó una conexión apropiada a MySQL! La base de datos mi_bd es genial." . PHP_EOL;
echo "Información del host: " . mysqli_get_host_info($conexion) . PHP_EOL;*/

/* Fin conexion a base de datos */

// Sin etiqueta de cierre "?>" a propósito: ver nota en
// admin/assets/php/Conexion_DB.php (evita el bug de "headers already sent").