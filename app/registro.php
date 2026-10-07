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

	<header class="kiosk-topbar">
		<span class="kiosk-logo-plate"><img class="kiosk-topbar-logo" src="assets/img/logo-bogota.svg" alt="Bogotá"></span>
	</header>

	<main class="kiosk-main">
		<div class="kiosk-card" style="max-width: 900px;">
			<h1 class="kiosk-heading">Debes registrarte<br>para continuar</h1>
			<p class="kiosk-subtext">Ingresa tu documento en el lector o completa tus datos en el formulario</p>

			<form action="datos_visitantes.php" method="POST" autocomplete="off">
				<input type="hidden" name="modulo" value="<?php echo $modulo; ?> ">

				<div class="kiosk-form-grid">
					<div class="kiosk-field kiosk-field--full">
						<label class="sr-only" for="tipodocumento">Tipo de documento</label>
						<select class="kiosk-select" id="tipodocumento" name="tipodocumento" required>
							<option selected>Cédula de ciudadanía</option>
							<option>Cédula de extranjería</option>
							<option>Tarjeta de Identidad</option>
							<option>Pasaporte</option>
						</select>
					</div>

					<div class="kiosk-field">
						<label class="sr-only" for="cedula">Número de documento</label>
						<input class="kiosk-input" id="cedula" type="text" name="cedula" placeholder="Número de documento" required autofocus>
					</div>

					<div class="kiosk-field">
						<label class="sr-only" for="nombres">Nombres</label>
						<input class="kiosk-input" id="nombres" name="nombres" type="text" placeholder="Nombres" required>
					</div>

					<div class="kiosk-field kiosk-field--full">
						<label class="sr-only" for="apellidos">Apellidos</label>
						<input class="kiosk-input" id="apellidos" name="apellidos" type="text" placeholder="Apellidos" required>
					</div>

					<div class="kiosk-policy">
						<input type="checkbox" required name="aceptar" id="aceptar">
						<p>
							<label for="aceptar">Acepto que he completado estos datos voluntariamente. Conoce nuestra política de manejo de datos en bogotaturismo.gov.co</label>
						</p>
					</div>
				</div>

				<button class="kiosk-btn kiosk-btn--primary kiosk-btn--block" type="submit" id="submit" style="margin-top: 20px;">Continuar</button>

			</form>
		</div>
	</main>

	<footer class="kiosk-footer">
		<span class="kiosk-logo-plate"><img class="kiosk-footer-logo" src="assets/img/logo-bogota.svg" alt="Bogotá"></span>
	</footer>

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