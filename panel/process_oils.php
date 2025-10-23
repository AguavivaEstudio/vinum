<?php
ob_start();
include '_includes.php';
checkSecurity();

$tableSelected = "20";

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

				$sSql = "SELECT `id`, `fileName` FROM `sys_files` WHERE `tableName` = 'wine_files' AND `columnName` = 'file_clientes' AND `publish` = 1;";
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
						INSERT INTO `publicaciones` (`titulo`, `editorial`, `precio`, `datosedic`,`autor`, `autor2`, `autor3`,`autor4`,`autor5`,`isbn`,`ano`,`especialidad`, `codigo`, `stock`, `activo`)
							SELECT CI.`titulo`, 1, CI.`precio`, NULL, 1, 1, 1, 1, 1, NULL, NULL, 1, CI.`codigo`, 0, 0
						  	FROM `oils_import` AS CI
						 	WHERE CI.`codigo` NOT IN (SELECT `codigo` FROM `publicaciones` WHERE `codigo` IS NOT NULL)
						 	GROUP BY CI.`codigo`, CI.`precio`
						;";
					ExecuteSql($sSql, null);

					$sSql = "
							UPDATE `publicaciones` AS CL
							 INNER JOIN `oils_import` AS CI ON CL.`codigo` = CI.`codigo`
							   SET CL.`precio` = CI.`precio`
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