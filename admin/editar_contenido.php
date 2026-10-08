<?php
/**
 * editar_contenido.php — Módulo Administrador
 *
 * Edita una publicación (un archivo de la biblioteca dentro de una lista
 * de reproducción): estado, orden, lista y ventana de publicación
 * (Fecha_Inicio / Fecha_Fin, vacías = sin límite) — el kiosco lo muestra
 * y lo retira solo según esa ventana.
 */
date_default_timezone_set('America/Bogota');
include 'assets/php/Conexion_DB.php';
include 'assets/php/contenido_helpers.php';

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

$consulta_programacion = mysqli_query($conexion,"SELECT * FROM `lista-reproduccion` ");

/* Consulta imagen */

$id = (int) ($_GET['id'] ?? $_POST['ID'] ?? 0);
$consulta_imagen = mysqli_query($conexion,"SELECT * FROM `contenido` WHERE ID = $id");
$datos_img = mysqli_fetch_array($consulta_imagen);

if (!$datos_img) {
	header("Location: contenido.php");
	exit();
}

$ID_img = $datos_img["ID"];
$url_img = $datos_img["URL"];
$tipo = $datos_img["Tipo"];
$estado = $datos_img["Estado"];
$orden = $datos_img["Orden"];
$lista_anterior = $datos_img["Lista-Reproduccion"];
$fecha_inicio = $datos_img["Fecha_Inicio"];
$fecha_fin = $datos_img["Fecha_Fin"];

// A dónde volver al guardar/cancelar: la página del admin desde donde se
// llegó (contenido.php o editar_modulo.php) — nunca una URL externa.
$volver = 'contenido.php';
$origen = $_POST['volver'] ?? ($_SERVER['HTTP_REFERER'] ?? '');
$origen_pagina = basename((string) parse_url($origen, PHP_URL_PATH));
if (in_array($origen_pagina, ['contenido.php', 'editar_modulo.php'], true)) {
	$origen_query = parse_url($origen, PHP_URL_QUERY);
	$volver = $origen_pagina . ($origen_query ? '?' . $origen_query : '');
}

/* Actualizar imagen */

if(isset($_POST['Actualizar'])){

	$Estado = $_POST["Estado"] == '1' ? 1 : 0;
	$orden = (int) $_POST["orden"];
	$lista_reproduccion = (int) $_POST["Lista"];
	$fecha = date("Y-m-d H:i:s");
	$fecha_inicio_sql = fecha_sql($_POST["fecha_inicio"] ?? '');
	$fecha_fin_sql = fecha_sql($_POST["fecha_fin"] ?? '');

	if ($fecha_inicio_sql !== 'NULL' && $fecha_fin_sql !== 'NULL' && $fecha_fin_sql <= $fecha_inicio_sql) {
		$errores = ["<p>La fecha de fin debe ser posterior a la de inicio.</p>"];
	}
	else {
		$actualizar_img = mysqli_query($conexion,"UPDATE `contenido` SET `Estado` = '$Estado', `Orden` = '$orden', `Lista-Reproduccion` = '$lista_reproduccion', `Fecha_Inicio` = $fecha_inicio_sql, `Fecha_Fin` = $fecha_fin_sql, `Fecha_Modificación` = '$fecha', `Usuario` = '$name_user' WHERE `ID` = $id");

		if ($actualizar_img){
			// Marca como modificada tanto la lista nueva como la anterior (si
			// cambió de lista), para que la sincronización en vivo del kiosco
			// detecte el cambio sin importar a cuál lista quedó asignado.
			marcar_lista_modificada($conexion, $lista_reproduccion);
			if ($lista_anterior && $lista_anterior != $lista_reproduccion) {
				marcar_lista_modificada($conexion, $lista_anterior);
			}
			header("Location: " . $volver);
			exit();
		}
		else{
			$errores = ["<p>No se han guardado los cambios, intentelo mas tarde.</p>"];
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
  <title>Galeria - IDT App</title>
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


<body class="bg-theme bg-theme9">

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
			<h4 class="page-title">Editar publicación</h4>
	   </div>
     </div>
    <!-- Fin Migas de pan-->
		
	<!-- Inicio Secciones -->
		
<div class="row">
        <div class="col-lg-12">
          <div class="card">
			  <div class="card-header text-uppercase"><i class="fa fa-file-image-o"></i> <?php echo htmlspecialchars($url_img); ?></div>
            <div class="card-body">
              
		<form action="editar_contenido.php" method="POST">
			<input type="hidden" name="ID" value="<?php echo $id; ?>">
			<input type="hidden" name="volver" value="<?php echo htmlspecialchars($volver); ?>">

			<div class="row">
				<div class="col-12 col-lg-4">
					<?php $src_img = 'assets/galeria/' . rawurlencode($url_img); ?>
					<?php if ($tipo == 'video'): ?>
						<video width="100%" controls preload="metadata"><source src="<?php echo $src_img; ?>" type="video/mp4"></video>
					<?php else: ?>
						<a href="<?php echo $src_img; ?>" data-fancybox="images" data-caption="<?php echo htmlspecialchars($url_img); ?>">
							<img src="<?php echo $src_img; ?>" class="lightbox-thumb img-thumbnail" alt="">
						</a>
					<?php endif; ?>
				</div>

				<div class="col-12 col-lg-8">
					<div class="form-row">
						<div class="form-group col-md-6">
							<label>Desde</label>
							<input class="form-control" type="datetime-local" name="fecha_inicio" value="<?php echo fecha_input($fecha_inicio); ?>">
							<small class="text-muted">Vacío = desde ya. Antes de esta fecha no se muestra.</small>
						</div>
						<div class="form-group col-md-6">
							<label>Hasta</label>
							<input class="form-control" type="datetime-local" name="fecha_fin" value="<?php echo fecha_input($fecha_fin); ?>">
							<small class="text-muted">Vacío = sin fin. Al llegar esta fecha se despublica solo.</small>
						</div>
						<div class="form-group col-md-4">
							<label>Estado</label>
							<select class="form-control" name="Estado">
								<option value="1" <?php echo $estado == '1' ? 'selected' : ''; ?>>Activo</option>
								<option value="0" <?php echo $estado == '1' ? '' : 'selected'; ?>>Inactivo</option>
							</select>
						</div>
						<div class="form-group col-md-3">
							<label>Orden</label>
							<input class="form-control" type="number" name="orden" value="<?php echo (int) $orden; ?>" required>
						</div>
						<div class="form-group col-md-5">
							<label>Lista de reproducción</label>
							<select class="form-control" name="Lista" required>
								<?php foreach ($consulta_programacion as $datos_sliders){ ?>
								<option value="<?php echo (int) $datos_sliders['ID']; ?>" <?php echo $datos_sliders['ID'] == $lista_anterior ? 'selected' : ''; ?>><?php echo htmlspecialchars($datos_sliders['Nombre']); ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<p class="mb-0">Estado ahora:
						<?php list(, $etq_estado, $clase_estado) = estado_publicacion($datos_img); ?>
						<span class="badge <?php echo $clase_estado; ?>"><?php echo $etq_estado; ?></span>
					</p>
				</div>
			</div>

			<?php
				if(!empty($errores)):
					foreach($errores as $erro):
						echo $erro;
					endforeach;
				endif;
			?>
			<div class="form-footer">
				<a href="<?php echo htmlspecialchars($volver); ?>" class="btn btn-secondary">Cancelar</a>
				<input type="submit" class="btn btn-success" name="Actualizar" value="Guardar">
			</div>

        </form>  
				
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
	
<?php 
	mysqli_close($conexion);
?>

</body>
</html>