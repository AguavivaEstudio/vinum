<?php
ob_start();
include '_includes.php';
checkSecurity();

CreateHeadder();

if(isset($_REQUEST['t'])) {
	$tableSelected = $_REQUEST['t'];
} else {
	$tableSelected = "";
}

$permissions = getTablePermission($tableSelected);

if(isset($_REQUEST['id'])) {
	$id = $_REQUEST['id'];
	$titleNewUpdate = ' Update ';
} else {
	$id = "";
	$titleNewUpdate = ' New ';
}

$sSql = "SELECT `group`, `menuText`, `table` FROM `sys_tables` WHERE `id` = ?;";
$result = ExecuteSql($sSql, array(null, $tableSelected));
$row = $result -> fetch_array(MYSQLI_ASSOC);
$group    = $row['group'];
$menuText = $row['menuText'];
$table    = $row['table'];

function toSlug($string, $id = null)
{
	if (!is_string($string) || trim($string) === '') {
		return '';
	}

	$string = mb_strtolower($string, 'UTF-8');

	if (class_exists('Normalizer')) {
		$string = Normalizer::normalize($string, Normalizer::FORM_D);
		$string = preg_replace('/\p{Mn}/u', '', $string);
	} else {
		$accents = [
			'á' => 'a', 'é' => 'e', 'í' => 'i',
			'ó' => 'o', 'ú' => 'u', 'ñ' => 'n',
			'ü' => 'u'
		];
		$string = strtr($string, $accents);
	}

	$string = preg_replace('/[^a-z0-9]+/', '-', $string);
	$string = trim($string, '-');

	return $id !== null ? $string . '-' . $id : $string;
}

if (count($_POST) > 0){
	$arrParameters = array(null);
	$columnNames = $_POST['columnNames'];
	$columnNames = explode(',', $columnNames);

	if ($_POST['id'] == ''){
		if ($permissions['add'] == 1){
			$sSql = "INSERT INTO `$table` (";
			$sSqlAux = "";


			foreach ($columnNames as $key => $value) {
				$colName = $columnNames[$key];
				if (substr($colName,0,4) == 'rel_') {
					$colValue = "";
					foreach ($_POST[$colName] as $valor) {
						$colValue .= $valor.',';
					}
					$colValue = rtrim($colValue, ", ");
				} else {
					if (isset($_POST[$colName])) { 
						if ($_POST[$colName] != 'NULL') {
							$colValue = $_POST[$colName]; 
							// Add automatic slug from name if column is empty
							if ($colName == 'slug') {
								if (!isset($_POST['slug']) || $_POST['slug'] == '') {
									$titleForSlug = !empty($_POST['name']) ? $_POST['name'] : 'form-' . $_POST['id'];
									$colValue = toSlug($titleForSlug);
								} else {
									$colValue = toSlug($_POST['slug']);
								}
							}
						} else {
							$colValue = NULL;
						}
					} else {
						$colValue = '0'; 
					}
				}

				$sSql = $sSql . "`" . $colName . "`, ";
				$sSqlAux = $sSqlAux . "?, ";

				$arrParameters[] = $colValue;
			}
	
			$sSql    = substr($sSql,    0, -2);
			$sSqlAux = substr($sSqlAux, 0, -2);
			$sSql    = $sSql . ") VALUES ($sSqlAux);";
			$result = ExecuteSql($sSql, $arrParameters);
		}
	} else {
		if ($permissions['update'] == 1){
			$sSql = "UPDATE `$table` SET ";

			foreach ($columnNames as $key => $value) {
				$colName = $columnNames[$key];
				if (isset($_POST[$colName])) { 
					if ($_POST[$colName] != 'NULL') {
						if (substr($colName,0,4) == 'rel_') {
							$colValue = "";
							foreach ($_POST[$colName] as $valor) {
								$colValue .= $valor.',';
							}
							$colValue = rtrim($colValue, ", ");
						} else {
							if (isset($_POST[$colName])) { 
								if ($_POST[$colName] != 'NULL') {
									$colValue = $_POST[$colName]; 
									// Add automatic slug from name if column is empty
									if ($colName == 'slug') {
										if (!isset($_POST['slug']) || $_POST['slug'] == '') {
											$titleForSlug = !empty($_POST['name']) ? $_POST['name'] : 'form-' . $_POST['id'];
											$colValue = toSlug($titleForSlug);
										} else {
											$colValue = toSlug($_POST['slug']);
										}
									}
								} else {
									$colValue = NULL;
								}
							} else {
								$colValue = '0'; 
							}
						}
					} else {
						$colValue = NULL;
					}
				} else {
					$colValue = '0'; 
				}
				$sSql = "$sSql `$colName` = ?, ";
				$arrParameters[] = $colValue;
			}

			$sSql    = substr($sSql,    0, -2);
			$sSql    = $sSql . " WHERE `id` = ?;";
			$arrParameters[] = $_POST['id'];

			$result = ExecuteSql($sSql, $arrParameters);
		}
	}
	header("Location: admin.php?t=$tableSelected");
}

?>
<body>
    <div class='container-fluid'>
    <?php
    createMenu();
    echo "<h1 id='divError' class='error'></h1>";

	if (($permissions['view'] != 1) || (is_null($table))){
		echo "<h1 class='error'>" . getLangVar('AuthorizationDenied') . "</hi>";
	} else {
		echo "<div class='divMenuOption'>";
    	echo "<h4 class='colorGrey_2'>";
    	echo "	<a href='admin.php'>Home</a> <i class='fa fa-angle-right' aria-hidden='true'></i>";
    	echo "	$group <i class='fa fa-angle-right' aria-hidden='true'></i> 
    			<a href='admin.php?t=$tableSelected'>$menuText</a> <span class='colorOrange_1'><i class='fa fa-angle-right' aria-hidden='true'></i> $titleNewUpdate</span>";
    	echo "</h4>";
    	echo "</div>";
		if ($permissions['add'] || $permissions['update']){
			echo "<form name='frmAddUpdate' method='POST' action='add.php'>";
			echo "<input type='hidden' name='t' value='$tableSelected'>";
		}
		$arrSql = CreateSqlQuery($table, false);

		$sSql = "SELECT * FROM $table WHERE `id` = ?";
		$result = ExecuteSql($sSql, array(null, $id));
		$row = $result -> fetch_array(MYSQLI_ASSOC);

		$columnNames = "";
		foreach ($arrSql[0] as $value){

			$columnName = (is_null($value['COLUMN_COMMENT']) || ($value['COLUMN_COMMENT'] == '') ? $value['COLUMN_NAME'] : $value['COLUMN_COMMENT']);
			if ($value['COLUMN_NAME'] == 'id'){
				echo createControl($value, $row[$value['COLUMN_NAME']]);
			} else if (substr($value['COLUMN_NAME'],0,5) == 'file_'){
			} else if (substr($value['COLUMN_NAME'],0,4) == 'rel_'){
				$columnNames = $columnNames . $value['COLUMN_NAME'] . ",";
				echo "
					<div class='form-group'>
						<div class='row'>
							<div class='col-xs-12 col-sm-3 col-sm-offset-2'>
								<label for='" . $value['COLUMN_NAME'] . "'>$columnName</label>
							</div>
							<div class='col-xs-12 col-sm-5'>
								" .
								createList($value, $row[$value['COLUMN_NAME']], $id)
								. "
							</div>
							<div class='hidden-xs col-sm-2'>
							</div>
						</div>
					</div>
					";
			} else {
				$columnNames = $columnNames . $value['COLUMN_NAME'] . ",";
				echo "
					<div class='form-group'>
						<div class='row'>
							<div class='col-xs-12 col-sm-3 col-sm-offset-2'>
								<label for='" . $value['COLUMN_NAME'] . "'>$columnName</label>
							</div>
							<div class='col-xs-12 col-sm-5'>
								" .
								createControl($value, $row[$value['COLUMN_NAME']])
								. "
							</div>
							<div class='hidden-xs col-sm-2'>
							</div>
						</div>
					</div>
					";
			}
		}
		echo "
			<div class='form-group'>
				<div class='row'>
					<div class='col-xs-12 col-sm-3 col-sm-offset-2'>
					</div>
					<div class='col-xs-12 col-sm-7'>
			";
			if ($permissions['add'] || $permissions['update']){
				echo "<button type='submit' class='btn btn-success2'>" . getLangVar('Save') . "</button>";
			}
			echo "
						<button type='button' class='btn btn-default2' id='btnCancellForm'>" . getLangVar('Cancel') . "</button>
					</div>
				</div>
			</div>
			";
		if ($permissions['add'] || $permissions['update']){
			$columnNames = substr($columnNames, 0, -1);
			echo "<input type='hidden' name='columnNames' value='$columnNames' >";
			echo "</form>";
		}
	}
    ?>
    </div>
</body>
</html>