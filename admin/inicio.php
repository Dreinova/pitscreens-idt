<?php
include 'assets/php/Conexion_DB.php';

session_start([
    'cookie_lifetime' => 7200,
    'gc_maxlifetime' => 7200,
]);

/*  verificacion login  */
if(!isset($_SESSION['logeado'])){
	header('Location: index.php');
}

/* datos de usuario */

$id = $_SESSION['id_Correo'];
$sql = "SELECT usuarios.ID, usuarios.Nombre, usuarios.Correo, usuarios.Foto_Usuario, funciones_usuario.Funcion FROM usuarios INNER JOIN funciones_usuario ON usuarios.Funcion = funciones_usuario.ID WHERE usuarios.ID = '$id' ";
$resultado = mysqli_query($conexion, $sql);
$datos = mysqli_fetch_array($resultado);

$name_user = $datos['Nombre'];
$mail_user = $datos['Correo'];
$foto_user = $datos['Foto_Usuario'];
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
  <title>Bienvenido - IDT App</title>
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

<!-- Menu Superior -->
	 <?php include 'assets/php/menu_superior.php' ?>

<div class="clearfix"></div>
	
  <div class="content-wrapper">
    <div class="container-fluid">
		
	<!-- Inicio Secciones -->
      <div class="row">
        <div class="col-lg-12">
		  <div> <!--Please remove the height before using this page-->
		      <h1>Bienvenido</h1>
          <p>Desde esta aplicación tiene acceso a la gestión de usuarios y a los reportes generados por los módulos de Autoatención SuperSubsidio.</p>
		  </div>
        </div>
      </div>
		
	<div class="row">
	<?php
		if($funcion_user == 'Administrador'){
			echo '	
	<div class="col-12 col-lg-2">
	    <div class="card">
			<div class="card-body text-center">
				<div class="dash-icon-badge"><i class="icon-people"></i></div>
				<h4 class="card-title">Gestión de Usuarios</h4>
				<h6>Creación, edición o eliminación de los usuarios que pueden acceder a este software.</h6>
				<hr>
                <a href="usuarios.php" class="btn btn-primary btn-sm"><i class="icon-people icons"></i> Gestión de Usuarios</a>
			</div>
		</div>
	   </div>
	  ';
			  }
		?>
		
		<div class="col-12 col-lg-2">
	    <div class="card">
			<div class="card-body text-center">
				<div class="dash-icon-badge"><i class="zmdi zmdi-edit"></i></div>
				<h4 class="card-title">Programación</h4>
				<h6>Edite que imágenes o video se mostrara en la aplicación</h6>
				<hr>
                <a href="programacion.php" class="btn btn-primary btn-sm"><i class="zmdi zmdi-edit"></i> Edición</a>
			</div>
		</div>
	   </div>
	   
		<div class="col-12 col-lg-2">
	    <div class="card">
			<div class="card-body text-center">
				<div class="dash-icon-badge"><i class="zmdi zmdi-image"></i></div>
				<h4 class="card-title">Contenido</h4>
				<h6>Suba, edite y elimine las imágenes que usara en la aplicación</h6>
				<hr>
                <a href="contenido.php" class="btn btn-primary btn-sm"><i class="zmdi zmdi-image"></i> Contenido</a>
			</div>
		</div>
	   </div>
		
		<div class="col-12 col-lg-2">
	    <div class="card">
			<div class="card-body text-center">
				<div class="dash-icon-badge"><i class="fa fa-crop"></i></div>
				<h4 class="card-title">Frame</h4>
				<h6>edite que pagina se visualizara en la aplicación</h6>
				<hr>
                <a href="frame.php" class="btn btn-primary btn-sm"><i class="fa fa-crop"></i> Frame</a>
			</div>
		</div>
	   </div>
		<?php
			  if($funcion_user == 'Administrador'){
				  echo '
	   <div class="col-12 col-lg-2">
	    <div class="card">
			<div class="card-body text-center">
				<div class="dash-icon-badge"><i class="zmdi zmdi-chart"></i></div>
				<h4 class="card-title">Estadísticas</h4>
				<h6>Visualice el alcance de uso de cada módulo que se ha usado.</h6>
				<hr>
                <a href="reporte_tabla.php" class="btn btn-primary btn-sm"><i class="zmdi zmdi-chart"></i> Estadísticas</a>
			</div>
		</div>
	   </div>
	   ';
			  }
			?>
		
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