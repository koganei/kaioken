<?php

// Define constants here

if($_SERVER['SERVER_NAME'] == "localhost" || true) { // while we set up prod
	//dev server
	
	define('DB_SERVER', 'berzdump.dyndns-free.com;port=33066');
	define('DB_USERSERVER', 'berzdump.dyndns-free.com');
	define('DB_PORT', '33066');
	define('DB_USER', 'marc.khoury');
	define('DB_PASSWORD', 'espion');
	define('DB_NAME', 'mypages');

	define('DB_USERTABLE', 'user');
	define('DB_LOGINTABLE', 'login_attempts');

	define('DB_BUSINESSTABLE', 'business');

} else {
	//prod server
	
	define('DB_SERVER', 'berzdump.dyndns-free.com;port=33066');
	define('DB_USERSERVER', 'berzdump.dyndns-free.com:33066');
	define('DB_USER', 'marc.khoury');
	define('DB_PASSWORD', 'espion');
	define('DB_NAME', 'mypages');

	define('DB_USERTABLE', 'user');
	define('DB_LOGINTABLE', 'login_attempts');

	define('DB_BUSINESSTABLE', 'business');
}

// some useful functions
// 
function issetor(&$variable, $or = NULL) {
    return $variable === NULL ? $or : $variable;
}


?>
