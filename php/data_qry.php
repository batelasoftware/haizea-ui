<?php
	session_start();
  	$ini          = parse_ini_file('app.ini');
  	//$ini = parse_ini_file('/var/www/html/app.ini');
  	$path_db      = $ini['full_path_db'];

    $BX_update = "FALSE";
    $BX_device_items = [];
    $haizea_id = [];
    $haizea_names = [];
	
	$avgValuesTotals=[];
	$maxValuesTotals=[];

    $BX_values = Array();
    $BX_dates = Array();
    $log_file = "./haizea-ui.log";

    $start   = $_GET['datestart'];
    $end     = $_GET['dateend'];

    $message = "He leido los valores de GET";
    error_log($message.":".$start.":".$end.";\n", 3, $log_file);


	function readMaxAvgValuesSpider($bd,$haizea_id,$hours)
	{
		global $path_db;
		global $avgValuesTotals;
		global $maxValuesTotals;
		
		$max_values = Array();
		$avg_values = Array();
		//$startGrads = [0,   22.5, 67.5, 112.5, 157.5, 202.5, 247.5, 292.5];
		//$endGrads   = [22.5,67.5, 112.5, 157.5, 202.5, 247.5, 292.5, 337,5];
		$startGrads = [0,   22, 67, 112, 157, 202, 247, 292,337];
		$endGrads   = [22,67, 112, 157, 202, 247, 292, 337,360];
		
		if ($hours == 6){
			$hoursAgo = date('Y-m-d H:i:s', strtotime('-6 hours'));
		}elseif ($hours == 12) {
			$hoursAgo = date('Y-m-d H:i:s', strtotime('-12 hours'));
		} else {
			$hoursAgo = date('Y-m-d H:i:s', strtotime('-24 hours'));
		}

		$sql = 'SELECT ROUND(MAX(windspeed),1) AS max_speed , ROUND(AVG(windspeed),1) AS avg_speed 
		FROM thistoric 
		WHERE haizea_id = :haizea_id 
		AND winddir BETWEEN :start_grad AND :end_grad 
		AND date >= :hours_ago';

		$stmt = $bd->prepare($sql);
		$stmt->bindParam(':haizea_id', $haizea_id, SQLITE3_INTEGER);
		$stmt->bindParam(':hours_ago', $hoursAgo);
		$stmt->bindParam(':start_grad', $start, SQLITE3_INTEGER);
		$stmt->bindParam(':end_grad', $end, SQLITE3_INTEGER);
		
		for ($i = 0; $i < count($startGrads); $i++) {
			$start = $startGrads[$i];
			$end = $endGrads[$i];
			$result = $stmt->execute();
			$row = $result->fetchArray(SQLITE3_ASSOC);			
			if ($row) {
				array_push($max_values, $row["max_speed"]);
				array_push($avg_values, $row["avg_speed"]);
			}
		}	
		
		if ($max_values[count($max_values)-1] > $max_values[0] ){
			$max_values[0] = $max_values[count($max_values)-1] ;
		}
		if ($avg_values[count($avg_values)-1] > $avg_values[0] ){
			$avg_values[0] = $avg_values[count($avg_values)-1] ;
		}
		array_pop($max_values);
		array_pop($avg_values);
		
		array_push ($maxValuesTotals[$haizea_id],$max_values);
		array_push ($avgValuesTotals[$haizea_id],$avg_values);
		
	}
	
	function readAllMaxAvgValuesSpider()
	{
		global $path_db;
		global $haizea_id;	
		global $avgValuesTotals;
		global $maxValuesTotals;
		
		$bd = new SQLite3($path_db);
		$bd->exec('PRAGMA read_uncommitted = true;');
		for ($i = 0; $i < count($haizea_id); $i++) {
			$maxValuesTotals[$haizea_id[$i]]=[];
			$avgValuesTotals[$haizea_id[$i]]=[];
	
			readMaxAvgValuesSpider ($bd,$haizea_id[$i],6);
			readMaxAvgValuesSpider ($bd,$haizea_id[$i],12);
			readMaxAvgValuesSpider ($bd,$haizea_id[$i],24);
		}
		$bd->close();	
	}
    
    function readDeviceValues($haizea_id,$start,$end)
    {
      global $path_db;
      global $BX_values;
      global $BX_dates;
      global $message;
      global $log_file;

      $bd = new SQLite3($path_db);
      foreach ($haizea_id as $a => $b) {
        $values = Array();
        $dates = Array();
        if ($start != "" and $end != "" ){
            // $message = 'SELECT ROUND(windspeed,1), winddir,date FROM thistoric where haizea_id='.$b.' and date >=\"' .$start. '\" and date <=\"' .$end. '\";' ;
            // error_log($message, 3, $log_file);
            $results = $bd->query('SELECT ROUND(windspeed,1) AS windspeed, winddir,date FROM thistoric where haizea_id='.$b.' and date >="' .$start. '" and date <="' .$end. '" order by date desc;');
        }
        else {
          $results = $bd->query('SELECT ROUND(windspeed,1) AS windspeed, winddir,date FROM thistoric where haizea_id='.$b.' order by date desc limit 100 ;');
        }
        while ($row = $results->fetchArray()) {
            array_push($values, $row["windspeed"]);
            array_push($dates, $row["date"]);
        }
        array_push($BX_values, array_reverse($values));
        array_push($BX_dates, array_reverse($dates));
    	}
      $bd->close();
  	}
  	
  	if (file_exists($path_db)) {
	  $bd = new SQLite3($path_db);
	  $results = $bd->query('SELECT name,haizea_id FROM vuseraizea  where user ="'. $_SESSION['user'] .'"  order by haizea_id asc;');
	  // $results = $bd->query('SELECT name,haizea_id FROM tdevices;');
	  $message = 'SELECT name,haizea_id FROM vuseraizea  where user ="'. $_SESSION['user'] .'"  order by haizea_id asc;';
	  error_log($message.":".$start.":".$end.";\n", 3, $log_file);

	  $i = 0;
	  while ($row = $results->fetchArray()) {
		  $chart_vrb[$i] = $row["name"];
		  $haizea_id[$i] = $row["haizea_id"];
		  $haizea_names[$row["haizea_id"]] = $row["name"]; 
		  $i=$i+1;
	  }
	  $bd->close();
	} else {
  		exit('Error abriendo base de datos en: '.$path_db );
  	}

    if(isset($_GET['datestart']) and isset($_GET['dateend'])) {
		readDeviceValues($haizea_id,$start,$end);
		$BX_update = "TRUE";
    }
    else{
        readDeviceValues($haizea_id,"","");
    }
    
    //We collect all, (06,12,24) max and avg values for all Aizea devices
	readAllMaxAvgValuesSpider();

?>
