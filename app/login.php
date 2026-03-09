<?php
error_reporting(0);
date_default_timezone_set('America/Bogota');

session_cache_expire("31536000");
session_set_cookie_params("31536000");
session_start([
    'cookie_lifetime' => 31536000,
    'gc_maxlifetime' => 31536000,
]);

if(!isset($_SESSION['log-modulo'])){
	header('Location: log_index.php');
}

$modulo = $_SESSION['modulo'];

include('../admin/assets/php/Conexion_DB.php');

?>

<!doctype html>
<html><head>
<meta charset="utf-8">
<title>IDT App - Acceso de visitantes</title>
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<meta name="HandheldFriendly" content="true" />
	
	<!-- No guardar Cache -->
	<meta http-equiv="Expires" content="0">
	<meta http-equiv="Last-Modified" content="0">
	<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
	<meta http-equiv="Pragma" content="no-cache">
	
	<!-- Favicon -->
	<link rel="icon" type="image/png" href="assets/img/favicon.png" />
	<!-- CSS de la app -->
	<link href="assets/css/styles.css" rel="stylesheet" type="text/css">
	
	<!-- Scripts arranque -->
<script>
function lanzadera(){
		loading();    
		inicio(); 
    }
    window.onload = lanzadera;	
</script>
	
</head>
	
<body oncontextmenu="return false" onselectstart="return false" ondragstart="return false" style="overflow:hidden" onkeypress="parar()" onclick="parar()" >
	
	<section class="header_contenido">
	  <h1>Bienvenido a<br>IDT App</h1>
	</section>
	
	<section class="section_contenido_registro">
		<p style="margin-top: 45%;">Ingresa tu documento en el lector o coloca el número de tu documento</p>
			<form action="comprobacion.php" onsubmit="return marcado();" method="POST" autocomplete="off">
				
				<div class="login">
					<input  type="text" name="cedula" placeholder="Número de documento" required autofocus>
					<input  type="hidden" name="nombres" >
					<input  type="hidden" name="apellidos" >
				</div>

				<input class="boton_continuar" type="submit" id="submit" value="Continuar">
				
			</form>		
	</section>
	
	<section class="footer_contenido">
    <img src="assets/img/Logos_Alcaldia.png"  alt="Alcaldia de Bogota - IDT"/> </section>
	
	<div id="contenedor_carga">
	  <div id="carga"></div>
	</div>
	
	<!-- Scripts JQuery -->
	<script src="assets/js/jquery.min.js"></script>
	<!-- Scripts IDT app -->
	<script type="text/javascript" src="assets/js/IDT_app.js"></script>
	<!-- Scripts Inactividad -->
	<script type="text/javascript" src="assets/js/Inactividad1.js"></script>
	
</body>
</html>