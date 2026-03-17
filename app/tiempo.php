<?php
/**
 * tiempo.php — Módulo App (Pantalla)
 *
 * Script de verificación de tiempo cargado vía AJAX cada segundo desde index.php.
 * Comprueba si es el momento de activar una nueva programación/lista de reproducción.
 *
 * Mecanismo:
 *   index.php actualiza el div #tiempo con el contenido de este script cada 1000ms:
 *     setInterval(function(){ $('#tiempo').load('tiempo.php'); }, 1000);
 *
 * Proceso:
 *   1. Obtiene la hora actual del servidor (zona horaria Bogotá).
 *   2. Consulta si existe alguna programación cuya Fecha-Inicio coincida exactamente
 *      con la hora actual (comparación al segundo).
 *   3. Si hay coincidencia, redirige a activacion.php para conmutar la lista activa.
 *   4. En caso contrario, simplemente muestra la hora actual en el div.
 *
 * Nota: La comparación exacta al segundo puede no activarse si el servidor
 * está bajo carga. activacion.php también se consulta como respaldo desde index.php.
 */

error_reporting(0); // Suprimir errores para que no rompan el JSON/HTML del div
date_default_timezone_set('America/Bogota');
include '../admin/assets/php/Conexion_DB.php';

$ahora = date("Y-m-d H:i:s"); // Hora actual del servidor

/* Buscar programación cuya fecha de inicio coincida exactamente con ahora */
$sql_hora = mysqli_query($conexion,
    "SELECT `Fecha-Inicio` FROM `lista-reproduccion`
     WHERE `Fecha-Inicio` = NOW()
     ORDER BY `Fecha-Inicio` DESC LIMIT 1"
);
$rowProgramaciones = mysqli_fetch_array($sql_hora);
$fecha_inicio      = $rowProgramaciones['Fecha-Inicio'];

// Mostrar la hora actual en el div de tiempo (visible en pantalla de kiosco)
echo $ahora;

// Si la hora del servidor coincide con la programación, activar el cambio de playlist
if($ahora == $fecha_inicio){
	header("location:activacion.php");
}
?>