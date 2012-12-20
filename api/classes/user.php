<?php

class User {
	public $id;
	public $username;
	public $password;
	public $salt;
	public $conn;
	public $loggedIn = false;
	private $data = array();

/*****

 Configuration Section
-=====================-

Change at your own peril

****/

	public $config = array(
		"levels" => array(
			"0" => "guest",
			"1" => "user",
			"2" => "moderator",
			"3" => "admin"
		),
		"moderator" => 1,
		"admin" => 2

	);






/**** 

  Function Definitions
 -====================-


*/




	function __construct(
							$id = false,
							$username = false,
							$password = false,
							$email = false,
							$salt = false,
							$conn = false,
							$loggedIn = false
						) {

		if($id instanceof User) { // if we supply only one argument
			$this->id = $id->id;
			$this->username = $id->username;
			$this->password = $id->password;
			$this->email = $id->email;
			$this->salt = $id->salt;	
			$this->loggedIn = $id->loggedIn;	
		} else {
			$this->id = $id;
			$this->username = $username;
			$this->password = $password;
			$this->email = $email;
			$this->salt = $salt;
			$this->loggedIn = $loggedIn;
		}
		if($conn instanceof mysqli) $this->conn = $conn;
		else $this->conn = new mysqli(DB_USERSERVER, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT) or
				die('There was a problem connecting to the database.');

	}

	function __set($name, $value) {
		$this->data[$name] = $value;
	}

	function __get($name) {
		if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
	}

	/**  As of PHP 5.1.0  */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**  As of PHP 5.1.0  */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

	function getUserInfo() {
		$query = 'SELECT * FROM ' . DB_USERTABLE . ' WHERE UserName = ? LIMIT 1';
		
		if ($stmt = $this->conn->prepare($query)) { 
	        
	        $stmt->bind_param('s', $this->username); // Bind "$user_id" to parameter.
	        $stmt->execute(); // Execute the prepared query.
	        //$stmt->store_result();
	 

	       
	        	$meta = $stmt->result_metadata();

				while ($field = $meta->fetch_field()) {
				  
				  $parameters[] = &$row[$field->name];
				}

				call_user_func_array(array($stmt, 'bind_result'), $parameters);

				while ($stmt->fetch()) {
				  
				  foreach($row as $key => $val) {
						
					$dummyUser = new User();
			    	if(isset($dummyUser->$key)) {
			    		$this->$key = $val; 	
			    	} else {
			    		$this->data[$key] = $val;
			    	}
				  }
				}
	        	//$stmt->bind_result($newUser->id, $newUser->password, $newUser->email, $newUser->salt); // get variables from result.
	        	//$stmt->fetch();

	           
	           return $this;

	    } else return false;
	}

	// http://www.php.net/manual/en/function.json-encode.php#96248
	function encodeJSON() { 
	    foreach ($this as $key => $value) { 
	        if($key == "data") {
	        	
	        	foreach($this->data as $k => $v) {
	        		
	        		$json->$k = $v;
	        	}
	        } else $json->$key = $value; 

	    } 

	    return json_encode($json); 
	} 

	function decodeJSON($json_str) { 
	    $json = json_decode($json_str, 1); 

	    foreach ($json as $key => $value) { 
	    	$dummyUser = new User();
	    	if(isset($dummyUser->$key)) {
	    		$this->$key = $value; 	
	    	} else {
	    		$this->data[$key] = $value;
	    	}

	        
	    } 
	}	 
}

?>