<?php
include '_includes.php';

checkSecurity();

$t = $_GET['t'];
$c = $_GET['c'];
$i = $_GET['i'];
$v = $_GET['v'];

$permissions = getTablePermission($t);

if($permissions['update']) {
	$sSql   = "update `$t` set `$c` = '$v' where `id` = ?";
	$result = ExecuteSql($sSql, array(null, $i));
	logAction('update check', $sSql . " | id=" . $i); //Habilitar pasa loguear las actualizaciones
} else {
	echo getLangVar('AuthorizationDenied');
}
?>