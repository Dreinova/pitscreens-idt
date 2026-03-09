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

/* datos de usuario Logeado */

$id = $_SESSION['id_Correo'];
$sql = "SELECT usuarios.ID, usuarios.Nombre, usuarios.Correo, usuarios.Foto_Usuario, funciones_usuario.Funcion FROM usuarios INNER JOIN funciones_usuario ON usuarios.Funcion = funciones_usuario.ID WHERE usuarios.ID = '$id' ";
$resultado = mysqli_query($conexion, $sql);
$datos = mysqli_fetch_array($resultado);

$name_user = $datos['Nombre'];
$mail_user = $datos['Correo'];
$foto_user = $datos['Foto_Usuario'];
$funcion_user = $datos['Funcion'];

$consulta_programacion = mysqli_query($conexion,"SELECT * FROM `reco`");

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>
  <title>Reporte General - IDT App by Electronika</title>
  <!--favicon-->
  <link rel="icon" href="assets/images/Favicon.png" type="image/x-icon">
  <!-- simplebar CSS-->
  <link href="assets/plugins/simplebar/css/simplebar.css" rel="stylesheet"/>
  <!-- Bootstrap core CSS-->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet"/>
  <!--Data Tables -->
  <link href="assets/plugins/bootstrap-datatable/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
  <link href="assets/plugins/bootstrap-datatable/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css">
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
			<h4 class="page-title">Reporte General de Uso</h4>
	   </div>
     </div>
    <!-- Fin Migas de pan-->
		
	<!-- Inicio Secciones -->
	<div class="row">
		 <div class="col-lg-12">
          <div class="card">
            <div class="card-header"><i class="fa fa-edit"></i> Configuración Actual</div>
            <div class="card-body">
              <div class="table-responsive">
              <table id="example" class="table table-bordered">
                  <thead>
                    <tr>
                      <th style="text-align: center;" scope="col">Módulo</th>
					  <th style="text-align: center;" scope="col">Rango de edad</th>
					  <th style="text-align: center;" scope="col">Genero</th>
					  <th style="text-align: center;" scope="col">fecha de interacción</th>
                      <th style="text-align: center;" scope="col">Tiempo promedio</th>
                    </tr>
                  </thead>
				  
				   <tbody>
				<?php
					foreach ($consulta_programacion as $row){
						
						$Edad = $row['Edad'];
						$Modulo = $row['modulo'];
						$Genero = $row['Genero'];
						if($Genero == 'Male'){
							$Genero = 'Masculino';
							$ico = 'fa fa-male fa-xs';
						}
						if($Genero == 'Female'){
							$Genero = 'Femenino';
							$ico = 'fa fa-female fa-xs';
						}
						$fecha = $row['Hora'];
						$fecha = date_format (new DateTime($fecha), 'd-m-Y h:i A');
						$Tiempo = $row['Tiempo'];
				?>
                  <tr>
					<td style="text-align: center;"><?php echo $Modulo; ?></td>
					  <td style="text-align: center;"><?php echo $Edad; ?></td>
					<td style="text-align: center;"><?php echo $Genero; ?> <i class="<?php echo $ico; ?>"></i></td>
					<td style="text-align: center;"><?php echo $fecha; ?></td>
					<td style="text-align: center;"><?php echo $Tiempo; ?> Minutos
						<div class="progress" style="height:10px;">
							<div class="progress-bar bg-info" style="width:<?php echo $Tiempo; ?>%" aria-valuenow="<?php echo $Tiempo; ?>" aria-valuemin="0" aria-valuemax="60"></div>
						</div>
                    </td>
                  </tr>
                 <?php }?>
				 	</tbody>
					
                </table>
            </div>
            </div>
          </div>
        </div>
	</div>
		
	<!-- End Row-->
		
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
          Desarrollado por Electronika © 2020 | Todos los derechos reservados.
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
  <script src="assets/plugins/bootstrap-datatable/lang/Spanish.json"></script>


    <script>
		
	$('#example').DataTable( {
    	language: { url: 'assets/plugins/bootstrap-datatable/lang/Spanish.json' },
		dom: 'Bfrtip', buttons: [ 'copy', 'excel', 'pdf', 'print', 'colvis' ]
	} );
		
	$('#example2').DataTable( {
    	language: { url: 'assets/plugins/bootstrap-datatable/lang/Spanish.json' },
		dom: 'Bfrtip', buttons: [ 'copy', 'excel', 'pdf', 'print', 'colvis' ]
	} );

    </script>

</body>
</html>