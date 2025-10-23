<?php
date_default_timezone_set("UTC"); 

function logTxtFile($sLog){
	$logFile = 'log/' . date('Y') . '-' . date('m') . '-' . date('d');
	isset($_SESSION['USER_email']) ? $USER_email = $_SESSION['USER_email'] : $USER_email = 'Invalid user';

	$plaintext = "------------------------------------------------------------------------------------------ \n";
	$plaintext = $plaintext . "Date: " . date('H:i:s', time()) . "\n";
	$plaintext = $plaintext . "User: " . $USER_email . "\n";
	$plaintext = $plaintext . "IP: " . get_client_ip() . "\n";
	$plaintext = $plaintext . "File: " . basename($_SERVER["SCRIPT_FILENAME"], '') . "\n";
	$plaintext = $plaintext . "Request: " . print_r($_REQUEST, true) . "\n";
	$plaintext = $plaintext . $sLog . "\n";

	file_put_contents($logFile, $plaintext, FILE_APPEND);
}

// logTxtFile('');
?>