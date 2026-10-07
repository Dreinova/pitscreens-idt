<?php
/**
 * eliminar_usuario.php — Módulo Administrador
 *
 * Elimina un usuario del sistema administrativo.
 * Primero borra la foto de perfil del disco (si tiene una), luego
 * elimina el registro correspondiente de la tabla `usuarios`.
 *
 * Parámetros GET:
 *   id (int) — ID del usuario a eliminar
 *
 * Solo accesible para Administradores (antes solo se ocultaba el botón
 * en usuarios.php, pero la URL en sí no verificaba nada).
 */

session_start([
    'cookie_lifetime' => 7200,
    'gc_maxlifetime'  => 7200,
]);
include 'Conexion_DB.php';

if(!isset($_SESSION['logeado'])){
	header('Location: ../../index.php');
	exit();
}

$id_sesion = $_SESSION['id_Correo'];
$datos_sesion = mysqli_fetch_array(mysqli_query($conexion,
    "SELECT funciones_usuario.Funcion FROM usuarios
     INNER JOIN funciones_usuario ON usuarios.Funcion = funciones_usuario.ID
     WHERE usuarios.ID = '$id_sesion'"
));
if(!$datos_sesion || $datos_sesion['Funcion'] != 'Administrador'){
	header('Location: ../../inicio.php');
	exit();
}

$redirect = $_SERVER['HTTP_REFERER'] ?? '../../usuarios.php';

if(isset($_GET['id'])){
	$id = (int) $_GET['id'];

	// Obtener la ruta de la foto de perfil del usuario para eliminarla del disco
	$consulta_img = mysqli_query($conexion, "SELECT Foto_Usuario FROM usuarios WHERE ID=$id");
	$res     = mysqli_fetch_array($consulta_img);
	$img_del = $res ? $res["Foto_Usuario"] : '';

	// Muchos usuarios no tienen foto (se les muestra la inicial del correo
	// en su lugar) — sin esta validación, substr('', 6) dejaba $img_name
	// en '..' y el unlink() apuntaba al directorio padre en vez de un archivo.
	if(!empty($img_del)){
		$img_name = '..' . substr($img_del, 6); // Navegar al directorio padre (assets/)
		if(file_exists($img_name)){
			unlink($img_name);
		}
	}

	// Eliminar el registro del usuario de la base de datos
	mysqli_query($conexion, "DELETE FROM usuarios WHERE ID = $id");
}

header("Location: " . $redirect);
exit();
?>