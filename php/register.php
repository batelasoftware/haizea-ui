<?php

$ini = parse_ini_file('/var/www/html/app.ini');
$path_db	= $ini['full_path_db'];
$log_file = "../haizea-ui.log";

session_start();

$passwd = "";
function readPassword($path_db){

	if (file_exists($path_db)) {
		$bd = new SQLite3($path_db);
		$results = $bd->query('SELECT passwd FROM tusers where name = "'.$_POST["username"].'"' );
		while ($row = $results->fetchArray()) {
			$passwd  = $row["passwd"];
		}
		$bd->close();
	} else {
		exit('Error abriendo base de datos en: '.$path_db );
	}
	return $passwd;
}


if (!isset($_SESSION['user'])) {
	$_SESSION['user']  = "no_user";
}

function checkUser ()
{
	if ($_SESSION['user']  == "no_user") {
		header("Location: ../index.html");
		exit();
	}
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
		$passwd = readPassword($path_db);
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
			// if( ($_POST["username"] == "csp") && ($_POST["password"] == "csp")){
			if ($_POST["password"] == $passwd){
        	// User::set_name("fff");
        	// <a href="page2.php?varname=<?php echo $var_value ?">Page2</a>
        	$_SESSION['user']  = $_POST["username"] ;
					//$_SERVER['user']  = "csp
        	header("Location: ../index.html");
     		exit();
        }
        else {
					header("Location: ../login.html");
			exit();
        }
    }
}

?>
