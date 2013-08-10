<?php 
require_once "uD_Admin.php";
if(!isset($_POST['submit'])){ ?>
<html>
	<head>
		<title>uDuck Login</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body style="text-align:center;">
	

	<div class="center bgcolorlight" style="padding:5%;width:300px;margin-top:100px;">
		
		<?php if(isset($_SESSION['alert_message'])){
			echo "<div class='center bgcolordark' style='padding:20px'>";
			$sesalrt=filter_var($_SESSION['alert_message'],FILTER_SANITIZE_STRING);
			echo "$sesalrt";
			unset($_SESSION['alert_message']);
			echo "</div><br>";
		}?>
		
		<img src="img/uDuckLogo.png" class="center"><br>
		<form method="POST">
		<table style="text-align:center">
			<tr>
				<td>Username: </td>
				<td>	<input type="text" name="user"></td>
			</tr>
			<tr>
				<td>Password:</td>
				<td> <input type="password" name="pword"></td>
			</tr>
			<tr>
				<td colspan="2" class="center"><input type="submit" name="submit" value="submit">
				    <input type="reset" value="reset"</td>
			</tr>
		
		</table>
		</form>
		<br>
		<a href="resetpass.php?resetform">forgot username/password</a><br>
	
				
	</div>	
	
	</body>
	
</html>
<?php }else{//if submitted
	
	
	$admin= new uDuck_Admin("rw");
	$user=$_POST['user'];
	$success=$admin->login($user,$_POST['pword']);
	if($success=="success"){
		if(isset($_SESSION['origin'])){
			$goto=$_SESSION['origin'];
		}else{
			$goto="index.php";
		}
		header("Location: $goto");
		exit;
	}elseif($success=="fail_nouser"){
		$_SESSION['alert_message']="No user '$user' found";
		header("Location: login.php");
		exit;
	}elseif($success=="fail_pass"){
		$left=MAX_LOGIN_ATTEMPT -$_SESSION['login_attempts'];
		$_SESSION['alert_message']="Incorrect password for user '$user'. $left login attempts left. ";
		header("Location: login.php");
		exit;
	}elseif($success=="fail_locked"){
		$lockedtime=$_SESSION['locked_time'];
		$_SESSION['alert_message']="User '$user' locked out for $lockedtime more minute(s)";
		header("Location: login.php");
		exit;
	}
	
	
}?>
