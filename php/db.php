<?php

	$ini = parse_ini_file('/var/www/html/app.ini');
	$path_db	= $ini['full_path_db'];
	$log_file = "../haizea-ui.log";
	$cntAlarmas = 0;
	$cntAllDeviceData = 0;

	$grados = 0;
	$velocidad = 0;

	session_start();
	if(array_key_exists('save_devices', $_POST)) {
		saveDevices ($path_db);
		header("location: ../config.html");
	}

	if(array_key_exists('save_local', $_POST)) {
		saveLocalDevice ($path_db);
		header("location: ../config.html");
	}

	if(array_key_exists('reset_alarmas', $_POST)) {
		resetAlarmas ($path_db);
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	}


	function saveLocalDevice($path_db)
	{
		global $log_file;
		error_log("Saving local " ,3,$log_file);

		$BX_local_conf=$_POST['BX_local_conf'];

		if (file_exists($path_db)) {
			if (isset($BX_local_conf)) {
				// This is to store the values
				$bd = new SQLite3($path_db);
				$format = "UPDATE tdevices SET name = \"%s\" , ip = \"%s\" WHERE web_remote=0";
				error_log("Going to insert: ". (string) count($BX_local_conf) ,3,$log_file);
				for ($j = 0; $j < count($BX_local_conf); $j+=2) {
					$query = sprintf($format, $BX_local_conf[$j],$BX_local_conf[$j+1]);
					error_log("Going to insert as local : ". $query ."\n" ,3,$log_file);
					$bd->exec($query);
				}
				$bd->close();

			}
		} else {
			exit('Error abriendo base de datos en: '.$path_db );
		}

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

			$bd = new SQLite3($path_db);
			$results = $bd->query('DELETE FROM tdevices where web_remote != 0;');
			$bd->close();

			if (isset($BX_device_confs)) {
				// This is to store the values
				$bd = new SQLite3($path_db);
				$format = "INSERT INTO tdevices (name,ip,haizea_id,remote,web_remote) VALUES(\"%s\",\"%s\",%d,%d,1)";
				error_log("Going to insert: ". (string) count($BX_device_confs) ,3,$log_file);
				for ($j = 0; $j < count($BX_device_confs); $j+=4) {
					error_log("Remote value is ".$BX_device_confs[$j+3]."-", 3, $log_file);

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

			}
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
			$results = $bd->query('SELECT name FROM tdevices  order by haizea_id asc;');
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

	function readNameIdDevices($path_db){
		$device_items = [];
		if (file_exists($path_db)) {
			$bd = new SQLite3($path_db);
			// $results = $bd->query('SELECT name,haizea_id FROM tdevices order by haizea_id asc;');
			$results =$bd->query('SELECT name,haizea_id FROM vuseraizea  where user ="'. $_SESSION['user'] .'"  order by haizea_id asc;');
			$i = 0;
			while ($row = $results->fetchArray()) {
				$device_items[$row["name"]]  = $row["haizea_id"];
			}
			$bd->close();
		} else {
			exit('Error abriendo base de datos en: '.$path_db );
		}

		return $device_items;
	}


	function readLocalDevice($path_db){

		if (file_exists($path_db)) {
			$bd = new SQLite3($path_db);
			$results = $bd->query('SELECT name FROM tdevices where web_remote=0 and remote= 0;');
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
		$BX_alarmas = [];
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

	function readAllDeviceData($path_db)
	{
		global $cntAllDeviceData;
		global $log_file;

		$BX_alldevicedata = [];
		error_log("Reading all devives", 3, $log_file);
		if (file_exists($path_db)) {

			$bd = new SQLite3($path_db);
			//$results = $bd->query('SELECT name,ip FROM tdevices  order by haizea_id asc;');
			$results = $bd->query('SELECT name,ip FROM vuseraizea  where user ="'. $_SESSION['user'] .'"  order by haizea_id asc;');
			$i = 0;
			while ($row = $results->fetchArray()) {
				$BX_alldevicedata[$cntAllDeviceData][0]  = $row["name"];
				$BX_alldevicedata[$cntAllDeviceData][1]  = $row["ip"];
				$cntAllDeviceData = $cntAllDeviceData+1;
			}
			$bd->close();
		} else {
			exit('Error abriendo base de datos en: '.$path_db );
		}

		return $BX_alldevicedata;
	}



	function resetAlarmas($path_db)
	{
		global $cntAlarmas;
		if (file_exists($path_db)) {
			$bd = new SQLite3($path_db);
			$results = $bd->query('update talarmas set ack=1;');
			$bd->close();
		} else {
			exit('Error abriendo base de datos en: '.$path_db );
		}
	}


	function readLastValues($path_db)
	{
		global $grados;
		global $velocidad;

		if (file_exists($path_db)) {
			$bd = new SQLite3($path_db);
			$results = $bd->query('SELECT windspeed,winddir FROM tvalues ;');
			while ($row = $results->fetchArray()) {
				$velocidad  = $row["windspeed"];
				$grados = $row["winddir"];
			}
			#$grados = rand(0, 360);
			#$velocidad = rand(0, 150);

			$bd->close();
		} else {
			exit('Error abriendo base de datos en: '.$path_db );
		}
	}

	function readMaxValues($path_db,$hours)
	{
		$max_values= [];
		$devices = [];
		$labels = [];
		if (file_exists($path_db)) {
			$devices = readNameIdDevices($path_db);
			$labels = array_keys($devices);
			$bd = new SQLite3($path_db);
			 for ($j = 0; $j < count($devices); $j+=1) {
				  if ($hours == 6){
							$hoursAgo = date('Y-m-d H:i:s', strtotime('-6 hours'));
					}elseif ($hours == 12) {
    						$hoursAgo = date('Y-m-d H:i:s', strtotime('-12 hours'));
				  } else {
				    	$hoursAgo = date('Y-m-d H:i:s', strtotime('-24 hours'));
				  }
					$query = "SELECT MAX(windspeed) AS max_value, date FROM thistoric WHERE haizea_id=".$devices[$labels[$j]]." and date >='" .$hoursAgo."';";
					$results = $bd->query($query);

					while ($row = $results->fetchArray()) {
						 $max_values[$labels[$j]][0] = round($row['max_value'],1);
						 $max_values[$labels[$j]][1] = $row['date'];
					}
			}

			$bd->close();
		} else {
			exit('Error abriendo base de datos en: '.$path_db );
		}
		return $max_values;
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

	$iLocalCount = 0 ;
	$cntLocalItems = 0;


	if (file_exists($path_db)) {
		$bd = new SQLite3($path_db);
		$results = $bd->query('SELECT name,ip,haizea_id,remote FROM tdevices where web_remote=1 ;');
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

		$results = $bd->query('SELECT * FROM tdevices WHERE web_remote=0 ;');
		$i = 0;
		while ($row = $results->fetchArray()) {
			$BX_local_conf[$iLocalCount][0] = $row["name"];
			$BX_local_conf[$iLocalCount][1] = $row["ip"];
			$iLocalCount = $iLocalCount + 1;
			$i=$i+1;
		}
		$cntLocalItems = 2;

		$bd->close();



	} else {
		exit('Error abriendo base de datos en: '.$path_db );
	}

	$BX_local_name =readLocalDevice($path_db);
	$BX_device_items = readDevices($path_db);
	$BX_alarmas = readAlarmas($path_db);
	$BX_alldevicedata = readAllDeviceData($path_db);
	$device_items = count($BX_device_items);

	$device_max_values = readMaxValues($path_db,6);
	$Max06HOficina=$device_max_values["Oficina"][0];
	$Max06HOficinaDate=$device_max_values["Oficina"][1];
	$Max06HGrua=$device_max_values["Portainer08"][0];
	$Max06HGruaDate=$device_max_values["Portainer08"][1];

	$device_max_values = readMaxValues($path_db,12);
	$Max12HOficina=$device_max_values["Oficina"][0];
	$Max12HOficinaDate=$device_max_values["Oficina"][1];
	$Max12HGrua=$device_max_values["Portainer08"][0];
	$Max12HGruaDate=$device_max_values["Portainer08"][1];

	$device_max_values = readMaxValues($path_db,24);
	$Max24HOficina=$device_max_values["Oficina"][0];
	$Max24HOficinaDate=$device_max_values["Oficina"][1];
	$Max24HGrua=$device_max_values["Portainer08"][0];
	$Max24HGruaDate=$device_max_values["Portainer08"][1];
?>
