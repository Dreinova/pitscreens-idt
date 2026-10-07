<?php
session_start([
    'cookie_lifetime' => 7200,
    'gc_maxlifetime'  => 7200,
]);
include('Conexion_DB.php');

if(!isset($_SESSION['logeado'])){
	header('Location: ../../index.php');
	exit();
}

$redirect = $_SERVER['HTTP_REFERER'] ?? '../../programacion.php';

if (isset($_GET['id'])){
	$id = (int) $_GET['id'];
	// La FK contenido.Lista-Reproduccion es ON UPDATE CASCADE, no ON DELETE
	// CASCADE — sin este paso el contenido asignado quedaría huérfano,
	// apuntando a una lista que ya no existe.
	mysqli_query($conexion, "DELETE FROM contenido WHERE `Lista-Reproduccion` = $id");
	mysqli_query($conexion, "DELETE FROM `lista-reproduccion` WHERE ID = $id");
}

header("Location: " . $redirect);
exit();
?>