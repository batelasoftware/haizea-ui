<!DOCTYPE html>
<html>
<head>
		<title>IBIL Configurator</title>
        <script src="js/script.js" type="text/javascript"></script>
        <link rel="stylesheet"  href="css/estilos.css"/>
		<script src="js/jquery.min.js"></script>

<script>
        function autoRefresh() {
            $('#content').load("data.html");
            $('#otro').load("lsal.php");
        }
        setInterval('autoRefresh()', 2000);
        
        /*
        function autoRefresh() {
            window.location = window.location.href;
        }
        setInterval('autoRefresh()', 2000);

		$(document).ready(function(){
			$('#content').load("data.html");
		
		});
        */

</script>
<header
	<nav class = "navegacion">
		<form id="myForm" action="process_info.php" method="post">  
			<ul class = "menu">
				<li> <a href="#">Inicio</a>
					<ul class="submenu">
						<li><a href="#">Inicio 1 </a></li>
						<li><a href="#">Inicio 2 </a></li>
					</ul>
				</li>
				<li> <a href="#">Servicios</a>
					<ul class="submenu">
						<li onclick="myFunction()" ><a href="">Servicio 1 </a></li>
						<li id="servicio2"><a href="">Servicio 2 </a></li>
					</ul>
				</li>
				<li onclick="myForm.submit();"> <a href="#">Salida</a></li>
				<li> <a href="datacontainer.html">Otros</a></li>						
			</ul>
		</form>
	</nav>
</header>

</head>

<h2>Date & Time</h2>
<div id="content"></div>

<h2>ls -al</h2> 
<div id="otro"></div>

</body>
</html>
