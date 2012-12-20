<?php

$userConfig = array(
	"levels" => array(
		"0" => "user",
		"1" => "moderator",
		"2" => "admin"
	)
);

User::setConfig($userConfig);

?>