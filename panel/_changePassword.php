<?php
include '_includes.php';
checkSecurity();

if(isset($_REQUEST['password'])) {
	$password = $_REQUEST['password'];
} else {
	$password = "";
}

$email = $_SESSION['USER_email'];

$sSql = "UPDATE `sys_users` SET `password` = ? WHERE `email` = '$email';";

$result = ExecuteSql($sSql, array(null, $password));

echo $result;

?>