<?php
/**
 * contenido.php — Módulo Administrador
 *
 * Catálogo global de contenido multimedia (imágenes y videos) y sus
 * listas de reproducción asignadas. La subida vive en nuevo_contenido.php
 * y la edición por ítem en editar_contenido.php — esta página solo lista.
 *
 * Campos de la tabla `contenido`:
 *   URL              — Nombre del archivo en disco
 *   Tipo             — 'image' o 'video'
 *   Estado           — 1 (Activo) / 0 (Inactivo)
 *   Orden            — Número de posición en la reproducción
 *   Lista-Reproduccion — FK a la tabla `lista-reproduccion`
 *   Fecha_Modificación — Fecha y hora de la última subida/edición
 *   Usuario          — Nombre del usuario que realizó la acción
 */

date_default_timezone_set('America/Bogota');
include 'assets/php/Conexion_DB.php';

// Sesión administrativa con 2 horas de duración
session_start([
    'cookie_lifetime' => 7200,
    'gc_maxlifetime'  => 7200,
]);

/* Proteger la página: redirigir al login si no hay sesión activa */
if(!isset($_SESSION['logeado'])){
	header('Location: index.php');
	exit();
}

/* --- Obtener datos del usuario autenticado --- */
$id     = $_SESSION['id_Correo'];
$sql    = "SELECT usuarios.ID, usuarios.Nombre, usuarios.Correo, usuarios.Foto_Usuario,
                  funciones_usuario.Funcion
           FROM usuarios
           INNER JOIN funciones_usuario ON usuarios.Funcion = funciones_usuario.ID
           WHERE usuarios.ID = '$id'";
$resultado    = mysqli_query($conexion, $sql);
$datos        = mysqli_fetch_array($resultado);
$name_user    = $datos['Nombre'];
$mail_user    = $datos['Correo'];
$foto_user    = $datos['Foto_Usuario'];
$funcion_user = $datos['Funcion'];

?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>
  <title>Contenido - IDT App</title>
  <!--favicon-->
  <link rel="icon" href="assets/images/Favicon.png" type="image/x-icon">
  <!--Lightbox Css-->
  <link href="assets/plugins/fancybox/css/jquery.fancybox.min.css" rel="stylesheet" type="text/css"/>
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
			<h4 class="page-title">Contenido</h4>
			<p class="text-muted mb-0">Catálogo de todo el contenido subido. Para asignar contenido solo a una pantalla puntual, hazlo directamente desde <a href="modulos.php">Pantallas</a> → Editar esa pantalla.</p>
	   </div>
	   <div class="col-sm-3 text-right">
		   <a href="nuevo_contenido.php" class="btn btn-primary"><i class="fa fa-plus"></i> Subir contenido</a>
	   </div>
     </div>
    <!-- Fin Migas de pan-->

	<div class="row">
			<div class="col-6">
			  <div class="card">
				<div class="card-header text-uppercase"><i class="fa fa-image"></i> Galeria de Imagenes</div>
				<div class="card-body">
				  <div class="row">

					 <?php
    				$consulta_img = mysqli_query($conexion,"SELECT `contenido`.`ID`, `contenido`.`URL`, `contenido`.`Tipo`, `contenido`.`Estado`, `contenido`.`Orden`, `lista-reproduccion`.`Nombre` FROM `contenido` INNER JOIN `lista-reproduccion` ON `contenido`.`Lista-Reproduccion` = `lista-reproduccion`.`ID` WHERE `contenido`.`Tipo` ='image' ORDER BY `contenido`.`ID` ASC");
    				if (mysqli_num_rows($consulta_img) === 0) {
						echo '<div class="col-12 text-muted">No hay imágenes subidas todavía.</div>';
					}
    				while($res = mysqli_fetch_array($consulta_img)){
						$ID_img = $res["ID"];
						$url_img = $res["URL"];
						$tipo = $res["Tipo"];
						$Estado = $res["Estado"];
							if($Estado == '1'){
								$Estado = 'Activo';
							}
							if($Estado == '0'){
								$Estado = 'Inactivo';
							}
						$Orden = $res["Orden"];
						$nombre_slide = htmlspecialchars($res["Nombre"]);

						echo '<div class="col-md-6 col-lg-3 col-xl-3" style="margin: 1% 0%;">';

						echo '<a href="assets/galeria/'.htmlspecialchars($url_img).'" data-fancybox="images" data-caption="'.htmlspecialchars($url_img).'">';
						echo '<img src="assets/galeria/'.htmlspecialchars($url_img).'" class="lightbox-thumb img-thumbnail">';
						echo '</a>';

						echo '<strong>Nombre: </strong>'.htmlspecialchars($url_img)."<br>";
						echo '<strong>Estado: </strong>'.$Estado."<br>";
						echo '<strong>Lista de reproduccion: </strong>'.$nombre_slide."<br>";
						echo '<strong>Orden: </strong>'.$Orden."<br><br>";

						echo '<div class="btn-group btn-group-sm" role="group">';
						echo '<a href="editar_contenido.php?id='.$ID_img.'" class="btn btn-outline-secondary" title="Editar"><i class="fa fa-edit"></i></a>';
						echo '<a href="assets/php/eliminar_img.php?id='.$ID_img.'&user='.urlencode($name_user).'"'
						   . ' class="btn btn-outline-danger js-confirm-delete" title="Eliminar"'
						   . ' data-title="¿Eliminar este contenido?"'
						   . ' data-body="Esta acción eliminará &quot;'.htmlspecialchars($url_img).'&quot; de la galería y de la pantalla donde se esté usando. Esta acción no se puede deshacer.">'
						   . '<i class="fa fa-trash"></i></a>';
						echo '</div>';

						echo '</div>';
    				}
    			?>

				  </div>
				</div>
			  </div>
			</div>

			<div class="col-6">
			  <div class="card">
				<div class="card-header text-uppercase"><i class="fa fa-image"></i> Galeria de Video</div>
				<div class="card-body">
				  <div class="row">

					  <?php
    				$consulta_video = mysqli_query($conexion,"SELECT `contenido`.`ID`, `contenido`.`URL`, `contenido`.`Tipo`, `contenido`.`Estado`, `contenido`.`Orden`, `lista-reproduccion`.`Nombre` FROM `contenido` INNER JOIN `lista-reproduccion` ON `contenido`.`Lista-Reproduccion` = `lista-reproduccion`.`ID` WHERE `contenido`.`Tipo` ='video' ORDER BY `contenido`.`ID` ASC");
    				if (mysqli_num_rows($consulta_video) === 0) {
						echo '<div class="col-12 text-muted">No hay videos subidos todavía.</div>';
					}
    				while($res = mysqli_fetch_array($consulta_video)){
						$ID_img = $res["ID"];
						$url_img = $res["URL"];
						$tipo = $res["Tipo"];
						$Estado = $res["Estado"];
							if($Estado == '1'){
								$Estado = 'Activo';
							}
							if($Estado == '0'){
								$Estado = 'Inactivo';
							}
						$Orden = $res["Orden"];
						$nombre_slide = htmlspecialchars($res["Nombre"]);

						echo '<div class="col-md-6 col-lg-3 col-xl-3" style="margin: 1% 0%;">';
						echo '<video width="100%" controls><source src="assets/galeria/'.htmlspecialchars($url_img).'" type="video/mp4"></video>';

						echo '<strong>Nombre: </strong>'.htmlspecialchars($url_img)."<br>";
						echo '<strong>Estado: </strong>'.$Estado."<br>";
						echo '<strong>Lista de reproduccion: </strong>'.$nombre_slide."<br>";
						echo '<strong>Orden: </strong>'.$Orden."<br><br>";

						echo '<div class="btn-group btn-group-sm" role="group">';
						echo '<a href="editar_contenido.php?id='.$ID_img.'" class="btn btn-outline-secondary" title="Editar"><i class="fa fa-edit"></i></a>';
						echo '<a href="assets/php/eliminar_img.php?id='.$ID_img.'&user='.urlencode($name_user).'"'
						   . ' class="btn btn-outline-danger js-confirm-delete" title="Eliminar"'
						   . ' data-title="¿Eliminar este contenido?"'
						   . ' data-body="Esta acción eliminará &quot;'.htmlspecialchars($url_img).'&quot; de la galería y de la pantalla donde se esté usando. Esta acción no se puede deshacer.">'
						   . '<i class="fa fa-trash"></i></a>';
						echo '</div>';

						echo '</div>';
    				}
    			?>

				  </div>
				</div>
			  </div>
			</div>
		  </div>


<!--End Row-->

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
          © Instituto Distrital de Turismo
        </div>
      </div>
    </footer>
	<!--End footer-->

  </div><!--End wrapper-->

  <?php include 'assets/php/confirm_delete_modal.php'; ?>

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

  <!--Lightbox-->
  <script src="assets/plugins/fancybox/js/jquery.fancybox.min.js"></script>
  <!-- Confirmación de eliminación -->
  <script src="assets/js/confirm-delete.js"></script>

<?php
	mysqli_close($conexion);
?>

</body>
</html>
