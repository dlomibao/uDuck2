
<?php
require_once "uD_Admin.php";
$uDadmin= new uDuck_Admin("rw");
if(!isset($_GET['action'])){
?>
<html>
<head>
<title>reset password</title>
<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body style="text-align:center;">
<div id="box" class="center bgcolorlight" style="padding:5%;width:400px;margin-top:100px;">
		<?php if(isset($_SESSION['alert_message'])){
			echo "<div class='center bgcolordark' style='padding:20px'>";
			$sesalrt=filter_var($_SESSION['alert_message'],FILTER_SANITIZE_STRING);
			echo "$sesalrt";
			unset($_SESSION['alert_message']);
			echo "</div><br>";
		}?>
<h1>reset password</h1>
<form method="POST" action="resetpass.php?action=sendreset">
<table>
	<tr>
		<td>e-mail address</td>
		<td><input type="text" name="email"></td>
	</tr>
	<tr> <td><input type="submit" value="submit"></td></tr>
</table>
</form>

</div>
</body>
</html>

<?php
}elseif($_GET['action']=="sendreset"){
	$email=filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
	if(!filter_var($email,FILTER_VALIDATE_EMAIL)){//invalid email redirect
		$_SESSION['alert_message']="Invalid Email Entered";
		header("Location: resetpass.php");
		exit;
	}
	
	$rs=urlencode($uDadmin->resetstring($email));
	
	$link="http://".$_SERVER['HTTP_HOST']."".dirname($_SERVER['REQUEST_URI'])."/resetpass.php?email=$email&rs=$rs&action=changeform";
	
	$to = $email;
	$subject= "uDuck CMS: Password Reset";
	$message="
	<html>
	<head><title>Reset Password</title></head>
	<body>
		<p> Click the link below to reset your password </p>
		<a href='$link'>$link</a>
	</body>
	</html>
	
	";
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: donotreply@'.$_SERVER['HTTP_HOST']."\r\n";
	if(mail($to, $subject, $message, $headers)){
		echo "password reset email sent. It should show up in your inbox soon";
	}else{
		echo "well crap... the password reset email didn't send...";
	}	
	
	
}elseif($_GET['action']=="changeform"){
	?>
<html>
<head></head>
<body>
 	<div>
 		<table>
 		<form method="post" action="resetpass.php?action=updatepass">
			<tr><td>reset string</td><td><input type="text" name="rs" value="<?php echo filter_var($_GET['rs'],FILTER_SANITIZE_STRING); ?>" ></td></tr>
 			<tr><td>email</td><td><input type="text" name="email" value="<?php echo filter_var($_GET['email'],FILTER_SANITIZE_EMAIL); ?>"></td></tr>
 			<tr><td>newpassword</td><td><input type="password" name="newpass"></td></tr>
 			<tr><td><input type="submit" value="submit"></td></tr>
 		</form>
 		</table>
 	</div>
</body>
</html>
	 <?php
}elseif($_GET['action']=="updatepass"){
	if($uDadmin->resetpass($_POST['email'], $_POST['rs'], $_POST['newpass'])){
		$_SESSION['alert_message']="Password Reset Successful";
		header("Location: login.php");
		exit;
		
	}else{//failed to update
		sleep(5);//discourage multiple attempts
		echo "failed to update password. The reset is probably invalid or expired.";
	}
	
}
	
?>