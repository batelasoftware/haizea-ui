<?php

	require_once('dlms_xml.php');
	require_once('modbus_xml.php');
	require_once('ems_xml.php');
	$ini = parse_ini_file('/var/www/html/app.ini');


	$path_ocpp 	 = $ini['home_xml'].$ini['file_ocpp'];
	$path_dlms 	 = $ini['home_xml'].$ini['file_dlms'];
	$path_ems 	 = $ini['home_xml'].$ini['file_ems'];
	$path_modbus = $ini['home_xml'].$ini['file_modbus'];

	$path_db 	 = $ini['full_path_db'];
	$path_xmlcnf = $ini['full_path_xmlcnf'];
	$path_bk     = $ini['full_path_bk'];

	$EMS_fi = [];
	$DT_ems[]  = [];
	$ems_total_items = 0;
	$ems_rows = 0;
	$ems_extra_items = 0;

	$MBS_fi = [];
	$DT_mbs = [];
	$mbs_total_items = 0;
	$mbs_rows = 0;
	$mbs_extra_items = 0;

	$DLMS_fi = [];
	$DT_dlms = [];
	$dlms_total_items = 0;
	$dlms_rows = 0;
	$dlms_extra_items = 0;

	if(array_key_exists('save', $_POST)) {
		SaveEMSData($ini['home_xml']);
	}

	function SaveEMSData ($save_path){

		global $ini;
		global $path_bk;
		global $path_db;
		global $path_ocpp ;
		global $path_dlms ;
		global $path_ems 	;
		global $path_modbus ;

		/**
		 * Backup files
		 **/
		$date = new DateTime();

		$xml = simplexml_load_file($path_ems);
		$xml->asXml($path_bk."ems.xml"."_bak_".$date->format('Ymd_hms'));
		$xml = simplexml_load_file($path_modbus);
		$xml->asXml($path_bk."modbus.xml"."_bak_".$date->format('Ymd_hms'));
		$xml = simplexml_load_file($path_dlms);
		$xml->asXml($path_bk."dlms.xml"."_bak_".$date->format('Ymd_hms'));


		/*check connectionName*/
		$RX_ast= $_POST['RX_ast'];
		$TX_ast= $_POST['TX_ast'];
		$DX_ast= $_POST['DX_ast'];
		$PX_ast= $_POST['PX_ast'];
		$BX_ast= $_POST['BX_ast'];


		/* Create ems mappings */
		CreateEMSFile ($save_path);
		/* Create modbus mappings */
		CreateModbusFile ($save_path);
		/* Create modbus mappings */
		CreateDLMSMappingNode ($save_path);

	}

	function emsTabValuesNumeric(){

		$DT_ems = $_POST['DT_ems'];
		$exitCode = 'Ok';

		for($i = 1; $i<count($DT_ems); $i= $i+7){
			if(is_numeric($DT_ems[$i])==1 && is_numeric($DT_ems[$i+4])==1 && is_numeric($DT_ems[$i+5])==1 ){
				$exitCode = "Ok";
			}
			else{
				return 'Error';
			}
		}
		return $exitCode;
	}

	function connRTUValuesNumeric($a_name){

		$a_astm = $_POST[$a_name];
		$exitCode = 'Ok';

		for($i = 2; $i<count($a_astm); $i= $i+7){
			if(is_numeric($a_astm[$i])==1 && is_numeric($a_astm[$i+1])==1 && is_numeric($a_astm[$i+3])==1 ){
				$exitCode = "Ok";
			}
			else{
				return 'Error';
			}
		}
		return $exitCode;
	}

	function connTCPValuesNumeric($a_name){

		$a_astm = $_POST[$a_name];
		$exitCode = 'Ok';

		for($i = 3; $i<count($a_astm); $i= $i+4){
			if(is_numeric($a_astm[$i])==1){
				$exitCode = "Ok";
			}
			else{
				return 'Error';
			}
		}
		return $exitCode;
	}

	function assetsConfValuesNumeric(){

		$BX_ast  = $_POST['BX_ast'];
		$exitCode = "Ok";

		for($i = 1; $i<count($BX_ast); $i= $i+6){
			if(is_numeric($BX_ast[$i])==1 && is_numeric($BX_ast[$i+1])==1 && is_numeric($BX_ast[$i+2])==1 && is_numeric($BX_ast[$i+3])==1){
				$exitCode = "Ok";
			}
			else{
				return 'Error';
			}
		}
		return $exitCode;
	}


	if ((file_exists($path_ems))) {
		global $path_ems;

		$xml = simplexml_load_file($path_xmlcnf);

		$cnt = 0;
		foreach($xml->ems->evcharger->attributes() as $a => $b) {
			$BX_col[$cnt] = $b;
			$cnt = $cnt + 1;
		}

		$cnf_row_items = 5 ;
		$cnt = 0;
		foreach($xml->ems->{'form-items'}->attributes() as $a => $b) {
			$EMS_fi[$cnt] = $b;
			$cnt += 1;
		}
		$ems_total_items = count($EMS_fi);
		$ems_rows = floor(count($EMS_fi)/$cnf_row_items);
		$ems_extra_items = count($EMS_fi)%$cnf_row_items;

		$xml        = simplexml_load_file($path_ems);
		$DT_ems[0]  = $xml->config->debug;
		$DT_ems[1]  = (string)$xml->config->interval;
		$DT_ems[2]  = $xml->mqtt->url;
		$DT_ems[3]  = $xml->sqlite->file;

		$DT_ems[4] = $xml->network->meter."\n";
		$DT_ems[5] = (string)$xml->network->maxCurrent;
		$DT_ems[6] = (string)$xml->network->bus->line->id;

		for ($i = 0; $i < $xml->assets->evCharger->count(); $i++) {
			$cnt = 0;
			foreach ($xml->assets->evCharger[$i]->children() as $key =>$chld) {
				$BX_ast[$i][$cnt] = $chld;
				$cnt = $cnt + 1;
			}
		}
	}

	if ((file_exists($path_modbus))) {
		global $path_modbus;

		$xml = simplexml_load_file($path_xmlcnf);
		$cnt = 0;

		$cnt = 0;
		foreach($xml->conn->connRTU->attributes() as $a => $b) {
			$RX_col[$cnt] = $b;
			$cnt = $cnt + 1;
		}

		$cnt = 0;
		foreach($xml->conn->connTCP->attributes() as $a => $b) {
			$TX_col[$cnt] = $b;
			$cnt = $cnt + 1;
		}

		$cnt = 0;
		foreach($xml->modbus->{'form-items'}->attributes() as $a => $b) {
			$MBS_fi[$cnt] = $b;
			$cnt += 1;
		}

		$mbs_total_items = count($MBS_fi);
		$mbs_rows = floor(count($MBS_fi)/$cnf_row_items);
		$mbs_extra_items = count($MBS_fi)%$cnf_row_items;

		$xml       = simplexml_load_file($path_modbus);
		$DT_mbs[0] = $xml->config->debug;
		$DT_mbs[1] = $xml->mqtt->url;

		$iTCP = 0;
		$iRTU = 0;

		for ($i = 0; $i < $xml->modbus->connection->count(); $i++) {
			if (isset($xml->modbus->connection[$i]->rtu)) {
				$RX_ast[$iRTU][0] = $xml->modbus->connection[$i]["name"];
				$RX_ast[$iRTU][1] = $xml->modbus->connection[$i]["debug"];
				$RX_ast[$iRTU][2] = $xml->modbus->connection[$i]["timeout"];
				$RX_ast[$iRTU][3] = $xml->modbus->connection[$i]["delay"];

				$cntRTU = 4;
				foreach ($xml->modbus->connection[$i]->rtu->children() as $key =>$chld) {
					$RX_ast[$iRTU][$cntRTU] = $chld;
					$cntRTU = $cntRTU + 1;
				}
				$iRTU = $iRTU + 1;
			}elseif (isset($xml->modbus->connection[$i]->tcp)) {
				$TX_ast[$iTCP][0] =$xml->modbus->connection[$i]["name"];
				$TX_ast[$iTCP][1] =$xml->modbus->connection[$i]["debug"];

				$cntTCP = 2;
				foreach ($xml->modbus->connection[$i]->tcp->children() as $key =>$chld) {
					$TX_ast[$iTCP][$cntTCP] = $chld;
					$cntTCP = $cntTCP + 1;
				}
				$iTCP = $iTCP + 1;
			}
		}
	}

	if ((file_exists($path_dlms))) {
		global $path_dlms;

		$xml = simplexml_load_file($path_xmlcnf);
		$cnt = 0;

		foreach($xml->dlms->{'form-items'}->attributes() as $a => $b) {
			$DLMS_fi[$cnt] = $b;
			$cnt += 1;
		}

   		$cnt = 0;
		foreach($xml->conn->connRTU->attributes() as $a => $b) {
			$DX_col[$cnt] = $b;
			$cnt = $cnt + 1;
		}

 		$cnt = 0;
		foreach($xml->conn->connTCP->attributes() as $a => $b) {
			$PX_col[$cnt] = $b;
			$cnt = $cnt + 1;
		}

		$dlms_total_items = count($DLMS_fi);
		$dlms_rows 		  = floor(count($DLMS_fi)/$cnf_row_items);
		$dlms_extra_items = count($DLMS_fi)%$cnf_row_items;

		$xml   = simplexml_load_file($path_dlms);

		$DT_dlms [0] 	= $xml->config->debug;
		$DT_dlms [1]    = $xml->mqtt->url;

		$iTCP = 0;
		$iRTU = 0;

		for ($i = 0; $i < $xml->dlms->connection->count(); $i++) {
			if (isset($xml->dlms->connection[$i]->rtu)) {
				$DX_ast[$iRTU][0] = $xml->dlms->connection[$i]["name"];
				$DX_ast[$iRTU][1] = $xml->dlms->connection[$i]["debug"];
				$DX_ast[$iRTU][2] = $xml->dlms->connection[$i]["timeout"];
				$DX_ast[$iRTU][3] = $xml->dlms->connection[$i]["delay"];

				$cntRTU = 4;
				foreach ($xml->dlms->connection[$i]->rtu->children() as $key =>$chld) {
					$DX_ast[$iRTU][$cntRTU] = $chld;
					$cntRTU = $cntRTU + 1;
				}
				$iRTU = $iRTU + 1;
			}
			else if (isset($xml->dlms->connection[$i]->tcp)) {
				$PX_ast[$iTCP][0] =$xml->dlms->connection[$i]["name"];
				$PX_ast[$iTCP][1] =$xml->dlms->connection[$i]["debug"];

				$cntTCP = 2;
				foreach ($xml->dlms->connection[$i]->tcp->children() as $key =>$chld) {
					$PX_ast[$iTCP][$cntTCP] = $chld;
					$cntTCP = $cntTCP + 1;
				}
				$iTCP = $iTCP + 1;
			}
		}
	}
?>
