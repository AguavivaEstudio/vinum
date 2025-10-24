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

				$sSql = "SELECT `id`, `fileName` FROM `sys_files` WHERE `tableName` = 'data_files' AND `columnName` = 'file_clientes' AND `publish` = 1 AND `rowId` = 3;";
				$result = ExecuteSql($sSql, null);
				$sSql = "TRUNCATE TABLE `distillates_import`;";
				ExecuteSql($sSql, null);
				$archivosProcesados = 0;
				while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
					$id = $row['id'];
					$fileName = $row['fileName'];

					$rowsAffected = importFile($baseDir . $fileName, 'distillates_import');

					// if active is empty, insert it with 1
					ExecuteSql("UPDATE `distillates_import` SET `active` = 1 WHERE `active` IS NULL;", null);


					$sSql = "UPDATE `sys_files` SET `publish` = 0, `comment` = concat('Procesado: ', now()) WHERE `id` = $id;";
					ExecuteSql($sSql, null);

					echo "<div>Archivo procesado: $fileName - Registros: $rowsAffected</div><br><br>$baseDir$fileName";
					$archivosProcesados++;
				}

				if ($archivosProcesados > 0) {

					$sSql = "
						INSERT INTO `distillates` ( `id`, `name`, `brand`, `type`, `amount`, `segment`, `other`, `sku`, `barcode`, `active`, `order` )
							SELECT DI.`id`, DI.`name`, DI.`brand`, DI.`type`, DI.`amount`, DI.`segment`, DI.`other`, DI.`sku`, DI.`barcode`, DI.`active`, DI.`order`
						  	FROM `distillates_import` AS DI
						 	WHERE DI.`sku` NOT IN (SELECT `sku` FROM `distillates` WHERE `sku` IS NOT NULL)
						 	GROUP BY DI.`sku`
						;";
					ExecuteSql($sSql, null);

					$sSql = "
							UPDATE `distillates` AS CL
							 	INNER JOIN `distillates_import` AS DI ON CL.`sku` = DI.`sku`
							   	SET CL.`id` = DI.`id`,
									CL.`name` = DI.`name`,
									CL.`brand` = DI.`brand`,
									CL.`type` = DI.`type`,
									CL.`amount` = DI.`amount`,
									CL.`segment` = DI.`segment`,
									CL.`other` = DI.`other`,
									CL.`sku` = DI.`sku`,
									CL.`barcode` = DI.`barcode`,
									CL.`active` = DI.`active`,
									CL.`order` = DI.`order`
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