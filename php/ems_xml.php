<?php

	$ini = parse_ini_file('/var/www/html/app.ini');

	$path_ocpp 	= $ini['home_xml'].$ini['file_ocpp'];
	$path_dlms 	= $ini['home_xml'].$ini['file_dlms'];
	$path_ems 	= $ini['home_xml'].$ini['file_ems'];
	$path_modbus = $ini['home_xml'].$ini['file_modbus'];

	$path_db 		= $ini['full_path_db'];

	function checkValueRanges ($name,$value)
	{
		$val = $value ;
		switch ($name){
			case "phase":
				if ((int)$value >= 3 || $value =="*") {
					$val = 3 ;
				}
			break;
		}
		return $val;
	}


	function CreateEMSFile ($save_home)
	{
		global $ini;
		global $path_xmlcnf;
		$path_ems = $ini['home_xml'].$ini['file_ems'];

		/**
		 * Create new files
		**/

		$dom = new DOMDocument();
		$dom->load($path_ems);
		$xpath = new DOMXpath($dom);

		/** EMS data  **/
		$DT_ems= $_POST['DT_ems'];

		/** Config **/
		$elements = $xpath->query("//config/debug");
		$elements->item(0)->nodeValue = $_POST['DT_ems'][0];

		$elements = $xpath->query("//config/interval");
		$elements->item(0)->nodeValue = $_POST['DT_ems'][1];

		/** Mqtt **/
		$elements = $xpath->query("//mqtt/url");
		$elements->item(0)->nodeValue = $_POST['DT_ems'][2];

		$elements = $xpath->query("//sqlite/file");
		$elements->item(0)->nodeValue = $_POST['DT_ems'][3];

		/** Network **/
		$elements = $xpath->query("//network/meter");
		$elements->item(0)->nodeValue = $_POST['DT_ems'][4];

		$elements = $xpath->query("//network/maxCurrent");
		$elements->item(0)->nodeValue = $_POST['DT_ems'][5];

		$elements = $xpath->query("//network/bus/line/id");
		$elements->item(0)->nodeValue = $_POST['DT_ems'][6];

		/** Assets **/
		/* 1.- Remove all items */
		$ev_assets = $dom->getElementsByTagName('assets')[0];
		while ($ev_assets->hasChildNodes()) {
			$ev_assets->removeChild($ev_assets->firstChild);
		}

		/* 2.- Read template, create attribute childer and load datta */
		$xml = simplexml_load_file("/var/www/html/xml_template/owa_cfg.xml");

		$BX_ast= $_POST['BX_ast'];
		$cnt = 0;

		$rows = count($BX_ast)/$xml->ems->evcharger->attributes()->count();

		for ($i=0 ; $i < $rows ; $i++ ){
			$element = $dom->createElement('evCharger');

			foreach($xml->ems->evcharger->attributes() as $attr_val) {
				$val =	checkValueRanges ($attr_val,$BX_ast[$cnt]);
				//echo "--".$attr_val."->".$val;
				$chl = $dom->createElement($attr_val);
				$chl->nodeValue = $val;
				$element->appendChild($chl);
				$cnt = $cnt + 1;
			}
			$ev_assets->appendChild($element);
		}
		/* 2.- Save the file */
		$dom->save($save_home.$ini['file_ems']);
	}
?>
