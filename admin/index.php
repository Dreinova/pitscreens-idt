<?php

include 'assets/php/Conexion_DB.php';

session_start();

/*  verificacion login  */
if(isset($_SESSION['logeado'])){
	header('Location: inicio.php');
}

if(isset($_POST['btn-entrar'])){
	$errores = array();
	$Usuario = mysqli_escape_string($conexion, $_POST['Usuario']);
	$contrasena = mysqli_escape_string($conexion, $_POST['contrasena']);

	if(isset($_POST['recuerda-contrasena'])){
		setcookie('Correo', $Usuario, time()+3600);
		setcookie('contrasena', md5($contrasena), time()+3600);
	}

	if(empty($Usuario) or empty($contrasena)){
		$errores[] = "<li> El campo Correo o Contraseña debe completarse.</li>";
	}
	else{
		$sql = "SELECT Nombre OR Correo FROM usuarios WHERE (Nombre='$Usuario' OR Correo='$Usuario') LIMIT 1";
		$resultado = mysqli_query($conexion, $sql);		

		if(mysqli_num_rows($resultado) > 0){
			$contrasena = md5($contrasena); 
			$sql = "SELECT * FROM usuarios WHERE (Nombre='$Usuario' OR Correo='$Usuario') AND password = '$contrasena' LIMIT 1";

			$resultado = mysqli_query($conexion, $sql);

			if(mysqli_num_rows($resultado) == 1){
				$datos = mysqli_fetch_array($resultado);
				mysqli_close($conexion);
				$_SESSION['logeado'] = true;
				$_SESSION['id_Correo'] = $datos['ID'];
				$_SESSION['Funcion'] = $datos['Funcion'];
				header('Location: inicio.php');
				
			}
			else{
				$errores[] = "<li> Correo y contraseña no coinciden</li>";
			}
		}
		else{
			$errores[] = "<li> Usuario o Correo inexistente </li>";
		}

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
  <title>Inicio de seción - IDT App by Electronika</title>
  <!--favicon-->
  <link rel="icon" href="assets/images/Favicon.png" type="image/x-icon">
  <!-- Bootstrap core CSS-->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- animate CSS-->
  <link href="assets/css/animate.css" rel="stylesheet" type="text/css"/>
  <!-- Icons CSS-->
  <link href="assets/css/icons.css" rel="stylesheet" type="text/css"/>
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

 <div class="loader-wrapper"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>
	<div class="card card-authentication1 mx-auto my-5">
		<div class="card-body">
		 <div class="card-content p-2">
		 	<div class="text-center">
		 		<img src="assets/images/Logo_1.png" width="350"  alt="logo icon">
		 	</div>
		  <div class="card-title text-uppercase text-center py-3">Inicio de Sesión</div>
			 <?php 
				if(!empty($errores)):
					foreach($errores as $erro):
						echo $erro;
					endforeach;
				endif;
			?>
		    
			 <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			  <div class="form-group">
			  <label for="exampleInputUsername" class="sr-only">Nombre o Correo</label>
			   <div class="position-relative has-icon-right">
				  <input type="text" id="exampleInputUsername" class="form-control input-shadow" name="Usuario" value="" placeholder="Nombre  o Correo" autocomplete="off">
				  <div class="form-control-position">
					  <i class="icon-user"></i>
				  </div>
			   </div>
			  </div>
			  <div class="form-group">
			  <label for="exampleInputPassword" class="sr-only">Contraseña</label>
			   <div class="position-relative has-icon-right">
				  <input type="password" id="exampleInputPassword" class="form-control input-shadow" name="contrasena" value="" placeholder="Contraseña" autocomplete="off">
				  <div class="form-control-position">
					  <i class="icon-lock"></i>
				  </div>
			   </div>
			  </div>
			<div class="form-row">
			 <div class="form-group col-12">
                
				 <div class="card-footer text-center py-3">
					<div class="icheck-material-white">
						<input type="checkbox" id="user-checkbox" name="recuerda-contrasena" />
						<label for="user-checkbox">Mantener la sesión iniciada</label>
					</div>
				</div>
			 
			 </div>

			</div>
				
			<button type="submit" class="btn btn-light btn-block" name="btn-entrar">Iniciar sesión</button>
				
				 
			 </form>
			 
		   </div>
		  </div>
		
	     </div>
    
     <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->
	
	</div><!--wrapper-->
	
	
  <!-- Bootstrap core JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
	
  <!-- sidebar-menu js -->
  <script src="assets/js/sidebar-menu.js"></script>
  
  <!-- Custom scripts -->
  <script src="assets/js/app-script.js"></script>
  
</body>
</html>
