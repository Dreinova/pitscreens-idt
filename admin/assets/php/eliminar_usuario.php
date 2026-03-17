<?php
/**
 * eliminar_usuario.php — Módulo Administrador
 *
 * Elimina un usuario del sistema administrativo.
 * Primero borra la foto de perfil del disco (si existe), luego elimina
 * el registro correspondiente de la tabla `usuarios`.
 *
 * Parámetros GET:
 *   id (int) — ID del usuario a eliminar
 *
 * Nota: Solo debe ser accedido por administradores; la restricción
 * se aplica visualmente desde usuarios.php (solo Administrador ve el botón).
 */

include 'Conexion_DB.php';

$id = $_GET['id'];

// Obtener la ruta de la foto de perfil del usuario para eliminarla del disco
$consulta_img = mysqli_query($conexion, "SELECT Foto_Usuario FROM usuarios WHERE ID=$id");
$res     = mysqli_fetch_array($consulta_img);
$img_del = $res["Foto_Usuario"];

// Ajustar la ruta relativa para unlink (desde el directorio assets/php/)
$img_name = substr($img_del, 6); // Quitar los primeros 6 caracteres de la ruta almacenada
$img_name = '..' . $img_name;    // Navegar al directorio padre (assets/)

// Eliminar la imagen física del servidor
unlink($img_name);

// Eliminar el registro del usuario de la base de datos
$consulta = "DELETE FROM usuarios WHERE ID = '$id'";
$query    = mysqli_query($conexion, $consulta);

// Regresar a la página de gestión de usuarios
if($query){
	header("Location: " . $_SERVER['HTTP_REFERER']);
}
else{
	echo "No se ha eliminado el usuario";
	header("Location: " . $_SERVER['HTTP_REFERER']);
}
?>