<?php
/**
 * eliminar_img.php — Módulo Administrador
 *
 * Quita una publicación (un archivo dentro de una lista de reproducción).
 * El archivo físico NO se borra: pertenece a la biblioteca y puede estar
 * publicado en otras pantallas — se elimina desde contenido.php
 * (assets/php/eliminar_biblioteca.php).
 *
 * Parámetros GET:
 *   id (int) — ID del registro en la tabla `contenido`
 */

date_default_timezone_set('America/Bogota');

session_start([
    'cookie_lifetime' => 7200,
    'gc_maxlifetime'  => 7200,
]);
include('Conexion_DB.php');

if(!isset($_SESSION['logeado'])){
	header('Location: ../../index.php');
	exit();
}

// HTTP_REFERER no siempre llega (navegadores/extensiones que lo bloquean,
// acceso directo) — sin respaldo, el header() de abajo fallaba.
$redirect = $_SERVER['HTTP_REFERER'] ?? '../../contenido.php';

if (isset($_GET['id'])){

	$id = (int) $_GET['id'];

	// Lista a la que pertenece, para marcarla como modificada.
	$consulta  = mysqli_query($conexion, "SELECT `Lista-Reproduccion` FROM contenido WHERE ID = $id");
	$datos_img = mysqli_fetch_array($consulta);

	// Eliminar el registro de la base de datos
	mysqli_query($conexion, "DELETE FROM contenido WHERE ID = $id");

	// Marca la lista padre como modificada (si el contenido pertenecía a
	// alguna), para que el kiosco detecte el cambio vía app/tiempo.php.
	if ($datos_img && !empty($datos_img['Lista-Reproduccion'])){
		$lista_padre = (int) $datos_img['Lista-Reproduccion'];
		$ahora_borrado = date("Y-m-d H:i:s");
		mysqli_query($conexion, "UPDATE `lista-reproduccion` SET `Fecha-Modificacion` = '$ahora_borrado' WHERE `ID` = $lista_padre");
	}
}

// Regresar a la página desde donde se llamó (contenido.php)
header("Location: " . $redirect);
exit();
?>