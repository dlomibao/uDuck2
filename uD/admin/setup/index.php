<html>
<head><title>uDuck2 setup</title></head>
<body>
<?php 
	if(!$_GET['install'] && !$_POST['install'] && !file_exists("../uD_config.php")){
		echo 
		'<img src="../img/uDuckLogo.png">
		<form>
			Would you like to install uDuck2?
			<br>
			<input type="submit" name="install" value="yes">
		</form>
		
		';	
		
	}elseif(file_exists("../uD_config.php")){
		echo "uD_config.php detected! If you wish to reinstall, you must <a href='./uninstall.php'>uninstall uDuck2 first</a> ";
	}else{
		if($_POST['install'] != "submit"){
			echo '
				<form method="post">
					<table style="width:600px">
						<tr>
							<td colspan=2>
								In order to install uDuck2, you must have a MySQL user account
								with permissions to create databases and users. This will only
								be used during installation and will not be stored afterward for
								security reasons. (root is the default but any admin level user will work)
							</td>
						</tr>
						<tr><td>MySQL username</td><td><input type="text" name="dbroot" value="root" ></td></tr>
						<tr><td>MySQL password</td><td><input type="password" name="dbrootpass"></td></tr>
						<tr>
							<td colspan=2>
								The following will be used internally by the uDuck2 application to connect to the database.
								To provide more secure access, two db users accounts will be created by setup
								to be used by the application. Both only have access to the specified database you name.
								One will have read and write privillages while the other only has read privillages.
							</td>
						</tr>
						<tr><td>DB host</td><td><input type="text" name="dbhost" value="localhost"></td></tr>
						<tr><td>DB name</td><td><input type="text" name="dbname" value="ud2_db"></td></tr>
						<tr><td>DB account</td><td><input type="text" name="dbuser" value="ud2user"></td></tr>
						<tr><td>DB userpass</td><td><input type="text" name="dbuserpass" value="UDpassword123"></td></tr>
					
						<tr><td>DB read only account</td><td><input type="text" name="dbrouser" value="ud2readonly"></td></tr>
						<tr><td>DB read only password</td><td><input type="text" name="dbrouserpass" value="UDreadonlypass"></td></tr>
						
					</table>
					<br>
					<input type="submit" name="install" value="submit">
					<input type="reset" value="reset">
				</form>';

		}else{//build database
			echo "submitted<br>";
			$dbroot      = $_POST['dbroot'];
			$dbrootpass  = $_POST['dbrootpass'];
			$dbname      = $_POST['dbname'];
			$dbuser      = $_POST['dbuser'];
			$dbuserpass  = $_POST['dbuserpass'];
			$dbrouser    = $_POST['dbrouser'];
			$dbrouserpass= $_POST['dbrouserpass'];
			$dbhost      = $_POST['dbhost'];
			

			
			//try creating database and users
			try{
				$dbh = new PDO("mysql:host=$dbhost",$dbroot,$dbrootpass);
				/*//not a good idea to allow this could arbitrarily drop another database
				 * $dbh->exec("SET FOREIGN_KEY_CHECKS=0;
							DROP DATABASE `$dbname` IF EXISTS;
							");
				 */
				$dbh->exec("CREATE DATABASE `$dbname`;
							CREATE USER '$dbuser'@'$dbhost' IDENTIFIED BY '$dbuserpass';
							GRANT ALL ON `$dbname`.* TO '$dbuser'@'$dbhost';
							CREATE USER '$dbrouser'@'$dbhost' IDENTIFIED BY '$dbrouserpass';
							GRANT SELECT ON `$dbname`.* TO '$dbrouser'@'$dbhost';
							");
				$dbh=null;
				echo "created database<br>";
			}catch(PDOException $e){
				echo "failed creating database<br>";
				die("Error creating Database or users".$e->getMessage());	
			}
			
			
			try{
				$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname",$dbuser,$dbuserpass);
				$dbh->exec("CREATE TABLE IF NOT EXISTS `user`
							(
								`id`       INT NOT NULL  AUTO_INCREMENT PRIMARY KEY,
								`username` VARCHAR(64)  NOT NULL ,
								`displayname` VARCHAR(255),
								`email`    VARCHAR(255) NOT NULL,
								`hash`     VARCHAR(255) NOT NULL,
								`permissions` INT NOT NULL DEFAULT 0,
								`created`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
							) ENGINE=INNODB;
							");
				$dbh->exec("CREATE TABLE IF NOT EXISTS `post`
							(
								`id`		INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
								`title`		VARCHAR(255),
								`authorid`	INT NOT NULL,
								`caption`	TEXT,
								`body`		MEDIUMTEXT,
								`catid`		INT,
								`visible`	TINYINT NOT NULL,
								`modified`	TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
								`created`	TIMESTAMP NOT NULL,
								INDEX `author` (`authorid` ASC),
								INDEX `category` (`catid` ASC)
							) ENGINE=INNODB;
							");
				$dbh->exec("CREATE TABLE IF NOT EXISTS `category`
							(
								`id`		INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
								`name`		VARCHAR(255) NOT NULL,
								`groupname` VARCHAR(255) NOT NULL
							) ENGINE=INNODB;
							");
				$dbh->exec("CREATE TABLE IF NOT EXISTS `group`
							(
								`id`		INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
								`name`		VARCHAR(255) NOT NULL,
								`catid` 	INT,
								`caption`	TEXT,
								INDEX `category` (`catid` ASC)
							) ENGINE=INNODB;
							");
				$dbh->exec("CREATE TABLE IF NOT EXISTS `postgroup`
							(
							`groupid`	INT NOT NULL,
							`postid`	INT NOT NULL,
							PRIMARY KEY (`groupid`, `postid`)
							) ENGINE=INNODB;
							"
						);
				$dbh->exec("CREATE TABLE IF NOT EXISTS `tags`
							(
								`postid` INT NOT NULL,
								`tag`	 VARCHAR(64) NOT NULL,
								PRIMARY KEY(`postid`,`tag`)
							) ENGINE=INNODB;
							");
				$dbh->exec("CREATE TABLE IF NOT EXISTS `thumbnail`
							(
								`type`	VARCHAR(64) NOT NULL,
								`id`	INT NOT NULL,
								`url`	TEXT,
								PRIMARY KEY (`type`,`id`)
							) ENGINE=INNODB;
							");
				$dbh->exec("CREATE TABLE IF NOT EXISTS `settings`
							(
								`setting` VARCHAR(255) NOT NULL PRIMARY KEY,
								`value`	  VARCHAR(255)
							) ENGINE=INNODB;
							");
				//add foreign keys	
				$dbh->exec("ALTER TABLE `post` ADD FOREIGN KEY (`authorid`) 
							REFERENCES `user`(`id`)
							ON DELETE CASCADE ON UPDATE CASCADE;
							");
				$dbh->exec("ALTER TABLE `post` ADD FOREIGN KEY (`catid`)
							REFERENCES `category`(`id`)
							ON DELETE SET NULL ON UPDATE CASCADE;
				");
				$dbh->exec("ALTER TABLE `group` ADD FOREIGN KEY (`catid`)
							REFERENCES `category`(`id`)
							ON DELETE SET NULL ON UPDATE CASCADE;
				");
				$dbh->exec("ALTER TABLE `postgroup` ADD FOREIGN KEY (`groupid`)
							REFERENCES `group`(`id`)
							ON DELETE CASCADE ON UPDATE CASCADE;
				");
				$dbh->exec("ALTER TABLE `postgroup` ADD FOREIGN KEY (`postid`)
							REFERENCES `post`(`id`)
							ON DELETE CASCADE ON UPDATE CASCADE;
				");				
				$dbh->exec("ALTER TABLE `tags` ADD FOREIGN KEY (`postid`)
							REFERENCES `post`(`id`)
							ON DELETE CASCADE ON UPDATE CASCADE;
				");	
				$dbh=null;
				echo "created Tables<br>";
				
			}catch(PDOException $e){
				echo "failed creating tables<br>";
				die("Error creating Tables".$e->getMessage());	
			}
			
			
			
			$configfile=
			"<?php"."\n".
			"/**uDuck Config File"."\n".
			"* this is where the basic configuration goes"."\n".
			"*/"."\n".
			"//      Setting Name		Value"."\n".
			"define('DB_HOST'           ,'{$dbhost}');"."\n"."\n".
			"define('DB_USER'           ,'{$dbuser}');"."\n".
			"define('DB_USERPASS'       ,'{$dbuserpass}');"."\n"."\n".
			 
			"define('DB_USER_RO'        ,'{$dbrouser}');//READ ONLY USER"."\n".
			"define('DB_USERPASS_RO'    ,'{$dbrouserpass}');"."\n"."\n".
			
			"define('DB_NAME'           ,'{$dbname}');"."\n".
			 
	
			"?>";
			
			file_put_contents("../uD_config.php", $configfile);
			echo "configuration file written.<br>";
			
			echo "<br><b>Done!</b>";
		}

	} //end of installation process
?>

</body>
</html>