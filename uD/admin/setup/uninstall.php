<?php
if($_POST['uninstall']){
	//uninstall==true
	include("../uD_config.php");
	try{
		echo "trying to delete stuff on database<br>";
		$dbh= new PDO("mysql:host=".DB_HOST,$_POST['rootuser'],$_POST['rootpass']);
		echo "...connected<br>";
		$dbh->exec("SET FOREIGN_KEY_CHECKS=0;");
		$dbh->exec("DROP DATABASE ".DB_NAME.";");
		$dbh->exec("DROP USER '".DB_USER."'@'".DB_HOST."'");
		$dbh->exec("DROP USER '".DB_USER_RO."'@'".DB_HOST."'");
		echo "finished cleaning database<br>";
				  
	}catch(PDOException $e){
		echo "<b>failed!</b> something went wrong with removing database and users";
		die();
	}
	if(unlink("../uD_config.php")){
		echo "<br>Deleted config file<br>";
		echo "<br><b>Uninstall Complete!</b>";
	}else{
		echo "<br>Failed to delete config file";die();
	}
	
	
}else{
	echo "<html><head></head><body>
	Would you like to uninstall uDuck2?<br>
	<form method='POST'>
	<table>
		<tr><td>mysql user</td><td><input type='text' name='rootuser' value='root'></td></tr>
		<tr><td>mysql user pass</td><td><input type='password' name='rootpass'></td></tr>
		<tr><td><input type='submit' name='uninstall' value='uninstall'></td></tr>
	</table>
	</form>
	</body></html>";
}

?>