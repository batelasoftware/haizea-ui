<?php
session_start();

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
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
	if( ($_POST["username"] == "ibil") && ($_POST["password"] == "ibil")){
        	// User::set_name("fff");
        	// <a href="page2.php?varname=<?php echo $var_value ?">Page2</a>
        	$_SESSION['user']  = "ibil";
        	header("Location: ../home.html");
     		exit();
        }
        else {
			header("Location: ../index.html");
			exit();
        }
    }
}

?>
