<?php
ob_start();
include '_includes.php';
checkSecurity();

$tableSelected = "14";
$permissions = getTablePermission($tableSelected);

$id = $_REQUEST['id'] ?? "";
$titleNewUpdate = $id ? ' Update ' : ' New ';

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

			if (($permissions['view'] != 1) || is_null($table)) {
				echo "<h1 class='error'>" . getLangVar('AuthorizationDenied') . "</h1>";
			} else {
				$baseDir = dirname(__FILE__);
				$baseDir = str_replace('panel', 'uploads\\', $baseDir);
				$baseDir = str_replace('\\', '/', $baseDir) . '/';

			function processImport($baseDir, $rowId, $importTable, $finalTable, $insertFields, $updateFields) {
				// Get files
				$sSql = "SELECT `id`, `fileName` 
						FROM `sys_files`
						WHERE `tableName` = 'data_files'
						AND `columnName` = 'file_clientes'
						AND `publish` = 1
						AND `rowId` = ?;";
				$result = ExecuteSql($sSql, array(null, $rowId));

				ExecuteSql("TRUNCATE TABLE `$importTable`;", null);

				$archivosProcesados = 0;

				// Import each file
				while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
					$id = $row['id'];
					$fileName = $row['fileName'];

					$rowsAffected = importFile($baseDir . $fileName, $importTable);

					// if active is empty, insert it with 1
					ExecuteSql("UPDATE `$importTable` SET `active` = 1 WHERE `active` IS NULL;", null);

					// Mark file as processed
					$sSql = "UPDATE `sys_files` SET `publish` = 0, `comment` = concat('Procesado: ', now()) WHERE `id` = $id;";
					ExecuteSql($sSql, null);

					echo "<div>Archivo procesado: <b>$fileName</b> - Registros importados: $rowsAffected</div>";
					$archivosProcesados++;
				}

				// Insert and update into final table
				if ($archivosProcesados > 0) {

					$sSql = "
						INSERT INTO `$finalTable` ($insertFields)
							SELECT $insertFields
							FROM `$importTable`
							WHERE `sku` NOT IN (SELECT `sku` FROM `$finalTable` WHERE `sku` IS NOT NULL)
							GROUP BY `sku`;";
					ExecuteSql($sSql, null);

					$sSql = "
							UPDATE `$finalTable` AS F
								INNER JOIN `$importTable` AS I ON F.`sku` = I.`sku`
								SET $updateFields;";
					ExecuteSql($sSql, null);

				} else {
					echo "<div>No existen archivos para procesar ($finalTable)</div>";
				}
			}

			//WINES 
			processImport(
				$baseDir,
				1,
				'wines_import',
				'wines',
				" `id`, `name`, `brand`, `grape`, `type`, `country`, `region`, `subregion`, `amount`, `segment`, `wine_stopper`, `is_organic`, `other`, `sku`, `barcode`, `active`, `order` ",
				"F.`id` = I.`id`,
				F.`name` = I.`name`,
				F.`brand` = I.`brand`,
				F.`grape` = I.`grape`,
				F.`type` = I.`type`,
				F.`country` = I.`country`,
				F.`region` = I.`region`,
				F.`subregion` = I.`subregion`,
				F.`amount` = I.`amount`,
				F.`segment` = I.`segment`,
				F.`wine_stopper` = I.`wine_stopper`,
				F.`is_organic` = I.`is_organic`,
				F.`other` = I.`other`,
				F.`sku` = I.`sku`,
				F.`barcode` = I.`barcode`,
				F.`active` = I.`active`,
				F.`order` = I.`order`"
			);

			//OILS 
			processImport(
				$baseDir,
				2,
				'oils_import',
				'oils',
				" `id`, `name`, `brand`, `country`, `region`, `amount`, `segment`, `sku`, `barcode`, `active`, `order` ",
				"F.`id` = I.`id`,
				F.`name` = I.`name`,
				F.`brand` = I.`brand`,
				F.`country` = I.`country`,
				F.`region` = I.`region`,
				F.`amount` = I.`amount`,
				F.`segment` = I.`segment`,
				F.`sku` = I.`sku`,
				F.`barcode` = I.`barcode`,
				F.`active` = I.`active`,
				F.`order` = I.`order`"
			);

			//DISTILLATES
			processImport(
				$baseDir,
				3, // rowId for distillates
				'distillates_import',
				'distillates',
				" `id`, `name`, `brand`, `type`, `amount`, `segment`, `other`, `sku`, `barcode`, `active`, `order` ",
				"F.`id` = I.`id`,
				F.`name` = I.`name`,
				F.`brand` = I.`brand`,
				F.`type` = I.`type`,
				F.`amount` = I.`amount`,
				F.`segment` = I.`segment`,
				F.`other` = I.`other`,
				F.`sku` = I.`sku`,
				F.`barcode` = I.`barcode`,
				F.`active` = I.`active`,
				F.`order` = I.`order`"
			);
		}
?>
</div>
</body>
</html>