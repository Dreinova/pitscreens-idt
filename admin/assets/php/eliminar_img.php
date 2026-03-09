<?php
include('Conexion_DB.php');

if (isset($_GET['id'])){
	
$id = $_GET['id'];

$consulta = mysqli_query($conexion,"SELECT URL FROM contenido WHERE ID = $id");
$datos_img = mysqli_fetch_array($consulta);

$img_url =  $datos_img['URL'];
unlink("../galeria/".$img_url);

$eliminar = "DELETE FROM contenido WHERE ID = $id ";
	
mysqli_query($conexion, $eliminar);
	
}
header("Location: " . $_SERVER['HTTP_REFERER']);

?>