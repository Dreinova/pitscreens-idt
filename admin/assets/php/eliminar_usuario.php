<?php
include 'Conexion_DB.php';

$id = $_GET['id'];
	
$consulta_img = mysqli_query($conexion,"SELECT Foto_Usuario FROM usuarios WHERE ID=$id");
$res=  mysqli_fetch_array($consulta_img);
$img_del = $res["Foto_Usuario"];
$img_name = substr($img_del, 6);
$img_name = '..'.$img_name;
/*echo $img_name;*/
unlink($img_name);
	
$consulta = "DELETE FROM usuarios WHERE ID = '$id' ";
	
$query = mysqli_query($conexion, $consulta);
	if($query){
		header("Location: " . $_SERVER['HTTP_REFERER']);
	}
	else{
		echo "No se ha eliminado la imagen";
		header("Location: " . $_SERVER['HTTP_REFERER']);
	}

?>