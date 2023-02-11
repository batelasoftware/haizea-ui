<?php

	require_once('dlms_xml.php');
	require_once('modbus_xml.php');
	require_once('ems_xml.php');
	require_once('ems.php');

	$ini = parse_ini_file('/var/www/html/app.ini');
	$path_ocpp 	= $ini['dump_xml'].$ini['file_ocpp'];
	$path_dlms 	= $ini['dump_xml'].$ini['file_dlms'];
	$path_ems 	= $ini['dump_xml'].$ini['file_ems'];
	$path_modbus 	= $ini['dump_xml'].$ini['file_modbus'];

	if(isset($_GET['download'])) {
		//Read the filename
		if ($_GET['download'] == "ems") {
			$files = array($path_ems,$path_dlms,$path_modbus);
			$filename = '/etc/xml/ems_modbus_dlms_files.zip';
			$zip = new ZipArchive;
			$zip->open($filename, ZipArchive::CREATE);
			foreach ($files as $file) {
  			$zip->addFile($file);
			}
			$zip->close();
		}
		else if ($_GET['download'] == "ocpp") {
			$filename = $path_ocpp;
		}

		//Check the file exists or not
		if(file_exists($filename)) {

			//Define header information
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: 0");
			header('Content-Disposition: attachment; filename="'.basename($filename).'"');
			header('Content-Length: ' . filesize($filename));
			header('Pragma: public');

			//Clear system output buffer
			flush();

			//Read the size of the file
			readfile($filename);
			//Terminate from the script
			die();
		}
		else{
			header("location: ../conf2.html?code=100&desc=File not found: $filename");
		}
	}

?>
