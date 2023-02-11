<?php
  	$ini          = parse_ini_file('app.ini');
  	$path_db      = $ini['full_path_db'];

  	if (file_exists($path_db)) {
  		$bd = new SQLite3($path_db);
      $results = $bd->query('SELECT name FROM tdevices;');
      $i = 0;
      while ($row = $results->fetchArray()) {
          $chart_vrb[$i] = $row["name"];
          $i=$i+1;
      }
  	} else {
  		exit('Error abriendo base de datos en: '.$path_db );
  	}
?>
