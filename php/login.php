<!DOCTYPE html>
<html>
	<head>
        <title>IBIL-Login Configurator</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
				<script type="text/javascript" src="js/bootstrap.bundle.min.js" ></script>
				<script type="text/javascript" src="js/bootstrap.min.js" ></script>
				<link rel="stylesheet"  href="css/bootstrap.min.css"/>
				<link rel="stylesheet"  href="css/estilos.css"/>
    </head>
		<div class = "datafield">
		<h2 align = "center">IBIL Welcome!!</h2>
		<p>Please fill this form log in.</p>
		<form action="php/register.php" method="post">
			<div class="form-group">
				<label>Username</label>
				<input type="text" style="width:200px" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
				<span class="invalid-feedback"><?php echo $username_err; ?></span>
			</div>
			<div class="form-group">
				<label>Password</label>
				<input type="password" style="width:200px" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
				<span class="invalid-feedback"><?php echo $password_err; ?></span>
			</div>
			<div class="form-group">
				<input type="submit" class="btn btn-info"  value="Submit" style="background-color: #357EC7;">
				<input type="reset"  class="btn btn-info"  value="Reset" style="background-color: #357EC7;">
			</div>
		</form>
		<div>
</html>
