<?php


class SecureSession {

	public $session_name;
	public $secure; // true if https
	public $httponly; // // This stops javascript being able to access the session id. 

	private $conn;

	function __construct($sn = 'netforge_custom_session', $sec = false, $http = true, $conn = false) {
		$this->session_name = $sn;
		$this->secure = $sec;
		$this->httponly = $http;
		
		if($conn) $this->conn = $conn;
		else $this->conn = new mysqli(DB_USERSERVER, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT) or
				die('There was a problem connecting to the database.');
	}

	function sec_session_start() {
		ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies. 
	    $cookieParams = session_get_cookie_params(); // Gets current cookies params.
	    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $this->secure, $this->httponly); 
	    session_name($this->session_name); // Sets the session name to the one set above.
	    session_start(); // Start the php session
	    session_regenerate_id(true); // regenerated the session, delete the old one.

	}

	function login_check() {

	   // Check if all session variables are set
	   if(isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {
	     $user_id = $_SESSION['user_id'];
	     $login_string = $_SESSION['login_string'];
	     $username = $_SESSION['username'];
	     $ip_address = $_SERVER['REMOTE_ADDR']; // Get the IP address of the user. 
	     $user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.
	 
	     if ($stmt = $this->conn->prepare("SELECT UserPassword FROM " . DB_USERTABLE . " WHERE UserId = ? LIMIT 1")) { 
	        $stmt->bind_param('i', $user_id); // Bind "$user_id" to parameter.
	        $stmt->execute(); // Execute the prepared query.
	        $stmt->store_result();
	 
	        if($stmt->num_rows == 1) { // If the user exists
	           $stmt->bind_result($password); // get variables from result.
	           $stmt->fetch();
	           $login_check = hash('sha512', $password.$ip_address.$user_browser);
	           if($login_check == $login_string) {
	              // Logged In!!!!
	              
	              return true;
	           } else {
	              // Not logged in
	              return false;
	           }
	        } else {
	            // Not logged in
	            return false;
	        }
	     } else {
	        // Not logged in
	        return false;
	     }
	   } else {
	     // Not logged in
	     return false;
	   }
	}


	function login($un, $pwd) {
		
		$query = "SELECT UserId, UserName, UserPassword, UserSalt
				FROM  " . DB_USERTABLE . "
				WHERE UserName = ?
				LIMIT 1";

		if($stmt = $this->conn->prepare($query)) { // make sure teh query checks out
			$stmt->bind_param('s', $un); //replace the ? with the right strings to make sure it's secure
			$stmt->execute();

			$stmt->store_result();

			//make an empty user
			$currentUser = new User();

			$stmt->bind_result($currentUser->id, $currentUser->username, $currentUser->password, $currentUser->salt);
			//$stmt->bind_result($id, $username, $password, $email, $salt);
			$stmt->fetch();
			// maybe do the hashing in js?
			$pwd = hash('sha512', $pwd.$currentUser->salt);
			


			if($stmt->num_rows == 1) { // if the user exists
				// we check if the account is locked from too many login attempts

				if($this->checkbrute($currentUser->id, $this->conn) == true) {
					// Account is locked
					// Send an email to user saying their account is locked
					return false;
				} else {
					if($pwd == $currentUser->password) { // Check if the password in the db matches
						$ip_address = $_SERVER['REMOTE_ADDR'];
						$user_browser = $_SERVER['HTTP_USER_AGENT'];

						$currentUser->id = preg_replace("/[^0-9]+/", "", $currentUser->id); // XSS protection as we might print this value
						$_SESSION['user_id'] = $currentUser->id;
						
						$currentUser->username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $currentUser->username); // XSS protection as we might print this value
	               		$_SESSION['username'] = $currentUser->username;
						$_SESSION['login_string'] = hash('sha512', $pwd.$ip_address.$user_browser);
					
						//login successful
						return true;
					} else {
						// Password is not correct"INSERT INTO login_attempts (user_id, time) VALUES ('$user_id', '$now')"
						// we record this attempt in the db
						$now = time();
						$this->conn->query("INSERT INTO " . DB_USERTABLE . " (UserId, UserLastLoginTime) VALUES ('".$currentUser->id."', '".$now."')");
						return false;
					}	
				}
			
			} else return false;
			
		}

	}

	function checkbrute($user_id) {
	   // Get timestamp of current time
	   $now = time();
	   // All login attempts are counted from the past 2 hours. 
	   $valid_attempts = $now - (2 * 60 * 60); 
	 
	   if ($stmt = $this->conn->prepare("SELECT UserLastLoginTime FROM " . DB_USERTABLE . " WHERE UserName = ? AND UserLastLoginTime > '$valid_attempts'")) { 
	      
	      $stmt->bind_param('i', $user_id); 
	     
	      // Execute the prepared query.
	      $stmt->execute();
	      $stmt->store_result();
	     
	      // If there has been more than 5 failed logins
	      if($stmt->num_rows > 5) {
	         return true;
	      } else {
	         return false;
	      }

   		}
	}

	function logout() {
		// Unset all session values
		$_SESSION = array();
		// get session parameters 
		$params = session_get_cookie_params();
		// Delete the actual cookie.
		setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
		// Destroy session
		session_destroy();
		return true;
	}

	function addUser($user) {
		// should probably be a check there to see if $user is a user

		// Create a random salt
		$user->salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
		// Create salted password (Careful not to over season)
		$password = hash('sha512', $user->password.$user->salt);
		 
		// Add your insert to database script here. 
		// Make sure you use prepared statements!
		/*if ($insert_stmt = $mysqli->prepare("INSERT INTO members (username, email, password, salt) VALUES (?, ?, ?, ?)")) {    
		   $insert_stmt->bind_param('ssss', $username, $email, $password, $random_salt); 
		   // Execute the prepared query.
		   $insert_stmt->execute();

		} */
	}



}