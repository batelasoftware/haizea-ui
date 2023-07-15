<?php

	$ini = parse_ini_file('/var/www/html/app.ini');
	$path_db	= $ini['full_path_db'];
	$log_file = "../haizea-ui.log";
	$cntAlarmas = 0;

	if(array_key_exists('save_devices', $_POST)) {
		saveDevices ($path_db);
		header("location: ../config.html");
	}

	function saveDevices($path_db){
		global $log_file;

		$BX_device_confs=$_POST['BX_device_confs'];
		if (file_exists($path_db)) {
			// This is to get the new haizea_id
			$bd = new SQLite3($path_db);
			$results = $bd->query('SELECT max(haizea_id)  AS haizea_id FROM tdevices;');
			while ($row = $results->fetchArray()) {
				$max_hid = $row["haizea_id"];
				$max_hid = $max_hid + 1 ;
			}
			$bd->close();
			
			// This is to store the values
			$bd = new SQLite3($path_db);
			$format = "INSERT INTO tdevices (name,ip,haizea_id,remote,web_remote) VALUES(\"%s\",\"%s\",%d,%d,1)";
			for ($j = 0; $j < count($BX_device_confs); $j+=4) {
				if ($BX_device_confs[$j+2] != 0 ){
					$query = sprintf($format, $BX_device_confs[$j],$BX_device_confs[$j+1],$BX_device_confs[$j+2], $BX_device_confs[$j+3]);
				}
				else {
					$query = sprintf($format, $BX_device_confs[$j],$BX_device_confs[$j+1],$max_hid, $BX_device_confs[$j+3]);
					$max_hid = $max_hid + 1 ;
				}
				$bd->exec($query);
				error_log("-".$query."-", 3, $log_file);
			}
			$bd->close();
		} else {
			exit('Error abriendo base de datos en: '.$path_db );
		}

	}

	/*
	Seccion de funciones
	*/
	$BX_device_items = [];
	function readDevices($path_db){

		if (file_exists($path_db)) {
			$bd = new SQLite3($path_db);
			$results = $bd->query('SELECT name FROM tdevices;');
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


	function readAlarmas($path_db)
	{
		global $cntAlarmas;
		if (file_exists($path_db)) {
			$bd = new SQLite3($path_db);
			$results = $bd->query('SELECT name,windspeed,date FROM valarmas where ack=0;');
			$i = 0;
			while ($row = $results->fetchArray()) {
				$BX_alarmas[$cntAlarmas][0]  = $row["name"];
				$BX_alarmas[$cntAlarmas][1]  = $row["windspeed"];
				$BX_alarmas[$cntAlarmas][2]  = $row["date"];
				$cntAlarmas = $cntAlarmas+1;
			}
			$bd->close();
		} else {
			exit('Error abriendo base de datos en: '.$path_db );
		}

		return $BX_alarmas;
	}

	/*
		Seccion main del script!!
	*/
	//$BX_device_confs = [];
	$iDevCount = 0 ;
	$cntDevItems = 0;

	//$BX_serial_confs = [];
	$iSerCount = 0 ;
	$cntSerItems = 0;

	if (file_exists($path_db)) {
		$bd = new SQLite3($path_db);
		$results = $bd->query('SELECT name,ip,haizea_id,remote FROM tdevices ;');
		$i = 0;
		while ($row = $results->fetchArray()) {
			$BX_name[$i]  = $row["name"];
			$BX_ip[$i]  = $row["ip"];
			$BX_device_confs[$iDevCount][0] = $row["name"];
			$BX_device_confs[$iDevCount][1] = $row["ip"];
			$BX_device_confs[$iDevCount][2] = $row["haizea_id"];
			$BX_device_confs[$iDevCount][3] = $row["remote"];
			$i=$i+1;
			$iDevCount = $iDevCount + 1;
		}
		$cntDevItems = 4;

		$results = $bd->query('SELECT * FROM tserial ;');
		$i = 0;
		while ($row = $results->fetchArray()) {
			$BX_serie[$i]  = $row["name"];
			$BX_bauds[$i]  = $row["bauds"];
			$BX_datab[$i]  = $row["datab"];
			$BX_stopb[$i]  = $row["stopb"];
			$BX_parit[$i]  = $row["parit"];
			$BX_serial_conf[$iSerCount][0] = $row["name"];
			$BX_serial_conf[$iSerCount][1] = $row["bauds"];
			$BX_serial_conf[$iSerCount][2] = $row["datab"];
			$BX_serial_conf[$iSerCount][3] = $row["stopb"];
			$BX_serial_conf[$iSerCount][4] = $row["parit"];
			$iSerCount = $iSerCount + 1;
			$i=$i+1;
		}
		$cntSerItems = 5;
		$bd->close();

	} else {
		exit('Error abriendo base de datos en: '.$path_db );
	}

	$BX_local_name =readLocalDevice($path_db);
	$BX_device_items = readDevices($path_db);
	$BX_alarmas = readAlarmas($path_db);
	$device_items = count($BX_device_items);
?>
