<?php
	$ini 		     = parse_ini_file('/var/www/html/app.ini');
	$path_ocpp 	 = $ini['home_xml'].$ini['file_ocpp'];
	$path_dlms 	 = $ini['home_xml'].$ini['file_dlms'];
	$path_ems 	 = $ini['home_xml'].$ini['file_ems'];
	$path_modbus = $ini['home_xml'].$ini['file_modbus'];
	$path_db 	 = $ini['full_path_db'];
	$path_xmlcnf =  $ini['full_path_xmlcnf'];


	/*
	* Creates the device node reading attributes from the Configuration
	* file, default values are read from that file
	*/
	function CreateDeviceNode ($dom_xml,...$options)
	{
		global 	$path_xmlcnf;
    
		$cnf_xml = simplexml_load_file($path_xmlcnf);
		$opt_cnt = count ($options);

		$att_idx = 0;
		$chl = $dom_xml->createElement("device");
		foreach($cnf_xml->dlms->{"device-attr"}->attributes() as $att => $att_val) {
			if ($att_idx < $opt_cnt) {
				$chl->setAttribute ($att,(string)$options[$att_idx]);
			}
			else {
				$chl->setAttribute ($att,(string)$att_val);
			}
			$att_idx +=1;
		}
		return $chl;
	}

	/*
	* DLMS file generation
	*/
	function CreateDLMSMappingNode ($save_home)
	{
		global $ini;
		global $path_xmlcnf;
		global $path_ems;
		global $path_dlms ;

		$dom_xml = new DOMDocument;
		$dom_xml->load($path_dlms);
		$xpath = new DOMXpath($dom_xml);
		
		$dom_tags  = $dom_xml->getElementsByTagName('mappings')[0];

		$DT_dlms = $_POST['DT_dlms'];
		$PX_ast  = $_POST['PX_ast'];
		$DX_ast  = $_POST['DX_ast'];
		$BX_ast  = $_POST['BX_ast'];

		/** Config **/
		$elements = $xpath->query("//config/debug");
		$elements->item(0)->nodeValue = $_POST['DT_dlms'][0];

		/** Mqtt **/
		$elements = $xpath->query("//mqtt/url");
		$elements->item(0)->nodeValue = $_POST['DT_dlms'][1];
   
 		$xml = simplexml_load_file($path_xmlcnf);

		$assets_modbus = count($_POST['chk_assets_modbus']);
		$assets_dlms   = count($_POST['chk_assets_dlms']);

		$asset_cols = count($BX_ast) /($assets_modbus+$assets_dlms);
		$asset_cont = ($assets_modbus+$assets_dlms);
		
		/* 1.- Remove all items */
		$tags_dlms = $dom_xml->getElementsByTagName('dlms')[0];
		while ($tags_dlms->hasChildNodes()) {
			$tags_dlms->removeChild($tags_dlms->firstChild);
		}
		
		/* RTU Conn section */
		for ($i=0 ; $i < count($_POST['chk_rtu_dlms']) ; $i++ ){
			$cntRTU = 7*$i;
			$rtu_conn = $dom_xml->createElement('connection');

			$domAttName = $dom_xml->createAttribute('name');
			$domAttName->value = $DX_ast[$cntRTU];
			$conn_name = $DX_ast[$cntRTU];
			$cntRTU = $cntRTU + 1;
			$rtu_conn->appendChild($domAttName);

			$domAttDebug = $dom_xml->createAttribute('debug');
			$domAttDebug->value = $DX_ast[$cntRTU];
			$cntRTU = $cntRTU + 1;
			$rtu_conn->appendChild($domAttDebug);

			$domAttTimeout = $dom_xml->createAttribute('timeout');
			$domAttTimeout->value = $DX_ast[$cntRTU];
			$cntRTU = $cntRTU + 1;
			$rtu_conn->appendChild($domAttTimeout);

			$domAttDelay = $dom_xml->createAttribute('delay');
			$domAttDelay->value = $DX_ast[$cntRTU];
			$cntRTU = $cntRTU + 1;
			$rtu_conn->appendChild($domAttDelay);

			$elementRTU = $dom_xml->createElement('rtu');
			
			foreach($xml->dlms->connRTUItems->attributes() as $attr_val) {
				$val =	checkValueRanges ($attr_val,$DX_ast[$cntRTU]);
				$chl = $dom_xml->createElement($attr_val);
				$chl->nodeValue = $val;
				$elementRTU->appendChild($chl);
				$cntRTU = $cntRTU + 1;
			}

			$elementDevRTU = $dom_xml->createElement('devices');
			
			for ($j=0 ; $j < $asset_cont ; $j++ ){
				
				if ($conn_name == $BX_ast[$asset_cols*$j+$asset_cols-1]) {
					
					$chl = $dom_xml->createElement("device");

					$domAttName = $dom_xml->createAttribute('id');
					$domAttName->value = $BX_ast[$asset_cols*$j];
					$chl->appendChild($domAttName);

					$domAttName = $dom_xml->createAttribute('slave');
					$domAttName->value = $j;
					$chl->appendChild($domAttName);

					$elementDevRTU->appendChild($chl);
					
				}
			}
			
			$rtu_conn->appendChild($elementRTU);
			$rtu_conn->appendChild($elementDevRTU);
			$tags_dlms->appendChild($rtu_conn);
		}
		
		
		/** TCP Conn section **/
		for ($i=0 ; $i < count($_POST['chk_tcp_dlms']) ; $i++ ){
			      
			$cntTCP = 4*$i;
			$tcp_conn = $dom_xml->createElement('connection');
      
			$domAttName = $dom_xml->createAttribute('name');
			$domAttName->value = $PX_ast[$cntTCP];
			$conn_name = $PX_ast[$cntTCP];
			$cntTCP = $cntTCP + 1;
			$tcp_conn->appendChild($domAttName);

			$domAttDebug = $dom_xml->createAttribute('debug');
			$domAttDebug->value = $PX_ast[$cntTCP];
			$cntTCP = $cntTCP + 1;
			$tcp_conn->appendChild($domAttDebug);

			$elementTCP = $dom_xml->createElement('tcp');
						
			foreach($xml->dlms->connTCPItems->attributes() as $attr_val) {
				$val =	checkValueRanges ($attr_val,$PX_ast[$cntTCP]);
				$chl = $dom_xml->createElement($attr_val);
				$chl->nodeValue = $val;
				$elementTCP->appendChild($chl);
				$cntTCP = $cntTCP + 1;	
			}
			
			/* 1.- Remove all items 
			$dev_tags = $dom_xml->getElementsByTagName('devices')[0];
			while ($dev_tags->hasChildNodes()) {
				$dev_tags->removeChild($dev_tags->firstChild);
			}*/
     
			$elementDevTCP = $dom_xml->createElement('devices');
			
			for ($j=0 ; $j < $asset_cont ; $j++ ){
				if ($conn_name == $BX_ast[$asset_cols*$j+$asset_cols-1]){
					
					$chl = $dom_xml->createElement("device");

					$domAttId = $dom_xml->createAttribute('id');
					//$domAttId->value = $BX_ast[$asset_cols*$j];
					$domAttId->value = $j+1;
					$chl->appendChild($domAttId);
					
					$domAttName = $dom_xml->createAttribute('name');
					$domAttName->value = "Device #".($j+1);
					$chl->appendChild($domAttName);
					
					$domAttSerial = $dom_xml->createAttribute('serial-number');
					$name_items = explode("-",$BX_ast[$asset_cols*$j]);
					$domAttSerial->value    =  $name_items[count($name_items)-1];
					$chl->appendChild($domAttSerial);
					
					$domAttLogical = $dom_xml->createAttribute('logical-address');
					$domAttLogical->value = "1";
					$chl->appendChild($domAttLogical);
					
					$domAttClientId = $dom_xml->createAttribute('client-id');
					$domAttClientId->value = "1";
					$chl->appendChild($domAttClientId);
					
					$domAttAarq = $dom_xml->createAttribute('aarq');
					$domAttAarq->value = "false";
					$chl->appendChild($domAttAarq);
                    					
					$elementDevTCP->appendChild($chl);
				}
			} 
			$tcp_conn->appendChild($elementTCP);
			$tcp_conn->appendChild($elementDevTCP);
			$tags_dlms->appendChild($tcp_conn);           
		}
		
		$tags = $dom_xml->getElementsByTagName('mappings')[0];
		while ($tags->hasChildNodes()) {
			$tags->removeChild($tags->firstChild);
		}
   
		/* 1.- Remove all items 
		$dev_tags = $dom_xml->getElementsByTagName('devices')[0];
		while ($dev_tags->hasChildNodes()) {
			$dev_tags->removeChild($dev_tags->firstChild);
		}
		*/
		$tags = $dom_xml->getElementsByTagName('mappings')[0];
		while ($tags->hasChildNodes()) {
			$tags->removeChild($tags->firstChild);
		}

		/** EMS file read **/
		$ems_xml = simplexml_load_file($path_ems);
		$device_id_cnt = 0;
		foreach($ems_xml->assets->evCharger  as $a => $b) {
			$device_id_cnt += 1;
			if(in_array($b->ConnName,$DX_ast) || in_array($b->ConnName,$PX_ast)){
			
				$name 		= $b->Name;
				$phase 	    = (string)$b->Phase;
				$name_items = explode("-",$Name);
				$serial     = $name_items[count($name_items)-1];

				$tmp_xml   = new DOMDocument;
				$tmp_xml->load($path_xmlcnf);
				$tmp_xpath = new DOMXpath($tmp_xml);

				if ($phase == ""){
					$phase = 3;
				}

				$chl = CreateDeviceNode($dom_xml,$device_id_cnt,$name,$serial);
				//$dev_tags->appendChild($chl);

				$tmp_tags = $tmp_xpath->query("//dlms/mappings/ph".$phase."/dlms-mqtt[@device]/@device");
				foreach ($tmp_tags as $pp ) {
					$pp->nodeValue = str_replace( '$D',(string)$device_id_cnt,$pp->nodeValue) ;
					//$pp->nodeValue = str_replace( '$D',(string)$name,$pp->nodeValue) ;
				}

				$tmp_tags = $tmp_xpath->query("//dlms/mappings/ph".$phase."/dlms-mqtt[@topic]/@topic");
				foreach ($tmp_tags as $pp ) {

					$pp->nodeValue = str_replace( '$D',$name,$pp->nodeValue) ;
				}
				

				$map_tags= $tmp_xpath->query("//dlms/mappings/ph".$phase)[0];
				foreach($map_tags->childNodes as $chl => $child) {
					$newdoc = new DOMDocument;
					$newdoc = $dom_xml->importNode($child, true);
					$dom_tags->appendChild($newdoc);
				}
			}
		}
		$dom_xml->save($save_home.$ini['file_dlms']);
	}
?>
