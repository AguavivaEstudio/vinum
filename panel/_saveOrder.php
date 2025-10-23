<?php
include '_includes.php';
checkSecurity();

$id	= $_REQUEST ['id'];
$order	= $_REQUEST ['order'];

$sSql = "UPDATE `sys_files` SET `order` = ? WHERE `id` = ?;";
$result = ExecuteSql($sSql, array(null, $order, $id));
?>