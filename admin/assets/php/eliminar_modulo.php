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

$redirect = $_SERVER['HTTP_REFERER'] ?? '../../modulos.php';

if (isset($_GET['id'])){
	$id = (int) $_GET['id'];
	mysqli_query($conexion, "DELETE FROM modulos WHERE ID = $id");
}

header("Location: " . $redirect);
exit();
?>