<?php
header('Content-Type: application/json');
include '_includes.php';
checkSecurity();
$permissions = getTablePermission('pedidos');

if ($permissions['view'] != 1) {
	http_response_code(401);
} else {
	http_response_code(200);
	$jsonResponse = array();
	$dbName = $_server['DB_NAME'];
	$sSql = "SELECT COLUMN_NAME, IS_NULLABLE, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, ORDINAL_POSITION, COLUMN_TYPE, COLUMN_COMMENT FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$dbName' AND TABLE_NAME = 'pedidos';";
	$result = ExecuteSql($sSql, null);
	$jsonCol = array();
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

		if ($row['COLUMN_COMMENT'] != '') {
			$row['displatName'] = $row['COLUMN_COMMENT'];
		} else {
			$row['displatName'] = $row['COLUMN_NAME'];
		}

		if (($row['DATA_TYPE'] != 'longtext') && ($row['COLUMN_NAME'] != 'id')) {
			$row['showInList'] = true;
		} else {
			$row['showInList'] = false;
		}

		$jsonCol[] = $row;
	}
	$jsonResponse['columns'] = $jsonCol;

	$sSql = "SELECT * FROM pedidos;";
	$result = ExecuteSql($sSql, null);
	$jsonData = array();
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		foreach ($jsonCol as $column) {
			if ($column['COLUMN_TYPE'] == 'tinyint(1)') {
				$row[$column['COLUMN_NAME']] = ($row[$column['COLUMN_NAME']] == 1 ? true : false);
			}
		}

		$jsonData[] = $row;
	}
	$jsonResponse['rows'] = $jsonData;
	echo json_encode($jsonResponse);
}
