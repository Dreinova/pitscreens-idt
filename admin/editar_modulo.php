<?php
date_default_timezone_set('America/Bogota');

include 'assets/php/Conexion_DB.php';

session_start();

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


/* datos tabla de usuarios */

$edit_id = $_GET['id'];
$fila = mysqli_query($conexion, "SELECT * FROM modulos WHERE ID = '$edit_id'");

/* Editar Módulo */

/* datos programación */

$consulta_programacion = mysqli_query($conexion,"SELECT * FROM `lista-reproduccion` ");

if(isset($_POST['actualizar_modulo'])) { 
	
	$id_modulo = $_POST["id_modulo"];
	$nombre_modulo = $_POST["modulo"];
	$ubicacion = $_POST["ubicacion"];
	$programacion = $_POST["programacion"];
	$fecha = date("Y-m-d H:i:s");
	
	echo $edit_id;

		/*$actualizar_modulo = mysqli_query($conexion, "UPDATE modulos SET nombre_modulo = '$nombre_modulo', ubicacion = '$ubicacion', programacion = '$programacion', fecha_modificacion = '$fecha' WHERE ID = '$id_modulo' ");*/
	
		$actualizar_modulo = mysqli_query($conexion, "UPDATE `modulos` SET `nombre_modulo` = '$nombre_modulo', `ubicacion` = '$ubicacion', `fecha_modificacion` = '$fecha', `Usuario` = '$name_user' WHERE `modulos`.`ID` = $id_modulo");
		
		if($actualizar_modulo){
			header("Location: modulos.php");
			$mensaje[]= 'Se ha Actualizado el módulo.';
		}
		else{
			$mensaje[]= 'No se ha guardado el módulo, intentelo mas tarde.';
		}
 } 

mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>
  <title>Edición de Usuario - SuperSubsidio App by Electronika</title>
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
   <div id="pageloader-overlay" class="visible incoming"><div class="loader-wrapper-outer"><div class="loader-wrapper-inner" ><div class="loader"></div></div></div></div>
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
		
	<div class="row pt-2 pb-2">
        <div class="col-sm-9">
			<h4 class="page-title">Editar usuario Usuario</h4>
	   </div>
     </div>
		
	<div class="row">
		<div class="col-lg-12">
        	<div class="card">
            	<div class="card-body">
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" >
						<input class="form-control" type="hidden" name="id_modulo" value="<?php echo $edit_id; ?>">
						<?php
						foreach ($fila as $datos_modulo){ 
						?>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Nombre del módulo</label>
                            <div class="col-lg-9">
                                
								<input class="form-control" type="text" maxlength="50" name="modulo" value="<?php echo $datos_modulo['nombre_modulo'] ?>" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Ubicación</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" name="ubicacion" value="<?php echo $datos_modulo['ubicacion'] ?>" required>
                            </div>
                        </div>
						
						<!--<div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Seleccione una programación</label>
                            <div class="col-lg-9">
								<select class="form-control" id="default-select" name="Lista" required>
								<option value="" selected disabled hidden>Seleccione una opción</option>
								<?php
									foreach ($consulta_programacion as $datos_sliders){ 
										$ID_slider = $datos_sliders['ID'];
										$nombre_slider = $datos_sliders['Nombre'];
								?>
								<option value="<?php echo $ID_slider; ?>"><?php echo $nombre_slider; ?></option>
								<?php } ?>
							</select>
                            </div>
                        </div>-->
						
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"></label>
                            <div class="col-lg-9">
								<a href="modulos.php" class="btn btn-secondary">Cancelar</a>
                                <input type="submit" name="actualizar_modulo" class="btn btn-primary" value="Actualizar módulo">
                            </div>
                        </div>
						<?php
						}
						?>
						<div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">
								<?php 
								if(!empty($mensaje)){
									foreach($mensaje as $erro){
										echo $erro;
									}
								}
								?>
							</label>
                        </div>
                    </form>
			  	</div>
			</div>
		</div>
	</div>
		

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
           © Electronika - 2019
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
