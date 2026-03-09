<?php
include('Conexion_DB.php');

if (isset($_GET['id'])){
	
$id = $_GET['id'];
	
$consulta = "DELETE FROM modulos WHERE ID = '$id' ";
	
mysqli_query($conexion, $consulta);
	
}
header("Location: " . $_SERVER['HTTP_REFERER']);

?>