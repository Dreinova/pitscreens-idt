<?php
/**
 * eliminar_img.php — Módulo Administrador
 *
 * Elimina un archivo multimedia (imagen o video) del sistema.
 * Recibe el ID del contenido por GET, borra el archivo físico del
 * directorio /galeria/ (si existe) y luego elimina el registro de la
 * base de datos.
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

	// Obtener el nombre del archivo (campo URL) y la lista a la que pertenece,
	// para poder borrarlo del disco y marcar la lista como modificada.
	$consulta  = mysqli_query($conexion, "SELECT URL, `Lista-Reproduccion` FROM contenido WHERE ID = $id");
	$datos_img = mysqli_fetch_array($consulta);

	// Si ya no existe el registro (doble clic, ID inválido), no hay nada
	// que borrar — evita el warning de array nulo y el unlink() a ciegas.
	if ($datos_img && !empty($datos_img['URL'])){
		$ruta_archivo = "../galeria/" . $datos_img['URL'];
		if (file_exists($ruta_archivo)){
			unlink($ruta_archivo);
		}
	}

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