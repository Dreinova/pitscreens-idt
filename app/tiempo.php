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
session_start(); // Necesario para leer $_SESSION['modulo'] (misma sesión que index.php)
include '../admin/assets/php/Conexion_DB.php';

$ahora = date("Y-m-d H:i:s"); // Hora actual del servidor

/* Buscar programación cuya fecha de inicio coincida exactamente con ahora
   (mecanismo heredado: mantiene Estado=1/0 sincronizado en la BD) */
$sql_hora = mysqli_query($conexion,
    "SELECT `Fecha-Inicio` FROM `lista-reproduccion`
     WHERE `Fecha-Inicio` = NOW()
     ORDER BY `Fecha-Inicio` DESC LIMIT 1"
);
$rowProgramaciones = mysqli_fetch_array($sql_hora);
$fecha_inicio      = $rowProgramaciones['Fecha-Inicio'];

if($ahora == $fecha_inicio){
	header("location:activacion.php");
}

/* --- Versión del contenido/configuración vigente para este módulo ---
   Misma resolución específica→general que usa app/index.php, para poder
   compararla en el cliente y forzar un location.reload() real cuando algo
   cambió (edición de contenido, de protector, o paso del tiempo a una
   nueva programación con Fecha-Inicio ya vigente). */
$modulo_actual = isset($_SESSION['modulo']) ? mysqli_real_escape_string($conexion, $_SESSION['modulo']) : '';

$id_lista = null;
$fecha_lista = null;

if ($modulo_actual !== '') {
	$sql_especifica = mysqli_query($conexion,
	    "SELECT `ID`, `Fecha-Modificacion` FROM `lista-reproduccion`
	     WHERE `Modulo` = '$modulo_actual' AND `Fecha-Inicio` <= NOW()
	     ORDER BY `Fecha-Inicio` DESC LIMIT 1"
	);
	$row_especifica = $sql_especifica ? mysqli_fetch_array($sql_especifica) : null;
} else {
	$row_especifica = null;
}

if ($row_especifica) {
	$id_lista    = $row_especifica['ID'];
	$fecha_lista = $row_especifica['Fecha-Modificacion'];
} else {
	$sql_general = mysqli_query($conexion,
	    "SELECT `ID`, `Fecha-Modificacion` FROM `lista-reproduccion`
	     WHERE (`Modulo` IS NULL OR `Modulo` = '') AND `Fecha-Inicio` <= NOW()
	     ORDER BY `Fecha-Inicio` DESC LIMIT 1"
	);
	$row_general = mysqli_fetch_array($sql_general);
	if ($row_general) {
		$id_lista    = $row_general['ID'];
		$fecha_lista = $row_general['Fecha-Modificacion'];
	}
}

$id_config = null;
$fecha_config = null;

if ($modulo_actual !== '') {
	$sql_config_esp = mysqli_query($conexion, "SELECT `ID`, `Fecha-Modificacion` FROM `configuracion` WHERE `Modulo` = '$modulo_actual' LIMIT 1");
	$row_config_esp = $sql_config_esp ? mysqli_fetch_array($sql_config_esp) : null;
} else {
	$row_config_esp = null;
}

if ($row_config_esp) {
	$id_config    = $row_config_esp['ID'];
	$fecha_config = $row_config_esp['Fecha-Modificacion'];
} else {
	$sql_config_gen = mysqli_query($conexion, "SELECT `ID`, `Fecha-Modificacion` FROM `configuracion` WHERE `Modulo` IS NULL LIMIT 1");
	$row_config_gen = mysqli_fetch_array($sql_config_gen);
	if ($row_config_gen) {
		$id_config    = $row_config_gen['ID'];
		$fecha_config = $row_config_gen['Fecha-Modificacion'];
	}
}

$version = $id_lista . '-' . $fecha_lista . '|' . $id_config . '-' . $fecha_config;

// El div #tiempo es invisible (0x0, overflow hidden) — seguro embeber esto.
echo $ahora . '|' . $version;
?>