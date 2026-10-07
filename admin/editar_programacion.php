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
	exit();
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

/* consulta de Programación */

// El formulario de edición envía el POST a $_SERVER['PHP_SELF'], que no
// conserva el "?id=..." de la URL — por eso al guardar llega en el campo
// oculto "ID" (mayúscula) en vez de en la query string. Se contemplan los
// dos casos, y se castea a int (el ID siempre es numérico) para evitar un
// error de sintaxis SQL si llegara vacío.
$ID_Programacion = isset($_GET["id"]) ? (int) $_GET["id"] : (int) ($_POST["ID"] ?? 0);

$consulta_programacion = mysqli_query($conexion,"SELECT * FROM `lista-reproduccion` WHERE ID = $ID_Programacion ");
$datos_programacion = mysqli_fetch_array($consulta_programacion);

$Nombre_programacion = $datos_programacion['Nombre'];
$Modulo_programacion = $datos_programacion['Modulo'];
$fecha_inicio = $datos_programacion['Fecha-Inicio'];
$Estado_programacion = $datos_programacion['Estado'];

/* Módulos registrados, para el selector "General / módulo específico" */
$consulta_modulos_sel = mysqli_query($conexion, "SELECT * FROM `modulos`");

/* Actualizar programación */

if(isset($_POST['enviar'])){
	$ID_post = $_REQUEST["ID"];
	$Nombre = $_REQUEST["Nombre"];
	$Modulo = trim($_REQUEST["Modulo"]); // vacío = general (todos los módulos)
	// El campo datetime-local ya trae fecha y hora juntas ("2020-06-01T12:00");
	// solo hace falta cambiar la "T" por un espacio para el formato DATETIME de MySQL.
	$fecha = str_replace('T', ' ', $_REQUEST["Fecha"]);
	// No hay campo Estado en el formulario: se conserva el que ya tenía (lo
	// gestiona el sistema automáticamente, no se pide al editar).
	$Estado = $Estado_programacion;
	$hoy = date("Y-m-d H:i:s");

	$sql_modulo = $Modulo === '' ? "NULL" : "'$Modulo'";
	$sql_programacion = "UPDATE `lista-reproduccion` SET `Nombre` = '$Nombre', `Modulo` = $sql_modulo, `Fecha-Inicio` = '$fecha', `Estado` = '$Estado', `Fecha-Modificacion` = '$hoy', `Usuario` = '$name_user' WHERE `lista-reproduccion`.`ID` = $ID_post";

    $Actualizar_programacion = mysqli_query($conexion,$sql_programacion);
		
	if ($Actualizar_programacion){
		header("Location: programacion.php");
	}

	else{
		echo "paila";
		header("Location: programacion.php");
	}
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>
  <title>Edición de Inicio - IDT App</title>
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
			<h4 class="page-title">Edición de Inicio de la App</h4>
	   </div>
     </div>
    <!-- Fin Migas de pan-->
		
	<!-- Inicio Secciones -->
		
	<div class="row">
		<div class="col-lg-12">
        	<div class="card">
			<div class="card-header"><i class="fa fa-edit"></i>Nueva Programación</div>
            	<div class="card-body">
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
						
						<div class="row">
							
							<input type="hidden" name="ID" value="<?php echo $ID_Programacion; ?>">
							
							<div class="col-12 col-lg-6 col-xl-6">
								<div class="form-group row">
									<label class="col-lg-12 col-form-label form-control-label">Nombre del la programación</label>
									<div class="col-lg-12">
										<input class="form-control" type="text" maxlength="50" name="Nombre" value="<?php echo $Nombre_programacion; ?>" placeholder="Ingrese el nombre de la programación" required>
									</div>
								</div>
							</div>
							
							<div class="col-12 col-lg-6 col-xl-6">
								<div class="form-group row">
									<label class="col-lg-12 col-form-label form-control-label">Fecha de programación</label>
									<div class="col-lg-10">
										<input type="datetime-local" name="Fecha" class="form-control" value="<?php echo str_replace(' ', 'T', substr($fecha_inicio, 0, 16)); ?>">
									</div>
								</div>
							</div>

							<div class="col-12 col-lg-6 col-xl-6">
								<div class="form-group row">
									<label class="col-lg-12 col-form-label form-control-label">Alcance</label>
									<div class="col-lg-10">
										<select class="form-control" name="Modulo">
											<option value="" <?php echo empty($Modulo_programacion) ? 'selected' : ''; ?>>General (todas las pantallas)</option>
											<?php foreach ($consulta_modulos_sel as $mod_sel){ ?>
											<option value="<?php echo htmlspecialchars($mod_sel['nombre_modulo']); ?>" <?php echo ($Modulo_programacion === $mod_sel['nombre_modulo']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($mod_sel['nombre_modulo']); ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>

						</div>
                       
                        <?php 
							if(!empty($errores)){
								echo $errores;
							}
						?>
						<div class="form-footer">
							<a href="programacion.php" class="btn btn-secondary">Cancelar</a>
							<input type="submit" class="btn btn-success" name="enviar" value="Guardar">
						</div>
						
                    </form>
			  	</div>
			</div>
		</div>
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
          © Instituto Distrital de Turismo
        </div>
      </div>
    </footer>
	<!--End footer-->
	
  </div><!--End wrapper-->

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