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

				$sSql = "SELECT `id`, `fileName` FROM `sys_files` WHERE `tableName` = 'data_files' AND `columnName` = 'file_clientes' AND `publish` = 1 AND `rowId` = 2;";
				$result = ExecuteSql($sSql, null);
				$sSql = "TRUNCATE TABLE `oils_import`;";
				ExecuteSql($sSql, null);
				$archivosProcesados = 0;
				while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
					$id = $row['id'];
					$fileName = $row['fileName'];

					$rowsAffected = importFile($baseDir . $fileName, 'oils_import');

					$sSql = "UPDATE `sys_files` SET `publish` = 0, `comment` = concat('Procesado: ', now()) WHERE `id` = $id;";
					ExecuteSql($sSql, null);

					echo "<div>Archivo procesado: $fileName - Registros: $rowsAffected</div><br><br>$baseDir$fileName";
					$archivosProcesados++;
				}

				if ($archivosProcesados > 0) {

					$sSql = "
						INSERT INTO `oils` ( `id`, `name`, `brand`, `country`, `region`, `amount`, `segment`, `sku`, `barcode`, `active`, `order` )
							SELECT OI.`id`, OI.`name`, OI.`brand`, OI.`country`, OI.`region`, OI.`amount`, OI.`segment`, OI.`sku`, OI.`barcode`, OI.`active`, OI.`order`
						  	FROM `oils_import` AS OI
						 	WHERE OI.`sku` NOT IN (SELECT `sku` FROM `oils` WHERE `sku` IS NOT NULL)
						 	GROUP BY OI.`sku`
						;";
					ExecuteSql($sSql, null);

					$sSql = "
							UPDATE `oils` AS CL
							 	INNER JOIN `oils_import` AS OI ON CL.`sku` = OI.`sku`
							   	SET CL.`id` = OI.`id`,
									CL.`name` = OI.`name`,
									CL.`brand` = OI.`brand`,
									CL.`country` = OI.`country`,
									CL.`region` = OI.`region`,
									CL.`amount` = OI.`amount`,
									CL.`segment` = OI.`segment`,
									CL.`sku` = OI.`sku`,
									CL.`barcode` = OI.`barcode`,
									CL.`active` = OI.`active`,
									CL.`order` = OI.`order`
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