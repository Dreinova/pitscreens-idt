<?php
/**
 * log_index.php — Módulo App (Pantalla)
 *
 * Pantalla de selección de perfil de módulo (kiosco).
 * Es el punto de entrada para la aplicación de pantalla interactiva.
 * El operador selecciona cuál kiosco/módulo es esta pantalla antes de
 * ponerla en funcionamiento público.
 *
 * Flujo:
 *   1. Si ya hay sesión de módulo activa, redirige a index.php (pantalla principal).
 *   2. Muestra un listado desplegable con todos los módulos registrados en la BD.
 *   3. Al seleccionar y enviar, guarda el módulo en sesión y redirige a index.php.
 *
 * Variables de sesión que establece:
 *   $_SESSION['log-modulo'] → true (módulo seleccionado)
 *   $_SESSION['modulo']     → Nombre del módulo/kiosco activo
 *
 * La sesión tiene duración de 1 año (31536000 segundos) para que la pantalla
 * no necesite re-seleccionar el módulo entre reinicios del navegador.
 */

// Configurar sesión de larga duración (1 año) para pantallas que operan 24/7
session_cache_expire("31536000");
session_set_cookie_params("31536000");
session_start([
    'cookie_lifetime' => 31536000,
    'gc_maxlifetime'  => 31536000,
]);

include '../admin/assets/php/Conexion_DB.php';

/* URL estable por pantalla (aditivo): abrir con ?pantalla=<modulos.ID>
   selecciona esa pantalla directamente, igual que si se hubiera elegido
   a mano en el selector de abajo. Si el ID no existe, se ignora por
   completo y el flujo normal sigue intacto. */
if(isset($_GET['pantalla'])){
	$pantalla_id   = (int) $_GET['pantalla'];
	$sql_pantalla  = mysqli_query($conexion, "SELECT nombre_modulo FROM modulos WHERE ID = $pantalla_id");
	$row_pantalla  = $sql_pantalla ? mysqli_fetch_array($sql_pantalla) : null;
	if($row_pantalla){
		$_SESSION['log-modulo'] = true;
		$_SESSION['modulo']     = $row_pantalla['nombre_modulo'];
		$_SESSION['modulo_id']  = $pantalla_id;
		header('Location: index.php');
		exit();
	}
}

/* Si ya hay sesión de módulo o de admin, ir directamente al inicio */
if(isset($_SESSION['log-modulo'])){
	header('Location: index.php');
	exit();
}
if(isset($_SESSION['logeado'])){
	header('Location: index.php');
	exit();
}

// Obtener todos los módulos registrados para mostrar en el selector
$consulta_modulo = mysqli_query($conexion, "SELECT * FROM modulos");

if(isset($_POST['entrar'])){
	// Guardar el módulo seleccionado en sesión e ir a la pantalla principal
	$Modulo = $_POST['modulo'];
	$_SESSION['log-modulo'] = true;
	$_SESSION['modulo']     = $Modulo;

	// Resolver también el ID de esa pantalla, para que quede disponible en
	// sesión igual que cuando se entra vía ?pantalla=<ID>.
	$Modulo_esc   = mysqli_real_escape_string($conexion, $Modulo);
	$sql_id       = mysqli_query($conexion, "SELECT ID FROM modulos WHERE nombre_modulo = '$Modulo_esc' LIMIT 1");
	$row_id       = $sql_id ? mysqli_fetch_array($sql_id) : null;
	if($row_id){
		$_SESSION['modulo_id'] = (int) $row_id['ID'];
	}

	header('Location: index.php');
	exit();
}
?>

<!DOCTYPE html>
<html lang="es">
	
<head>
<meta charset="UTF-8">
<title>Bienvenido - IDT App</title>
	
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
    }
    window.onload = lanzadera;	
</script>
		
</head>
	
<body oncontextmenu="return false" onselectstart="return false" ondragstart="return false">

	<div class="kiosk-select-wrap">
		<header class="kiosk-topbar">
			<span class="kiosk-logo-plate"><img class="kiosk-topbar-logo" src="assets/img/logo-bogota.svg" alt="Bogotá"></span>
		</header>

		<main class="kiosk-main">
			<div class="kiosk-card">
				<h2 class="kiosk-heading">Selecciona la pantalla<br>de este punto</h2>
				<p class="kiosk-subtext">Este ajuste solo debe hacerse una vez al configurar la pantalla</p>
				<form class="kiosk-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
					<div class="kiosk-field">
						<label class="sr-only" for="modulo">Pantalla</label>
						<select class="kiosk-select" id="modulo" name="modulo">
							 <?php
								foreach ($consulta_modulo as $modulos){
									$nombre_modulo = $modulos['nombre_modulo'];
									$ubicacion_modulo = $modulos['ubicacion'];
							 ?>
								<option value="<?php echo $nombre_modulo; ?>"><?php echo $nombre_modulo.' '.$ubicacion_modulo; ?></option>
							<?php } ?>
						</select>
					</div>

					<button type="submit" class="kiosk-btn kiosk-btn--primary kiosk-btn--block" name="entrar">Iniciar</button>
				</form>
			</div>
		</main>
	</div>

	<div id="contenedor_carga">
		<div id="carga"></div>
	</div>
	
<!-- Scripts JQuery -->
	<script src="assets/js/jquery.min.js"></script>
<!-- Scripts IDT app -->
	<script type="text/javascript" src="assets/js/IDT_app.js"></script>	
	
</body>
</html>