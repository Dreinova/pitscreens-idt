var inactividad;

	function inicio() {
		inactividad = setTimeout(function(){ location.href="index.php"; }, 900000);
	}

	function parar(){
		clearTimeout(inactividad);
		inicio();
		console.log('Click!');
	}