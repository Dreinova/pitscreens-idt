<?php

include 'Conexion_DB.php';

$Modulo = $_POST["modulo"];

if($Modulo){
	setcookie('Modulo', $Modulo);
	$_SESSION['log-modulo'] = true;
	$_SESSION['modulo'] = $Modulo;
	/*header('Location: ../../index.php');*/
}

?>