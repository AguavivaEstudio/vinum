<?php
include '_includes.php';

checkSecurity();

$t = $_GET['t'];
$i = $_GET['i'];

$permissions = getTablePermission($t);

if($permissions['delete']) {
	if ($t == 'sys_files') {
		$sSql = "SELECT fileName FROM sys_files WHERE id = ?;";
		$result = ExecuteSql($sSql, array(null, $i));
		$row = $result -> fetch_array(MYSQLI_ASSOC);
		unlink("../uploads/" . $row['fileName']);
		if ($_SESSION['sysConfig']['ImageResize'] == 'true') {
			$temp       = explode(".",  $row['fileName']);
			$extension  = end($temp);
			$fileName2 = $temp[0]."-sm.$extension";
			unlink("../uploads/" . $fileName2);
		}
	}

	$sSql   = "DELETE FROM $t WHERE id = ?;";
	$result = ExecuteSql($sSql, array(null, $i));

	$sSql   = "SELECT fileName FROM sys_files WHERE tableName = ? AND rowId = ?;";
	$result = ExecuteSql($sSql, array(null, $t, $i));
	while ($row = $result -> fetch_array(MYSQLI_ASSOC)) {
		unlink("../uploads/" . $row['fileName']);
	}
	$sSql   = "DELETE FROM sys_files WHERE tableName = '$t' AND rowId = ?;";
	$result = ExecuteSql($sSql, array(null, $i));

	logAction("Delete record | $t", $sSql . " | id=" . $i); //Habilitar pasa loguear las actualizaciones
} else {
	echo getLangVar('AuthorizationDenied');
}
?>