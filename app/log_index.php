<?php
session_cache_expire("31536000");
session_set_cookie_params("31536000");
session_start([
    'cookie_lifetime' => 31536000,
    'gc_maxlifetime' => 31536000,
]);

/*  verificacion login  */
if(isset($_SESSION['log-modulo'])){
	header('Location: index.php');
}

if(isset($_SESSION['logeado'])){
	header('Location: index.php');
}

include '../admin/assets/php/Conexion_DB.php';

$consulta_modulo = mysqli_query($conexion, "SELECT * FROM modulos");

if(isset($_POST['entrar'])){
	$Modulo = $_POST['modulo'];
	$_SESSION['log-modulo'] = true;
	$_SESSION['modulo'] = $Modulo;
	header('Location: index.php');
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

			<header class="header_login">
				<img class="logo_login" src="assets/img/Logos_Alcaldia.png" alt="Alcaldia de Bogotá" >
			</header>
	
			<section  class="section_login">
				<h2>Seleccione el perfil de este módulo</h2>
				<div class="seleccion_modulo">
					<form  action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
						<select name="modulo">
							 <?php
								foreach ($consulta_modulo as $modulos){ 
									$nombre_modulo = $modulos['nombre_modulo'];
									$ubicacion_modulo = $modulos['ubicacion'];
							 ?>
								<option value="<?php echo $nombre_modulo; ?>"><?php echo $nombre_modulo.' '.$ubicacion_modulo; ?></option>
							<?php } ?>
						</select>
						 
						<button type="submit" class="boton_login" name="entrar">Iniciar</button>
					</form>
				</div>
			</section>
	
	<div id="contenedor_carga">
		<div id="carga"></div>
	</div>
	
<!-- Scripts JQuery -->
	<script src="assets/js/jquery.min.js"></script>
<!-- Scripts IDT app -->
	<script type="text/javascript" src="assets/js/IDT_app.js"></script>	
	
</body>
</html>