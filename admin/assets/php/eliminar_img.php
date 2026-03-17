<?php
/**
 * eliminar_img.php — Módulo Administrador
 *
 * Elimina un archivo multimedia (imagen o video) del sistema.
 * Recibe el ID del contenido por GET, borra el archivo físico del
 * directorio /galeria/ y luego elimina el registro de la base de datos.
 *
 * Parámetros GET:
 *   id (int) — ID del registro en la tabla `contenido`
 *
 * Nota: No verifica autenticación de sesión; depende de que el enlace
 * sea generado únicamente desde páginas protegidas (contenido.php).
 */

include('Conexion_DB.php');

if (isset($_GET['id'])){

	$id = $_GET['id'];

	// Obtener el nombre del archivo (campo URL) para poder borrarlo del disco
	$consulta  = mysqli_query($conexion, "SELECT URL FROM contenido WHERE ID = $id");
	$datos_img = mysqli_fetch_array($consulta);
	$img_url   = $datos_img['URL'];

	// Eliminar el archivo físico del servidor
	unlink("../galeria/" . $img_url);

	// Eliminar el registro de la base de datos
	$eliminar = "DELETE FROM contenido WHERE ID = $id";
	mysqli_query($conexion, $eliminar);
}

// Regresar a la página desde donde se llamó (contenido.php)
header("Location: " . $_SERVER['HTTP_REFERER']);
?>