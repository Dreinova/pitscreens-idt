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
    exit();
}

$modulo = $_SESSION['modulo'];

include '../admin/assets/php/Conexion_DB.php';

/* Consulta URL */
$consulta_URL = mysqli_query($conexion, "SELECT * FROM `frame` LIMIT 1");
$row_URL = mysqli_fetch_array($consulta_URL);
$URL = $row_URL["URL"];
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>IDT App - Contenido</title>	

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="HandheldFriendly" content="true" />

<!-- No guardar Cache -->
<meta http-equiv="Expires" content="0">
<meta http-equiv="Last-Modified" content="0">
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">

<!-- Favicon -->
<link rel="icon" type="image/png" href="assets/img/favicon.png" />
<!-- CSS de la app -->
<link href="assets/css/styles.css" rel="stylesheet" type="text/css">

<script>
function lanzadera(){
    loading();    
    inicio(); 
}
window.onload = lanzadera;	
</script>
	
</head>

<body oncontextmenu="return false" onselectstart="return false" ondragstart="return false" 
      style="overflow:hidden" onkeypress="parar()" onclick="parar()" >
	
<section class="section_contenido">
    <div class="container"> 
        <a class="regresar" href="index.php">Regresar</a>
        
        <!-- Aquí va el proxy -->
        <iframe class="responsive-iframe" 
                src="proxy.php?url=<?php echo urlencode($URL); ?>" 
                frameborder="0" 
                width="100%" 
                height="100%">
        </iframe>
        
    </div>
</section>

<div id="contenedor_carga">
    <div id="carga"></div>
</div>

<!-- Scripts JQuery -->
<script src="assets/js/jquery.min.js"></script>
<!-- Scripts Inactividad -->
<script type="text/javascript" src="assets/js/Inactividad2.js"></script>
<!-- Scripts IDT app -->
<script type="text/javascript" src="assets/js/IDT_app.js"></script>

</body>
</html>
