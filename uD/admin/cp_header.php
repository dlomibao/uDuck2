<?php
//starts session loads config and includes uDuckAdmin class
require_once "uD_Admin.php";
$uDadmin= new uDuck_Admin();
if(isset($_GET['logout'])){$uDadmin->logout();}
if(!isset($_SESSION['uID'])){
	$_SESSION['origin']=$_SERVER['SCRIPT_NAME'];
	header("Location: login.php");
	exit;
}
?>
<html>
	<head>
		<title>uDuck Control Panel</title>
		<link rel="stylesheet" href="style.css" type="text/css">
	</head>
	<body>
	<div id="UDUCKWRAP">
		<div id="UDUCKHEAD">
			<img src="img/uDuckLogo.png" height="100" width="100" class="fleft">
			<div id="UDUCKUSERBOX" class="fright">
				<span >Hello, <?php echo $_SESSION['uDisplay']; ?></span><br>
				<a href="?logout" id="headerlogout" >logout</a>
			</div>
			<div id="UDUCKTITLE">uDuck Content Management System</div>

		</div>
		<div id="UDUCKNAV">
			
		</div>
		<div id="UDUCKMAIN"><!--end of div in cp_footer.php-->
		<?php if(isset($_SESSION['alert_message'])){
			$sesalrt=filter_var($_SESSION['alert_message'],FILTER_SANITIZE_STRING);
			echo"<div id='alertmessage'> $sesalrt </div>";
			unset($_SESSION['alert_message']);
		}?>
