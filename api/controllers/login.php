<?php

/*
**

This is the login processing script.
it will be called with a POST request
@param action action to take (login/logout/getUserInfo)
@param username username
@param pwd password

@output [JSON/Class:User] Info about user
@output [false] Error (no post variables or incorrect username and password)


Please hash password on client side with:

<script src="http://crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/sha512.js"></script>
<script>
    var hash = CryptoJS.SHA512("Message");
</script>


Expects something like:
$('.login form').submit(function(event) {
	// Act on the event
	event.preventDefault();

	var un = $(this).find(":input[name='username']").val();
	var pwd = $(this).find(":input[name='pwd']").val();
	
	var user = new User(un, pwd); //JS User object
	user.onLogin = function(user) {
		// will only be called if succesful login
		// 'this' variable is identical to the user variable
	}

	user.login(function(userResponse) {
		// other way to do something on login event
		// if not specified, it will run onLogin
		
	}, function(errorResponse) {
		// on failed login
	});
});

*/

require_once('../includes/require.php');

$sec_session = New SecureSession();

$sec_session->sec_session_start();

if(isset($_POST['action']) && $_POST['action'] == 'login') {

	if(isset($_POST['username'], $_POST['pwd'])) {
		$username = $_POST['username'];
		$password = $_POST['pwd']; // This should ideally be an md5 hashed password
		if($sec_session->login($username, $password) == true) {
			//login success
			// retrieve their info from the db
			$newUser = new User(false, $username);
			// we return a JSON User object
			$fullUser = $newUser->getUserInfo();
			header('Content-Type: application/json');
			if($fullUser instanceof User) $newUser = $fullUser;
			// clean up password, salt and conn
			$newUser->password = false;
			$newUser->salt = false;
			$newUser->conn = false;
			echo $newUser->encodeJSON();
		} else {
			//login failed
			echo "false";
		}
	} else {
		// Incorrect POST vars
		echo "false";
	}
} else if(isset($_POST['action']) && $_POST['action'] == 'logout') {
	$sec_session->logout();
	$emptyUser = new User();
	echo $emptyUser->encodeJSON();
} else if(isset($_POST['action']) && $_POST['action'] == 'getUserInfo') {
	// can't do that if user is not logged in
	if($sec_session->login_check()) {
		$newUser = new User(false, $_SESSION['username']);
		$newUser = $newUser->getUserInfo();
		echo $newUser->encodeJSON();
	} else echo "false";
	
}

?>
