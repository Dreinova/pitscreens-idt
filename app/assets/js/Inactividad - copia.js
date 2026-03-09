var inactividad;

		function inicio() {
			inactividad = setTimeout(function(){ window.open('http://localhost/supersubsidio/app/assets/php/logout.php','_parent'); }, 300000);
		}

		function parar(){
			clearTimeout(inactividad);
			inicio();
		}