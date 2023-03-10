<?php

	$ini = parse_ini_file('/var/www/html/app.ini');
	$path_db	= $ini['full_path_db'];

	/*
		Seccion de funciones
	*/
	$BX_device_items = [];
	function readDevices($path_db){

		if (file_exists($path_db)) {
			$bd = new SQLite3($path_db);
			$results = $bd->query('SELECT name FROM tdevices where web_remote=1;');
			$i = 0;
			while ($row = $results->fetchArray()) {
				$BX_device_items[$i]  = $row["name"];
				$i=$i+1;
			}
			$bd->close();
		} else {
			exit('Error abriendo base de datos en: '.$path_db );
		}

		return $BX_device_items;
	}

	function readLocalDevice($path_db){

		if (file_exists($path_db)) {
			$bd = new SQLite3($path_db);
			$results = $bd->query('SELECT name FROM tdevices where remote=0;');
			$i = 0;
			while ($row = $results->fetchArray()) {
				$BX_local_name[$i]  = $row["name"];
				$i=$i+1;
			}
			$bd->close();
		} else {
			exit('Error abriendo base de datos en: '.$path_db );
		}

		return $BX_local_name;
	}
	/*
		Seccion main del script!!
	*/
	if (file_exists($path_db)) {
		$bd = new SQLite3($path_db);
		$results = $bd->query('SELECT name,ip FROM tdevices ;');
		$i = 0;
		while ($row = $results->fetchArray()) {
			$BX_name[$i]  = $row["name"];
			$BX_ip[$i]  = $row["ip"];
      $i=$i+1;

		}

		$results = $bd->query('SELECT * FROM tserial ;');
		$i = 0;
		while ($row = $results->fetchArray()) {
			$BX_serie[$i]  = $row["name"];
			$BX_bauds[$i]  = $row["bauds"];
			$BX_datab[$i]  = $row["datab"];
			$BX_stopb[$i]  = $row["stopb"];
			$BX_parit[$i]  = $row["parit"];
      $i=$i+1;

		}
		$bd->close();

	} else {
		exit('Error abriendo base de datos en: '.$path_db );
	}

	$BX_local_name =readLocalDevice($path_db);
	$BX_device_items = readDevices($path_db);
	$device_items = count($BX_device_items);
?>
