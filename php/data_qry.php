<?php
  	$ini          = parse_ini_file('app.ini');
  	$path_db      = $ini['full_path_db'];

    $BX_device_items = [];
    $haizea_id = [];
    $BX_values = Array();
    $log_file = "./haizea-ui.log";

    $start   = $_GET['datestart'];
    $end     = $_GET['dateend'];

    $message = "He leido los valores de GET";
    error_log($message.":".$start.":".$end.";\n", 3, $log_file);

    function readDeviceValues($haizea_id,$start,$end){
      global $path_db;
      global $BX_values;
      global $message;
      global $log_file;

      $bd = new SQLite3($path_db);
      echo $path_db;
      foreach ($haizea_id as $a => $b) {
        $values = Array();
        if ($start != "" and $end != "" ){
            // $message = 'SELECT windspeed, winddir,date FROM thistoric where haizea_id='.$b.' and date >=\"' .$start. '\" and date <=\"' .$end. '\";' ;
            // error_log($message, 3, $log_file);
            $results = $bd->query('SELECT windspeed, winddir,date FROM thistoric where haizea_id='.$b.' and date >="' .$start. '" and date <="' .$end. '";');
        }
        else {
          $results = $bd->query('SELECT windspeed, winddir,date FROM thistoric where haizea_id='.$b.' order by date desc limit 100 ;');
        }
        while ($row = $results->fetchArray()) {
            array_push($values, $row["windspeed"]);
        }
        array_push($BX_values, $values);
    	}
      $bd->close();
      var_dump($BX_values);

  	}



  	if (file_exists($path_db)) {
  		$bd = new SQLite3($path_db);
      $results = $bd->query('SELECT name,haizea_id FROM tdevices;');
      $i = 0;
      while ($row = $results->fetchArray()) {
          $chart_vrb[$i] = $row["name"];
          $haizea_id[$i] = $row["haizea_id"];
          $i=$i+1;
      }
      $bd->close();
  	} else {
  		exit('Error abriendo base de datos en: '.$path_db );
  	}

    if(isset($_GET['datestart']) and isset($_GET['dateend'])) {
      readDeviceValues($haizea_id,$start,$end);
    }
    else{
        readDeviceValues($haizea_id,"","");
    }

    error_log("hay datos:".$b."\n", 3, $log_file);
    error_log(print_r($BX_values,true), 3, $log_file);

?>
