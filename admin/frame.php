<?php
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

/* datos tabla de Frame */

$sql_categoria = "SELECT * FROM `frame`";
$consulta_categoria = mysqli_query($conexion, $sql_categoria);

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>
  <title>Frame de Contenido - IDT App</title>
  <!--favicon-->
  <link rel="icon" href="assets/images/Favicon.png" type="image/x-icon">
  <!-- jquery steps CSS-->
  <link rel="stylesheet" type="text/css" href="assets/plugins/jquery.steps/css/jquery.steps.css">
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
			<h4 class="page-title">Frame de Contenido</h4>
	   </div>
     </div>
    <!-- Fin Migas de pan-->
		
	<!-- Inicio Secciones -->

<div class="row">
		<?php
			while($datos_categoria = mysqli_fetch_array($consulta_categoria)){
				$id = $datos_categoria['ID'];
				$categoria = $datos_categoria['URL'];
		?>
		<div class="col-lg-6">
          <div class="card">
			<div class="card-header text-uppercase">URL de Contenido</div>
            <div class="card-body">
				<p class="text-left"><strong>URL: </strong><?php echo $categoria; ?></p>
			  <a class="btn btn-info waves-effect waves-light m-1" href="editar_frame.php"><i class='fa fa-handshake-o'></i> Editar URL</a>
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
  
  <script>
  	function asegurar (){
		  rc = confirm("¿Está seguro de eliminar la programación?");
		  return rc;
	  }
  </script>
  <!-- Bootstrap core JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
	

  <!-- sidebar-menu js -->
  <script src="assets/js/sidebar-menu.js"></script>
  
  <!-- Custom scripts -->
  <script src="assets/js/app-script.js"></script>
	
	  <!--Data Tables js-->
  <script src="assets/plugins/bootstrap-datatable/js/jquery.dataTables.min.js"></script>
  <script src="assets/plugins/bootstrap-datatable/js/dataTables.bootstrap4.min.js"></script>
  <script src="assets/plugins/bootstrap-datatable/js/dataTables.buttons.min.js"></script>
  <script src="assets/plugins/bootstrap-datatable/js/buttons.bootstrap4.min.js"></script>
  <script src="assets/plugins/bootstrap-datatable/js/jszip.min.js"></script>
  <script src="assets/plugins/bootstrap-datatable/js/pdfmake.min.js"></script>
  <script src="assets/plugins/bootstrap-datatable/js/vfs_fonts.js"></script>
  <script src="assets/plugins/bootstrap-datatable/js/buttons.html5.min.js"></script>
  <script src="assets/plugins/bootstrap-datatable/js/buttons.print.min.js"></script>
  <script src="assets/plugins/bootstrap-datatable/js/buttons.colVis.min.js"></script>

</body>
</html>