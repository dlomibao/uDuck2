<?php
/*calculates the optimum cost for bcrypt on your server. 
 * since this is designed to be used by only a handful of
 * users, bcrypt is set to take AT LEAST .2 seconds. 
 * (if it is close and goes over it could be up to .39 sec)
 * maximizing cost without being too noticable increases security
 * */
function optimum_bcrypt_cost(){
	$chars='./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	$saltstring="";
	for($i=0;$i<22;$i++) $saltstring.=$chars[mt_rand(0,63)];
	
	$length=0;
	$timeout=false;
	
	$cost=5;
	while($length<.25 && !$timeout){
		$salt="";
		$salt=sprintf('$2a$%02d$',$cost);
		$salt.=$saltstring;//make sure to use the same salt each time*/
		$passvar="";
		for($i=0;$i<10;$i++) $passvar.=$chars[mt_rand(0,63)];
		$hash=crypt($passvar, $salt);
		$start=microtime(true);
			crypt($passvar,$hash);
		$end=microtime(true);
		$length=$end-$start;
		$cost+=1;
		if($cost>31){$timeout=true;}
	}
	return $cost;
}

		/**hashes a password in a secure non reversible way that is safe from rainbow table
		 *  attacks and can be made stronger by increasing the cost
		 * @param password		password string to by hashed
		 * @param cost			
		 */
		function blowfishCrypt($password,$cost)//cost makes it harder for hackers to crack the password
		{
			$chars='./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
			$salt=sprintf('$2a$%02d$',$cost);
			for($i=0;$i<22;$i++) $salt.=$chars[mt_rand(0,63)];
			return crypt($password,$salt);
		}
?>