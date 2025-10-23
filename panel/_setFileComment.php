<?php
include '_includes.php';

$id = $_REQUEST['id'];
$comment = $_REQUEST['comment'];

$sSql = "UPDATE  `sys_files` set  `comment` = ? WHERE `id` = ?;";
$result = ExecuteSql($sSql, array(null, $comment, $id));
