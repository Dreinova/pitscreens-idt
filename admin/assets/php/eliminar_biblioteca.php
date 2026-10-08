<?php
/**
 * eliminar_biblioteca.php — Módulo Administrador
 *
 * Elimina un archivo de la biblioteca: lo quita de todas las pantallas
 * donde estuviera publicado, borra su registro y el archivo físico (salvo
 * que se esté usando como protector de pantalla en `configuracion`).
 *
 * Parámetros GET:
 *   id (int) — ID del registro en la tabla `biblioteca`
 */

date_default_timezone_set('America/Bogota');

session_start([
    'cookie_lifetime' => 7200,
    'gc_maxlifetime'  => 7200,
]);
include('Conexion_DB.php');
include('contenido_helpers.php');

if(!isset($_SESSION['logeado'])){
	header('Location: ../../index.php');
	exit();
}

if (isset($_GET['id'])) {
	$id      = (int) $_GET['id'];
	$sql     = mysqli_query($conexion, "SELECT `URL` FROM `biblioteca` WHERE `ID` = $id");
	$archivo = $sql ? mysqli_fetch_array($sql) : null;

	if ($archivo) {
		$url_esc = mysqli_real_escape_string($conexion, $archivo['URL']);

		// Quitarlo de cada lista donde estaba y avisar a esas pantallas.
		$listas = mysqli_query($conexion, "SELECT DISTINCT `Lista-Reproduccion` FROM `contenido` WHERE `URL` = '$url_esc'");
		mysqli_query($conexion, "DELETE FROM `contenido` WHERE `URL` = '$url_esc'");
		while ($listas && ($l = mysqli_fetch_array($listas))) {
			marcar_lista_modificada($conexion, $l['Lista-Reproduccion']);
		}

		mysqli_query($conexion, "DELETE FROM `biblioteca` WHERE `ID` = $id");

		$en_protector = mysqli_query($conexion, "SELECT 1 FROM `configuracion` WHERE `Protector_URL` = '$url_esc' LIMIT 1");
		$ruta = '../galeria/' . basename($archivo['URL']);
		if (!mysqli_num_rows($en_protector) && file_exists($ruta)) {
			unlink($ruta);
		}
	}
}

header('Location: ../../contenido.php');
exit();
