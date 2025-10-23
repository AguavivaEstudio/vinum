<?php
include '_includes.php';

$ds = DIRECTORY_SEPARATOR;
$storeFolder = '../uploads/';

if (!empty($_FILES)) {
	$tableName	= $_REQUEST ['tableName'];
	$columnName	= $_REQUEST ['columnName'];
	$rowId		= $_REQUEST ['rowId'];

	$temp       = explode(".", $_FILES["file"]["name"]);
	$extension  = end($temp);
	$fileName   = urls_amigables(str_replace(".$extension", "", $_FILES["file"]["name"]));
	
	if ($_FILES["file"]["error"] > 0) {
		echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
	} else {
		if (file_exists("../uploads/" . $fileName.".".$extension)) {
			$fileName1 = $fileName . time() .".". $extension;
			$fileName2 = "$fileName" . time() ."-sm.$extension";

		} else {
			$fileName1 = "$fileName.$extension";
			$fileName2 = "$fileName-sm.$extension";
		}
		move_uploaded_file($_FILES["file"]["tmp_name"], $storeFolder . $fileName1);

		if ($_SESSION['sysConfig']['ImageResize'] == 'true') {
			// Get specific maxWidth and maxHeight
			$sSql = "SELECT `key`, `value` FROM sys_config WHERE `key` IN (?,?)";
			$result = ExecuteSql($sSql, array(null, "$tableName-$columnName-ImageMaxWidth", "$tableName-$columnName-ImageMaxHeight"));
			
			$config = [];
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$config[$row['key']] = $row['value'];
			}
			$imageMaxWidth  = $config["$tableName-$columnName-ImageMaxWidth"] ?? $_SESSION['sysConfig']['ImageMaxWidth'];
			$imageMaxHeight = $config["$tableName-$columnName-ImageMaxHeight"] ?? $_SESSION['sysConfig']['ImageMaxHeight'];
			
			echo "
			
			
			imageMaxHeight: $imageMaxHeight";
			echo "
			
			
			imageMaxWidth: $imageMaxWidth";


			if ($extension === 'webp') {
				resize_image_webp($storeFolder.$fileName1,$storeFolder.$fileName2, $imageMaxWidth, $imageMaxHeight, 80,true);
			} else {
				if (!copy($storeFolder . $fileName1, $storeFolder . $fileName2)) {
					echo "Error al duplicar";
				} else {
					resizeImage($storeFolder . $fileName2, $imageMaxWidth, $imageMaxHeight, 'crop');
				}
			}
		}

		$sSql = "INSERT INTO `sys_files` (`id`, `tableName`, `columnName`, `rowId`, `fileName`, `publish`, `order`, `comment`) VALUES (NULL, ?, ?, ?, ?, '1', '0', NULL);";
		$result = ExecuteSql($sSql, array(null, $tableName, $columnName, $rowId, $fileName1));
	}
}
?>