var inactividad;

	function inicio() {
		inactividad = setTimeout(function(){ location.href="index.php"; }, 60000);
	}

	function parar(){
		clearTimeout(inactividad);
		inicio();
		console.log('Click!');
	}