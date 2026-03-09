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

?>
<!doctype html>
<html><head>
<meta charset="utf-8">
<title>IDT App - Registro de visitantes</title>
	
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
	  <h1>Debes registrarte para<br>continuar</h1>
	</section>
	
	<section class="section_contenido_registro">
		<p>Ingresa tu documento en el lector o completa tus datos en el formulario</p>
		
		<div class="formulario">
			<form action="datos_visitantes.php" onsubmit="return marcado();" method="POST" autocomplete="off">
				<input type="hidden" name="modulo" value="<?php echo $modulo; ?> ">
																				
				<select name="tipodocumento" required>
					<option selected>Cédula de ciudadanía</option>
					<option>Cédula de extranjería</option>
					<option>Tarjeta de Identidad</option>
					<option>Pasaporte</option>
				</select>
				
				<input type="text" name="cedula" placeholder="Número de documento" required autofocus>
				
				<input name="nombres" type="texto" placeholder="Nombres" required > 
				
				<input name="apellidos" type="texto" placeholder="Apellidos" required> 			
				
				<div class="datos">
					<p class="politicas" >He completado estos datos voluntariamente,<br>Conozca nuestra política de manejo de datos en  http://www.bogotaturismo.gov.co/</p>
					
					<p class="aceptar_politicas">
						<input class="input_politicas" type="checkbox" required  name="aceptar" id="aceptar">
						<span>Acepto Términos</span>
					</p>
				</div>
				
				<input class="boton_continuar" type="submit" id="submit" value="Continuar">
				
			</form>		
		</div>
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