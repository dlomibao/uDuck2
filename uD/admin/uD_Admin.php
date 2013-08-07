<?php
	if(!isset($_SESSION)){session_start();} 
	require_once dirname(__FILE__)."/uD_config.php";
	/**
	 *  the uDuck admin class
	 *  used to handle backend stuff
	 */
	class uDuck_Admin{
		private $HOST = DB_HOST;
		private $DB	  = DB_NAME;
		private $USER = DB_USER_RO;
		private $PASS = DB_USERPASS_RO;
		
		public $con;//connection object (TODO: make private for final code)
		
		public $posts;//holds the last accessed group of posts as an array of arrays
		public $apost;//holds the last accessed post as an array
		
		//requires an initial call to fill, after grab from db it will be in mem
		public $u;
		public $c;
		public $g;
		
		/**creates a new cms object defaults to readonly (READWRITE='r') and autoconnect
	 	 * @param READWRITE		r or rw
		 * @param CONNECT 		true or false 
	 	 */
		public function __construct($READWRITE="r",$CONNECT=TRUE){
			if($READWRITE=="rw"){
				$this->USER= DB_USER;
				$this->PASS= DB_USERPASS;
			}
			if($CONNECT){
				$this->connect();
			}
		}//end construct
		
		/** connects via PDO to Database
		 * 
		 */
		public function connect(){
			try{
				$db=$this->DB;
				$host=$this->HOST;
				$user=$this->USER;
				$pass=$this->PASS;
				$this->con = new PDO("mysql:dbname=$db;host=$host",$user,$pass);
			 
			 return 1;
			}catch(PDOException $e){
				die("Database Connection Failed: ". $e->getMessage());
				return 0;
			}
			
		}//end connect
		
		/**closes connection*/
		public function close(){
			$this->con=null;
		}
		/**takes a sql statement, the parameters, 
		 * and whether to 'fetch' (the first row returned),
		 *  'fetchAll',or 'execute' (only return true or false)
		 * @param sql	 			a string of the sql code (parameterized)
		 * @param parameterArray	array containing the values of the parameters
		 * @param fetch				the method of execution/return to use. either fetch, fetchAll or execute
		 * @param errorreport		set to true if you want to print error info (default is false)
		 * @return data			depends of fetch type. 
		 * 							execute returns true/false on success/failure
		 * 							fetchAll returns all rows of result set
		 * 							fetch returns array indexed by both col name and 0 indexed col number
		 *  */
		private function genericQuery($sql,$parameterArray=array(),$fetch="fetchAll",$errorreport=false){
			$this->con->prepare($sql);
			$success=$statement->execute($paramerterArray);
			if($errorreport==true && !$success){
				print_r($statement->errorInfo());
			}
			if($fetch=="fetchAll"){ $data=$statement->fetchAll();}
			elseif($fetch=="fetch"){ $data=$statement->fetch();}
			elseif($fetch=="execute"){$data=$success;}
			else{$data=null;}
			$statement->closeCursor();
			return $data;
			
		}
		/**hashes a password in a secure non reversible way that is safe from rainbow table
		 *  attacks and can be made stronger by increasing the cost
		 * @param password		password string to by hashed
		 * @param cost			
		 */
		public function blowfishCrypt($password,$cost)//cost makes it harder for hackers to crack the password
		{
			$chars='./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
			$salt=sprintf('$2a$%02d$',$cost);
			for($i=0;$i<22;$i++) $salt.=$chars[mt_rand(0,63)];
			return crypt($password,$salt);
		}
		
		
	}
?>