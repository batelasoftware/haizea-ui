<?php
	$ini = parse_ini_file('app.ini');

	$path_db 	        = $ini['full_path_db'];
	$path_ocpp 	      = $ini['full_path_ocpp'];
	$path_dlms 	      = $ini['full_path_dlms'];
	$path_ems 	      = $ini['full_path_ems'];
	$vendor           = $ini['vendor'];
	$model            = $ini['model'];
	$live_data_limit  = $ini['live_data_limit'];

	$full_path_owa 	= $ini['full_path_owa'];


	if (file_exists($path_db)) {
		$bd = new SQLite3($path_db);
		$results = $bd->query('SELECT * FROM DEVICE_MEASUREMENTS order by ts DESC limit '.$live_data_limit.';');
		$i = 0;
		while ($row = $results->fetchArray()) {
			$BX_CODE[$i]  = $row["CODE"];
      $BX_TS[$i] 	  =  gmdate("Y-m-d H:i:s.ms", $row["TS"]);
			$BX_VALUE[$i] = (string)$row["VALUE"];
			$i=$i+1;

		}
	} else {
		exit('Error abriendo base de datos en: '.$path_db );
	}

	if (file_exists($full_path_owa)) {
		$gestor     = fopen($full_path_owa, "rb");
		$contenido  = fread($gestor, filesize($full_path_owa));
		$owaSubstr  = explode("V",$contenido);
	}

?>
