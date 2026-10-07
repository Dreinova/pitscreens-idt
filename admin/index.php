<?php
/**
 * index.php — Módulo Administrador
 *
 * Página de inicio de sesión del panel administrativo.
 * Permite autenticar a los usuarios del sistema usando nombre de usuario
 * o correo electrónico junto con su contraseña (almacenada como hash MD5).
 *
 * Flujo de autenticación:
 *   1. Si el usuario ya tiene sesión activa, redirige directamente a inicio.php.
 *   2. Al enviar el formulario, valida que los campos no estén vacíos.
 *   3. Verifica que el usuario/correo exista en la tabla `usuarios`.
 *   4. Compara la contraseña ingresada (en MD5) con la almacenada en la BD.
 *   5. Si es correcta, inicia la sesión y redirige al dashboard.
 *
 * Variables de sesión que se establecen al autenticarse:
 *   $_SESSION['logeado']   → true (indicador de sesión activa)
 *   $_SESSION['id_Correo'] → ID del usuario autenticado
 *   $_SESSION['Funcion']   → Rol del usuario (Administrador / Colaborador)
 */

include 'assets/php/Conexion_DB.php';

session_start();

/* Redirige al dashboard si ya hay una sesión activa */
if(isset($_SESSION['logeado'])){
	header('Location: inicio.php');
}

if(isset($_POST['btn-entrar'])){
	$errores = array();

	// Sanear entradas para prevenir inyección básica en MySQLi
	$Usuario   = mysqli_escape_string($conexion, $_POST['Usuario']);
	$contrasena = mysqli_escape_string($conexion, $_POST['contrasena']);

	// Guardar credenciales en cookies por 1 hora si el usuario marcó "Mantener sesión"
	if(isset($_POST['recuerda-contrasena'])){
		setcookie('Correo',     $Usuario,        time()+3600);
		setcookie('contrasena', md5($contrasena), time()+3600);
	}

	// Validar que los campos no estén vacíos
	if(empty($Usuario) or empty($contrasena)){
		$errores[] = "<li> El campo Correo o Contraseña debe completarse.</li>";
	}
	else{
		// Primer paso: verificar si el usuario o correo existe en la BD
		$sql = "SELECT Nombre OR Correo FROM usuarios WHERE (Nombre='$Usuario' OR Correo='$Usuario') LIMIT 1";
		$resultado = mysqli_query($conexion, $sql);

		if(mysqli_num_rows($resultado) > 0){
			// Segundo paso: verificar que la contraseña (MD5) coincida
			$contrasena = md5($contrasena);
			$sql = "SELECT * FROM usuarios WHERE (Nombre='$Usuario' OR Correo='$Usuario') AND password = '$contrasena' LIMIT 1";

			$resultado = mysqli_query($conexion, $sql);

			if(mysqli_num_rows($resultado) == 1){
				// Autenticación exitosa: establecer variables de sesión y redirigir
				$datos = mysqli_fetch_array($resultado);
				mysqli_close($conexion);
				$_SESSION['logeado']   = true;
				$_SESSION['id_Correo'] = $datos['ID'];
				$_SESSION['Funcion']   = $datos['Funcion'];
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
  <title>Inicio de seción - IDT App</title>
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
		 		<span class="brand-plate">
		 			<img src="assets/images/logo-bogota.svg" class="login-logo" alt="Bogotá">
		 		</span>
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
