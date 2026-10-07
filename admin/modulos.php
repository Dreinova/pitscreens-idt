<?php
date_default_timezone_set('America/Bogota');
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


/* datos tabla de módulos */

$sql_modulos = "SELECT * FROM modulos";
$consulta_modulos = mysqli_query($conexion, $sql_modulos);

/* Nuevo Módulo */

if (isset($_POST['nuevo_modulo'])) { 
    
      // variables para subir a la db el módulo nuevo
        $nombre_modulo = $_POST["modulo"];
		$ubicacion = $_POST["ubicacion"];
		$fecha = date("Y-m-d H:i");
	
		$nuevo_user = "INSERT INTO `modulos` (`nombre_modulo`, `ubicacion`, `fecha_modificacion`, `Usuario`) VALUES ('$nombre_modulo', '$ubicacion', '$fecha', '$name_user')"; 
		$guardar_modulo = mysqli_query($conexion, $nuevo_user);
		
		if($guardar_modulo){
			header("Location: modulos.php");
		}
		else{
			$errores = "<p>No se ha guardado correctamente la pantalla.</p>";
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
  <title>Registro de Pantallas - IDT App</title>
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
			<h4 class="page-title">Nueva Pantalla</h4>
	   </div>
     </div>
		
	<div class="row">
		<div class="col-lg-12">
        	<div class="card">
            	<div class="card-body">
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" >
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Nombre de la pantalla</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" maxlength="50" name="modulo" value="" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Ubicación</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" name="ubicacion" value="" required>
                            </div>
                        </div>
                       
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"></label>
                            <div class="col-lg-9">
                                <input type="reset" class="btn btn-secondary" value="Cancelar">
                                <input type="submit" name="nuevo_modulo" class="btn btn-primary" value="Nueva pantalla">
                            </div>
                        </div>
						
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
		
		
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
			<h4 class="page-title">Pantallas Registradas</h4>
	   </div>
     </div>


      <div class="row">

        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
			  <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Ubicación</th>
                    <th scope="col">Fecha de Modificación</th>
					<th scope="col">Edición</th>
                  </tr>
                </thead>
				  
                <tbody>
				<?php
					foreach ($consulta_modulos as $datos_modulos){ ?>
                  <tr>
					<td><?php echo $datos_modulos['ID']; ?></td>
                    <td><?php echo $datos_modulos['nombre_modulo']; ?></td>
                    <td><?php echo $datos_modulos['ubicacion']; ?></td>
					<td><?php echo $datos_modulos['fecha_modificacion']; ?></td>
                    <?php
						$id = $datos_modulos['ID'];
						echo "<td><a href='editar_modulo.php?id=$id'><i class='icon-user-following icons'></i></a>
						<a href='assets/php/eliminar_modulo.php?id=$id''><i class='icon-user-unfollow icons'></i></a>";
						echo "</tr>"; 
					?>
                  </tr>
                 <?php } ?>
					
                </tbody>
              </table>
            </div>
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
