var inactividad;

	function inicio() {
		// IDT_TIMEOUT_MS lo inyecta el PHP de la página (configurable desde
		// el admin); 900000ms es el valor de respaldo si no llega.
		var tiempo = (typeof IDT_TIMEOUT_MS !== 'undefined') ? IDT_TIMEOUT_MS : 900000;
		inactividad = setTimeout(function(){ location.href="index.php"; }, tiempo);
	}

	function parar(){
		clearTimeout(inactividad);
		inicio();
		console.log('Click!');
	}