<?php
/**
 * configuracion.php — Módulo Administrador
 *
 * Vista de la configuración del "protector de pantalla" del kiosco: el
 * tiempo de inactividad antes de volver a la pantalla de espera, y el
 * video/imagen de respaldo que se muestra ahí cuando no hay contenido
 * programado (tabla `configuracion`, fila única — mismo patrón que `frame`).
 */

date_default_timezone_set('America/Bogota');
include 'assets/php/Conexion_DB.php';

session_start([
    'cookie_lifetime' => 7200,
    'gc_maxlifetime' => 7200,
]);

/*  verificacion login  */
if(!isset($_SESSION['logeado'])):
	header('Location: index.php');
endif;

/* datos de usuario */

$id = $_SESSION['id_Correo'];
$sql = "SELECT usuarios.ID, usuarios.Nombre, usuarios.Correo, usuarios.Foto_Usuario, funciones_usuario.Funcion FROM usuarios INNER JOIN funciones_usuario ON usuarios.Funcion = funciones_usuario.ID WHERE usuarios.ID = '$id' ";
$resultado = mysqli_query($conexion, $sql);
$datos = mysqli_fetch_array($resultado);

$name_user = $datos['Nombre'];
$mail_user = $datos['Correo'];
$foto_user = $datos['Foto_Usuario'];
$funcion_user = $datos['Funcion'];

/* datos de configuración general (de respaldo) — filtra explícitamente
   por Modulo IS NULL para no mostrar por error el override de alguna
   pantalla específica (ver admin/editar_modulo.php). */

$sql_config = "SELECT * FROM `configuracion` WHERE `Modulo` IS NULL LIMIT 1";
$consulta_config = mysqli_query($conexion, $sql_config);

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>
  <title>Configuración del Kiosco - IDT App</title>
  <!--favicon-->
  <link rel="icon" href="assets/images/Favicon.png" type="image/x-icon">
  <!-- simplebar CSS-->
  <link href="assets/plugins/simplebar/css/simplebar.css" rel="stylesheet"/>
  <!-- Bootstrap core CSS-->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- animate CSS-->
  <link href="assets/css/animate.css" rel="stylesheet" type="text/css"/>
  <!-- Icons CSS-->
  <link href="assets/css/icons.css" rel="stylesheet" type="text/css"/>
  <!-- Sidebar CSS-->
  <link href="assets/css/sidebar-menu.css" rel="stylesheet"/>
  <!-- Custom Style-->
  <link href="assets/css/app-style.css" rel="stylesheet"/>

</head>

<body class="bg-theme bg-theme2">

<!-- start loader -->
   <div id="pageloader-overlay" class="visible incoming">
	   <div class="loader-wrapper-outer">
		   <div class="loader-wrapper-inner" >
			   <div class="loader"></div>
		   </div>
	   </div>
	</div>
   <!-- end loader -->

<!-- Start wrapper-->
 <div id="wrapper">

<!-- Menu lateral -->
	 <?php include 'assets/php/menu_lateral.php' ?>

<!-- Menu lateral -->
	 <?php include 'assets/php/menu_superior.php' ?>

<div class="clearfix"></div>

  <div class="content-wrapper">
    <div class="container-fluid">

    <!--Inicio Migas de pan-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
			<h4 class="page-title">Configuración del Kiosco</h4>
	   </div>
     </div>
    <!-- Fin Migas de pan-->

	<!-- Inicio Secciones -->

<div class="row">
		<?php
			while($datos_config = mysqli_fetch_array($consulta_config)){
				$id_config = $datos_config['ID'];
				$tiempo_kiosco = $datos_config['Tiempo_Inactividad_Kiosco'];
				$tiempo_contenido = $datos_config['Tiempo_Inactividad_Contenido'];
				$protector_url = $datos_config['Protector_URL'];
				$protector_tipo = $datos_config['Protector_Tipo'];
		?>
		<div class="col-lg-6">
          <div class="card">
			<div class="card-header text-uppercase"><i class="fa fa-clock-o"></i> Tiempos de inactividad</div>
            <div class="card-body">
				<p class="text-left"><strong>Pantallas de documento/registro: </strong><?php echo $tiempo_kiosco; ?> segundos</p>
				<p class="text-left"><strong>Pantalla de contenido (iframe): </strong><?php echo $tiempo_contenido; ?> segundos</p>
				<small class="text-muted">Tiempo sin tocar la pantalla antes de volver a la pantalla de espera.</small><br>
			  <a class="btn btn-info waves-effect waves-light m-1" href="editar_configuracion.php"><i class='fa fa-pencil'></i> Editar configuración</a>
            </div>
          </div>
        </div>
		<div class="col-lg-6">
          <div class="card">
			<div class="card-header text-uppercase"><i class="fa fa-film"></i> Protector de pantalla</div>
            <div class="card-body">
				<?php if($protector_tipo == 'video'){ ?>
				<video width="100%" controls><source src="assets/galeria/<?php echo $protector_url; ?>" type="video/mp4"></video>
				<?php } else { ?>
				<img src="assets/galeria/<?php echo $protector_url; ?>" class="img-thumbnail" alt="Protector de pantalla">
				<?php } ?>
				<p class="text-left mt-2"><strong>Archivo: </strong><?php echo $protector_url; ?></p>
				<small class="text-muted">Lo que se muestra en la pantalla de espera cuando no hay ninguna programación de contenido activa.</small><br>
			  <a class="btn btn-info waves-effect waves-light m-1" href="editar_configuracion.php"><i class='fa fa-pencil'></i> Editar configuración</a>
            </div>
          </div>
        </div>
		<?php } ?>
    </div>

	<!-- Inicio Secciones -->

    </div>
    <!-- End container-fluid-->

   </div><!--End content-wrapper-->

   <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->

	<!--Start footer-->
	<footer class="footer">
      <div class="container">
        <div class="text-center">
          Instituto Distrital de Turismo — Alcaldía Mayor de Bogotá D.C.
        </div>
      </div>
    </footer>
	<!--End footer-->

  </div><!--End wrapper-->

  <!-- Bootstrap core JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>

  <!-- simplebar js -->
  <script src="assets/plugins/simplebar/js/simplebar.js"></script>
  <!-- sidebar-menu js -->
  <script src="assets/js/sidebar-menu.js"></script>

  <!-- Custom scripts -->
  <script src="assets/js/app-script.js"></script>

</body>
</html>
