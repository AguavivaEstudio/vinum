<?php
ob_start();
include '_includes.php';
checkSecurity();

$tableSelected = "14";

$permissions = getTablePermission($tableSelected);

if (isset($_REQUEST['id'])) {
	$id = $_REQUEST['id'];
	$titleNewUpdate = ' Update ';
} else {
	$id = "";
	$titleNewUpdate = ' New ';
}

$sSql = "SELECT `group`, `menuText`, `table` FROM `sys_tables` WHERE `id` = ?;";
$result = ExecuteSql($sSql, array(null, $tableSelected));
$row = $result->fetch_array(MYSQLI_ASSOC);
$group = $row['group'];
$menuText = $row['menuText'];
$table = $row['table'];

CreateHeadder();
?>
<body>
    <div class='container-fluid'>
    <?php
			createMenu();
			echo "<h1 id='divError' class='error'></h1>";

			if (($permissions['view'] != 1) || (is_null($table))) {
				echo "<h1 class='error'>" . getLangVar('AuthorizationDenied') . "</hi>";
			} else {
				$baseDir = dirname(__FILE__);
				$baseDir = str_replace('panel', 'uploads\\', $baseDir);
				$baseDir = str_replace('\\', '/', $baseDir);

				$sSql = "SELECT `id`, `fileName` FROM `sys_files` WHERE `tableName` = 'data_files' AND `columnName` = 'file_clientes' AND `publish` = 1 AND `rowId` = 1;";
				$result = ExecuteSql($sSql, null);
				$sSql = "TRUNCATE TABLE `wines_import`;";
				ExecuteSql($sSql, null);
				$archivosProcesados = 0;
				while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
					$id = $row['id'];
					$fileName = $row['fileName'];

					$rowsAffected = importFile($baseDir . $fileName, 'wines_import');
					
					ExecuteSql("UPDATE `wines_import` SET `active` = 1 WHERE `active` IS NULL;", null);

					$sSql = "UPDATE `sys_files` SET `publish` = 0, `comment` = concat('Procesado: ', now()) WHERE `id` = $id;";
					ExecuteSql($sSql, null);

					echo "<div>Archivo procesado: $fileName - Registros: $rowsAffected</div><br><br>$baseDir$fileName";
					$archivosProcesados++;
				}

				if ($archivosProcesados > 0) {

					$sSql = "
						INSERT INTO `wines` ( `id`, `name`, `brand`, `grape`, `type`, `country`, `region`, `subregion`, `amount`, `segment`, `wine_stopper`, `is_organic`, `other`, `sku`, `barcode`, `active`, `order` )
							SELECT WI.`id`, WI.`name`, WI.`brand`, WI.`grape`, WI.`type`, WI.`country`, WI.`region`, WI.`subregion`, WI.`amount`, WI.`segment`, WI.`wine_stopper`, WI.`is_organic`, WI.`other`, WI.`sku`, WI.`barcode`, WI.`active`, WI.`order`
						  	FROM `wines_import` AS WI
						 	WHERE WI.`sku` NOT IN (SELECT `sku` FROM `wines` WHERE `sku` IS NOT NULL)
						 	GROUP BY WI.`sku`
						;";
					ExecuteSql($sSql, null);

					$sSql = "
							UPDATE `wines` AS CL
							 	INNER JOIN `wines_import` AS WI ON CL.`sku` = WI.`sku`
							   	SET CL.`id` = WI.`id`,
									CL.`name` = WI.`name`,
									CL.`brand` = WI.`brand`,
									CL.`grape` = WI.`grape`,
									CL.`type` = WI.`type`,
									CL.`country` = WI.`country`,
									CL.`region` = WI.`region`,
									CL.`subregion` = WI.`subregion`,
									CL.`amount` = WI.`amount`,
									CL.`segment` = WI.`segment`,
									CL.`wine_stopper` = WI.`wine_stopper`,
									CL.`is_organic` = WI.`is_organic`,
									CL.`other` = WI.`other`,
									CL.`sku` = WI.`sku`,
									CL.`barcode` = WI.`barcode`,
									CL.`active` = WI.`active`,
									CL.`order` = WI.`order`
							;";
					ExecuteSql($sSql, null);
				} else {
					echo "<div>No existen archivos para procesar</div>";
				}
			}
			?>
    </div>
</body>
</html>