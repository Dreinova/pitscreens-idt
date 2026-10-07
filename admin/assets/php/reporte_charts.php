<?php
include 'assets/php/Conexion_DB.php';

session_start();

setlocale(LC_TIME, 'es_co');

/*  verificacion login  */
if(!isset($_SESSION['logeado'])):
	header('Location: index.php');
endif;

/* datos de usuario */

$id = $_SESSION['id_Correo'];
$sql = "SELECT * FROM usuarios WHERE id = '$id' ";
$resultado = mysqli_query($conexion, $sql);
$datos = mysqli_fetch_array($resultado);


/* datos tabla de usuarios */

$consulta_users = "SELECT * FROM usuarios";
$query = mysqli_query($conexion, $consulta_users);
$array = mysqli_fetch_array($query);

/* Consulta uso General */

$UsoXSem_F1_chart = "SELECT * FROM `esencias` WHERE `Fragancia`='Fragancia 1'";
$result1 = mysqli_query($conexion , $UsoXSem_F1_chart);
$numero1 = mysqli_num_rows($result1);

$UsoXSem_F2_chart = "SELECT * FROM `esencias` WHERE `Fragancia`='Fragancia 2'";
$result2 = mysqli_query($conexion , $UsoXSem_F2_chart);
$numero2 = mysqli_num_rows($result2);

$UsoXSem_F3_chart = "SELECT * FROM `esencias` WHERE `Fragancia`='Fragancia 3'";
$result3 = mysqli_query($conexion , $UsoXSem_F3_chart);
$numero3 = mysqli_num_rows($result3);

$UsoXSem_F4_chart = "SELECT * FROM `esencias` WHERE `Fragancia`='Fragancia 4'";
$result4 = mysqli_query($conexion , $UsoXSem_F4_chart);
$numero4 = mysqli_num_rows($result4);

/* Consulta uso x mes y año */
$anio = date("Y");
$mes = date("n");

if(isset($_POST['consultar_fecha'])){
	
$anio = $_POST['anio'];
$mes = $_POST['mes'];

$consulta_mes_F1 = mysqli_query($conexion, "SELECT COUNT(ID) AS total_f1 FROM esencias where YEAR(Fecha)=$anio AND MONTH(Fecha)=$mes AND Fragancia='Fragancia 1' LIMIT 1");
$conteoF1 = mysqli_fetch_array($consulta_mes_F1);
	
$consulta_mes_F2 = mysqli_query($conexion, "SELECT COUNT(ID) AS total_f2 FROM esencias where YEAR(Fecha)=$anio AND MONTH(Fecha)=$mes AND Fragancia='Fragancia 2' LIMIT 1");
$conteoF2 = mysqli_fetch_array($consulta_mes_F2);
	
$consulta_mes_F3 = mysqli_query($conexion, "SELECT COUNT(ID) AS total_f3 FROM esencias where YEAR(Fecha)=$anio AND MONTH(Fecha)=$mes AND Fragancia='Fragancia 3' LIMIT 1");
$conteoF3 = mysqli_fetch_array($consulta_mes_F3);
	
$consulta_mes_F4 = mysqli_query($conexion, "SELECT COUNT(ID) AS total_f4 FROM esencias where YEAR(Fecha)=$anio AND MONTH(Fecha)=$mes AND Fragancia='Fragancia 4' LIMIT 1");
$conteoF4 = mysqli_fetch_array($consulta_mes_F4);

}

else{

$consulta_mes_F1 = mysqli_query($conexion, "SELECT COUNT(ID) AS total_f1 FROM esencias where YEAR(Fecha)=$anio AND MONTH(Fecha)=$mes AND Fragancia='Fragancia 1' LIMIT 1");
$conteoF1 = mysqli_fetch_array($consulta_mes_F1);
	
$consulta_mes_F2 = mysqli_query($conexion, "SELECT COUNT(ID) AS total_f2 FROM esencias where YEAR(Fecha)=$anio AND MONTH(Fecha)=$mes AND Fragancia='Fragancia 2' LIMIT 1");
$conteoF2 = mysqli_fetch_array($consulta_mes_F2);
	
$consulta_mes_F3 = mysqli_query($conexion, "SELECT COUNT(ID) AS total_f3 FROM esencias where YEAR(Fecha)=$anio AND MONTH(Fecha)=$mes AND Fragancia='Fragancia 3' LIMIT 1");
$conteoF3 = mysqli_fetch_array($consulta_mes_F3);
	
$consulta_mes_F4 = mysqli_query($conexion, "SELECT COUNT(ID) AS total_f4 FROM esencias where YEAR(Fecha)=$anio AND MONTH(Fecha)=$mes AND Fragancia='Fragancia 4' LIMIT 1");
$conteoF4 = mysqli_fetch_array($consulta_mes_F4);
	
}


$fecha = DateTime::createFromFormat('!m', $mes);
$mes = strftime("%B", $fecha->getTimestamp());

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
			<h4 class="page-title">Inicio</h4>
	   </div>
     </div>
    <!-- Fin Migas de pan-->
		
	<!-- Inicio Secciones -->

    
     <div class="row">
		 
        <div class="col-lg-6 col-xl-7">
          <div class="card">
            <div class="card-header text-uppercase">Uso de Modulo Fragancias</div>
            <div class="card-body">
				
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
				<select name="anio" class="browser-default custom-select col-sm-3">
					<?php
						for($i=2019;$i<2030;$i++){
							if($i == $anio){
								echo '<option value="'.$i.'" selected>'.$i.'</option>';
							}else{
								echo '<option value="'.$i.'">'.$i.'</option>';
							}
						}
					?>
            	</select>
				
				<select name="mes" class="browser-default custom-select col-sm-3">
					<option value="1" selected>Enero</option>
					<option value="2" >Febrero</option>
					<option value="3" >Marzo</option>
					<option value="4" >Abril</option>
					<option value="5" >Mayo</option>
					<option value="6" >Junio</option>
					<option value="7" >Julio</option>
					<option value="8" >Agosto</option>
					<option value="9" >Septiembre</option>
					<option value="10" >Octubre</option>
					<option value="11" >Noviembre</option>
					<option value="12" >Diciembre</option>
				</select>
				
				<input type="submit" class="btn btn-success" name="consultar_fecha" value="Consultar Fecha">
				
			</form>
			   
                 <canvas id="barChart"></canvas>
             </div>
          </div>
        </div>
		 
		 <div class="col-lg-6 col-xl-5">
          <div class="card">
            <div class="card-header text-uppercase">Porcentaje de Uso General</div>
            <div class="card-body">
              <canvas id="pieChart" height="240"></canvas>
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
  <!-- Chart JS -->
  <script src="assets/plugins/Chart.js/Chart.min.js"></script>
<!--	<script src="assets/plugins/Chart.js/chartjs-script.js"></script>-->
  			
	
	<script>
	
		(function(window, document, $, undefined) {
	  "use strict";
	$(function() {
		
		if ($('#pieChart').length) {
			var ctx = document.getElementById("pieChart").getContext('2d');
			var myChart = new Chart(ctx, {
				type: 'pie',
				data: {
					labels: ["Fragancia 1", "Fragancia 2", "Fragancia 3", "Fragancia 4"],
					datasets: [{
						backgroundColor: [
							"rgb(191, 51, 118, 0.7)",
							"rgba(219, 150, 0, 0.7)",
							"rgba(74, 114, 178, 0.7)",
							"rgba(95, 161, 153, 0.7)"
						],
						data: [<?php echo $numero1; ?>, <?php echo $numero2; ?>, <?php echo $numero3; ?>, <?php echo $numero4; ?>],
						borderWidth: [0, 0, 0, 0]
					}]
				},
			options: {
			   legend: {
				 position :"right",	
				 display: true,
				    labels: {
					  fontColor: '#ddd',  
					  boxWidth:15
				   }
				}
			   }
			});
		}

		
		if ($('#barChart').length) {
			var ctx = document.getElementById("barChart").getContext('2d');
			var myChart = new Chart(ctx, {
				type: 'bar',
				data: {
					labels: ['<?php echo $mes; ?>'],
					datasets: [
						{
						label: 'Fragancia 1',
						data: ['<?php echo $conteoF1['total_f1']; ?>'],
						backgroundColor: "rgb(191, 51, 118, 0.5)"
						}, 
						{
						label: 'Fragancia 2',
						data: ['<?php echo $conteoF2['total_f2']; ?>'],
						backgroundColor: "rgba(219, 150, 0, 0.5)"
						},
						{
						label: 'Fragancia 3',
						data: ['<?php echo $conteoF3['total_f3']; ?>'],
						backgroundColor: "rgba(74, 114, 178, 0.5)"
						},
						{
						label: 'Fragancia 4',
						data: ['<?php echo $conteoF4['total_f4']; ?>'],
						backgroundColor: "rgba(95, 161, 153, 0.5)"
						}
					]
				},
			options: {
				legend: {
				  display: true,
				  labels: {
					fontColor: '#ddd',  
					boxWidth:40
				  }
				},
				tooltips: {
				  enabled:false
				},	
			  scales: {
				  xAxes: [{
					  barPercentage: .5,
					ticks: {
						beginAtZero:true,
						fontColor: '#ddd'
					},
					gridLines: {
					  display: true ,
					  color: "rgba(221, 221, 221, 0.08)"
					},
				  }],
				   yAxes: [{
					ticks: {
						beginAtZero:true,
						fontColor: '#ddd'
					},
					gridLines: {
					  display: true ,
					  color: "rgba(221, 221, 221, 0.08)"
					},
				  }]
				 }

			 }
			});
		}




		


	});

})(window, document, window.jQuery);
	
	
	</script>
	
	<?php
	
	mysqli_close($conexion);
	
	?>

</body>
</html>