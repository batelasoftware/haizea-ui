<?php     
      
      $ini = parse_ini_file('app.ini');      	
      $path_db   	= $ini['full_path_db'];
      $path_ocpp 	= $ini['full_path_ocpp'];
      $path_dlms 	= $ini['full_path_dlms'];
      $path_ems 	= $ini['full_path_ems'];	
      
      $nameSensorQuery = str_replace('_CODE', ' OR CODE' ,$_GET['nameCode']);
 	  $codeNames    = explode("_CODE=",$_GET['nameCode']);
      $codeNames[0] = substr($codeNames[0],5);	
         
      $ts_Start   = $_GET['dateStart'];
      $ts_End     = $_GET['dateEnd'];
      
      if(file_exists($path_db)) {	
        
        $bd = new SQLite3($path_db);
		//exit('SELECT * FROM DEVICE_MEASUREMENTS WHERE CODE="'.$nameSensor.'" and (TS>='.$ts_Start.' and TS<'.$ts_End.');');
        $results = $bd->query('SELECT * FROM DEVICE_MEASUREMENTS WHERE ('.$nameSensorQuery.') and (TS>='.$ts_Start.' and TS<'.$ts_End.');');
        //$results = $bd->query('SELECT * FROM DEVICE_MEASUREMENTS WHERE CODE="'.$nameSensor.'" and (TS>='.$ts_Start.' and TS<'.$ts_End.');');            
        $i = 0;  
  		while ($row = $results->fetchArray()) {         
    		$BX_ts[$i] 	  = gmdate("Y-m-d H:i:s", $row["TS"]);
    		$BX_value[$i] = $row["VALUE"];
			$BX_code[$i]  = $row["CODE"];	
    		$i=$i+1;
    	}	
        if(!isset($BX_value)){
          exit("No Data to Show"); 
        }
      }
?>