<?php
require 'includes/funcionesdb.php';
require 'includes/resize-class.php';
require 'includes/mailer/mailer.php';
// require '_includesCustom.php';

require '__log.php';

function checkSecurity()
{
	if (!(isset($_SESSION['USER_id']) && isset($_SESSION['USER_email']) && isset($_SESSION['USER_sysadmin'])))
		header('Location: signOut.php');
}

function getTablePermission($table)
{
	if ($_SESSION['USER_sysadmin'] == 1) {
		$view   = true;
		$add    = true;
		$update = true;
		$delete = true;
	} else {
		$view   = false;
		$add    = false;
		$update = false;
		$delete = false;
		for ($i = 0; $i < count($_SESSION['USER_permission']); $i++) {
			if (($_SESSION['USER_permission'][$i]['table']      == $table)
				|| ($_SESSION['USER_permission'][$i]['sys_tables'] == $table)
			) {
				$view   = ($_SESSION['USER_permission'][$i]['view']   || $_SESSION['USER_permission'][$i]['full']);
				$add    = ($_SESSION['USER_permission'][$i]['add']    || $_SESSION['USER_permission'][$i]['full']);
				$update = ($_SESSION['USER_permission'][$i]['update'] || $_SESSION['USER_permission'][$i]['full']);
				$delete = ($_SESSION['USER_permission'][$i]['remove'] || $_SESSION['USER_permission'][$i]['full']);
			}
		}
	}
	$permissions = array('view' => $view, 'add' => $add, 'update' => $update, 'delete' => $delete);
	return $permissions;
}

function CreateHeadder()
{
	$PROJECT_NAME		= $_SESSION['sysConfig']['ProjectName'];
	$DEFAULT_LANGUAGE	= $_SESSION['sysConfig']['DefaultLanguage'];
	if (false && $GLOBALS["APP_LocalHost"]) {
		$vueFileJS = "<script src='https://cdn.jsdelivr.net/npm/vue/dist/vue.js'></script>";
	} else {
		$vueFileJS = "<script src='https://cdn.jsdelivr.net/npm/vue'></script>";
	}


	echo "
		<!DOCTYPE html>
		<html lang='$DEFAULT_LANGUAGE'>
		<head>
			<meta charset='utf-8'>
			<meta name='author' content='emiber'>
			<meta http-equiv='cleartype' content='on'>
			<meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'>
			<meta name='viewport' content='width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=yes'/>

			<link rel='shortcut icon' href='img/favicon.ico'>
			<link rel='apple-touch-icon' href='img/apple-touch-icon.png'>

			<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js'></script>
			<script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js'></script>
			<script type='text/javascript' src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js' integrity='sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa' crossorigin='anonymous'></script>
			<script type='text/javascript' src='https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js'></script>
			<script type='text/javascript' src='https://cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js'></script>
			<!--script type='text/javascript' src='https://dme0ih8comzn4.cloudfront.net/imaging/v3/editor.js'></script-->
			$vueFileJS
			<link type='text/css' rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'			integrity='sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u' crossorigin='anonymous' />
			<link type='text/css' rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css'	integrity='sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp' crossorigin='anonymous' />
			<link type='text/css' rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css' />
			<link type='text/css' rel='stylesheet' href='https://cdn.datatables.net/responsive/2.1.0/css/responsive.dataTables.min.css' />

			<script type='text/javascript' src='third-party/ckeditor/ckeditor.js'></script>
			<script type='text/javascript' src='third-party/ckeditor/adapters/jquery.js'></script>

			<script type='text/javascript' src='third-party/dropzone/dropzone.min.js'></script>
			<link type='text/css' rel='stylesheet' href='third-party/dropzone/dropzone.min.custom.css' />

			<script type='text/javascript' src='third-party/sortble/jquery-sortable-min.js'></script>

			<script type='text/javascript' src='js/custom.js'></script>
			<link rel='stylesheet' type='text/css' href='css/global.css' media='all'>
			<link rel='stylesheet' type='text/css' href='css/newcustom.css' media='all'>

			<title>$PROJECT_NAME</title>
		</head>
	";
}

function createMenu()
{
	global $tableSelected;
	$PROJECT_NAME		= strtoupper($_SESSION['sysConfig']['ProjectName']);
	$DEFAULT_LANGUAGE	= $_SESSION['sysConfig']['DefaultLanguage'];

	if ($_SESSION['USER_sysadmin'] == 1) {
		$sSql = "SELECT `id`, `group`, `table`, `menuText`, `link`, `icon` FROM `sys_tables` ORDER BY `order_1`, `order_2`";
		$arrParam = null;
	} else {
		$sSql = "
		SELECT S_T.`id`, S_T.`group`, S_T.`table`, S_T.`menuText`, S_T.`link`, `icon`
		  FROM `sys_tables` AS S_T
		  LEFT JOIN `vwuserspermissions` AS U_P ON S_T.`id` = U_P.`sys_tables`
		 WHERE (U_P.`view` = 1 OR U_P.`full` = 1)
		   AND U_P.`email` = ?
		 ORDER BY S_T.`order_1`, S_T.`order_2`;
		";

		$arrParam = array(null, $_SESSION['USER_email']);
	}

	$result = ExecuteSql($sSql, $arrParam);


	echo "
	<div id='divMenu'>
	<nav class='navbar navbar-default'>
		<div class='container-fluid'>
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class='navbar-header'>
				<button type='button' class='navbar-toggle collapsed' data-toggle='collapse' data-target='#mainMenu' aria-expanded='false'>
					<span class='sr-only'>Toggle navigation</span>
					<span class='icon-bar'></span>
					<span class='icon-bar'></span>
					<span class='icon-bar'></span>
				</button>
				<a class='navbar-brand' href='admin.php'>
					<h3>
						$PROJECT_NAME<br class='hidden-xs'>
						<small class='hidden-xs'>" . strtoupper(getLangVar('PanelTitle')) . "</small>
					</h3>
				</a>
			</div>
			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class='collapse navbar-collapse' id='mainMenu'>
				<ul class='nav navbar-nav'>
					";

	$prevGroup = "";
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$id       = $row["id"];
		$group    = $row["group"];
		$table    = $row["table"];
		$menuText = $row["menuText"];
		$link     = $row["link"];
		$icon     = $row["icon"];

		$active   = "";

		if (is_null($icon))
			$icon = 'question';

		if ($prevGroup != $group) {
			if ($prevGroup != "") {
				echo "
						</ul>
					</li>
				";
			}
			echo "
				<li class='dropdown'>
					<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>
						$group
						<div class='hidden-xs'></div>
						<i class='fa fa-angle-down' aria-hidden='true'></i>
					</a>
					<ul class='dropdown-menu'>
			";
			$prevGroup = $group;
		}
		if (is_null($link) or ($link == ''))
			$link = "admin.php?t=$id";

		if ($tableSelected == $id)
			$active = "active";

		echo "<li class='$active'><a href='$link'><i class='fa fa-$icon' aria-hidden='true'></i> $menuText</a></li>
		";
	}

	echo "
						</ul>
					</li>
				</ul>

				<ul class='nav navbar-nav navbar-right'>
					<li>
						<a href='#' data-toggle='modal' data-target='#modalChangePass' title='" . getLangVar('ChangePassword') . "'>
							<i class='fa fa-lock' aria-hidden='true'></i>
						</a>
					</li>
					<li>
						<a href='signOut.php' title='Sign Out'>
							<i class='fa fa-power-off' aria-hidden='true'></i>
						</a>
					</li>
				</ul>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>
	</div><!-- /menu -->
	";
}

function CreateSqlQuery($tableName, $list)
{
	global $_server;
	$dbName = $_server['DB_NAME'];

	$sSql = "
			SELECT T1.*, T2.REFERENCED_TABLE_NAME,T2.REFERENCED_COLUMN_NAME
			  FROM information_schema.TABLES T
			  LEFT JOIN (SELECT TABLE_NAME, COLUMN_NAME, IS_NULLABLE, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, ORDINAL_POSITION, COLUMN_TYPE, COLUMN_COMMENT
			               FROM information_schema.COLUMNS
			              WHERE TABLE_SCHEMA = '$dbName'
            ";
	if ($list)
		$sSql = $sSql . " AND (CHARACTER_MAXIMUM_LENGTH < 400 or CHARACTER_MAXIMUM_LENGTH is null) ";
	$sSql = $sSql . "
			            ) T1 ON T.TABLE_NAME = T1.TABLE_NAME
			  LEFT OUTER JOIN
			            (SELECT TABLE_NAME, COLUMN_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
			               FROM information_schema.KEY_COLUMN_USAGE
			              WHERE TABLE_SCHEMA = '$dbName'
			                AND REFERENCED_COLUMN_NAME is not NULL
			            ) T2
			            ON T1.TABLE_NAME = T2.TABLE_NAME AND T1.COLUMN_NAME = T2.COLUMN_NAME
			 WHERE T.TABLE_SCHEMA = '$dbName'
			   AND T.TABLE_TYPE = 'BASE TABLE'
			 ORDER BY T1.TABLE_NAME, ORDINAL_POSITION;
	";
	$result = ExecuteSql($sSql, null);

	$json = array();
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$json[] = $row;
	}

	$jsonReturn = array();
	$tableId    = 1;
	$sSelect    = "SELECT ";
	$sFrom      = " FROM `$tableName` AS `T_1` ";
	foreach ($json as $value) {
		if ($value['TABLE_NAME'] == $tableName) {
			$jsonReturn[] = $value;
			if (is_null($value['REFERENCED_TABLE_NAME'])) {
				$sSelect = $sSelect . "`T_1`.`" . $value['COLUMN_NAME'] . "`, ";
			} else {
				$rowId = 0;
				foreach ($json as $valueRef) {
					if ($valueRef['TABLE_NAME'] == $value['REFERENCED_TABLE_NAME']) {
						$rowId++;
						if ($rowId == 2) {
							$tableId++;
							$sSelect = $sSelect . "`T_" . $tableId . "`.`" . $valueRef['COLUMN_NAME'] . "` AS `" . $value['COLUMN_NAME'] . "`, ";
							$sFrom = $sFrom . " LEFT JOIN `" . $valueRef['TABLE_NAME'] . "` AS `T_" . $tableId . "` ON `T_1`.`" . $value['COLUMN_NAME'] . "` = `T_" . $tableId . "`.`" . $value['REFERENCED_COLUMN_NAME'] . "`";
						}
					}
				}
			}
		}
	}
	$sSelect = substr($sSelect, 0, -2) . $sFrom;
	return (array($jsonReturn, $sSelect));
}


function CreateCheckBox($TABLE_NAME, $COLUMN_NAME, $rowId, $value, $disabled, $cssClass)
{
	$id = "chk_$TABLE_NAME-$COLUMN_NAME-$rowId";

	if ($value == 0) {
		$checked = "";
	} else {
		$checked = "checked='checked'";
	}
	$sHtmlReturn = "<span class='hidden'>$value</span>";
	if ($disabled == 'disabled') {
		$sHtmlReturn .= "<input type='checkbox' $checked disabled id='$id' />";
	} else {
		$sHtmlReturn .= "<input type='checkbox' name='$COLUMN_NAME' data-TABLE_NAME='$TABLE_NAME' data-COLUMN_NAME='$COLUMN_NAME' data-rowId='$rowId' value='1' $checked class='$cssClass' id='$id' />";
	}

	$sHtmlReturn .= "<label for='$id'></label>";

	return $sHtmlReturn;
}

function createControl($arrControlData, $sControlValue)
{
	$TABLE_NAME					= $arrControlData['TABLE_NAME'];
	$COLUMN_NAME 				= $arrControlData['COLUMN_NAME'];
	$IS_NULLABLE 				= $arrControlData['IS_NULLABLE'];
	$DATA_TYPE 					= $arrControlData['DATA_TYPE'];
	$CHARACTER_MAXIMUM_LENGTH 	= $arrControlData['CHARACTER_MAXIMUM_LENGTH'];
	$ORDINAL_POSITION 			= $arrControlData['ORDINAL_POSITION'];
	$COLUMN_TYPE 				= $arrControlData['COLUMN_TYPE'];
	$COLUMN_COMMENT 			= $arrControlData['COLUMN_COMMENT'];
	$REFERENCED_TABLE_NAME 		= $arrControlData['REFERENCED_TABLE_NAME'];
	$REFERENCED_COLUMN_NAME 	= $arrControlData['REFERENCED_COLUMN_NAME'];
	$IS_NULLABLE 	= $IS_NULLABLE == 'NO' ? " required " : "";
	$permissions 	= getTablePermission($TABLE_NAME);
	$fieldTitle		= (is_null($COLUMN_COMMENT) || ($COLUMN_COMMENT == '') ? $COLUMN_NAME : $COLUMN_COMMENT);

	if ($permissions['add'] || $permissions['update']) {
		$readonly = "";
	} else {
		$readonly = " readonly ";
	}

	$sHtmlReturn 	= "";
	if ($REFERENCED_COLUMN_NAME != "") {
		$sHtmlReturn = CreateDropDownForTable($REFERENCED_TABLE_NAME, $COLUMN_NAME, $TABLE_NAME, $sControlValue);
	} else {
		if ($COLUMN_NAME == 'id') {
			$sHtmlReturn = "<input type='hidden' name='$COLUMN_NAME' id='$COLUMN_NAME' value='$sControlValue' />";
		} else if (substr($COLUMN_NAME, 0, 5) == 'file_') {
			$sHtmlReturn = '';
		} else if ($COLUMN_TYPE == 'tinyint(1)') {
			$sHtmlReturn = CreateCheckBox($TABLE_NAME, $COLUMN_NAME, '', $sControlValue, '', '');
		} elseif ($DATA_TYPE == 'date') {
			$sHtmlReturn = "<input class='form-control' name='$COLUMN_NAME' id='$COLUMN_NAME' value='$sControlValue' maxlength='$CHARACTER_MAXIMUM_LENGTH' $IS_NULLABLE $readonly placeholder='$fieldTitle' type='date' />";
		} elseif (($DATA_TYPE == 'datetime') || ($DATA_TYPE == 'timestamp')) {
			$sControlValue = str_replace(" ", "T", $sControlValue);
			$sHtmlReturn = "<input class='form-control' name='$COLUMN_NAME' id='$COLUMN_NAME' value='$sControlValue' maxlength='$CHARACTER_MAXIMUM_LENGTH' $IS_NULLABLE $readonly placeholder='$fieldTitle' type='datetime-local' />";
		} elseif ($DATA_TYPE == 'time') {
			$sHtmlReturn = "<input class='form-control' name='$COLUMN_NAME' id='$COLUMN_NAME' value='$sControlValue' maxlength='$CHARACTER_MAXIMUM_LENGTH' $IS_NULLABLE $readonly placeholder='$fieldTitle' type='time' />";
		} elseif ($DATA_TYPE == 'text') {
			$sHtmlReturn = "<textarea class='form-control' rows='4' cols='50' name='$COLUMN_NAME' id='$COLUMN_NAME' $IS_NULLABLE $readonly>$sControlValue</textarea>";
		} else {
			$sHtmlReturn = "<input class='form-control' name='$COLUMN_NAME' id='$COLUMN_NAME' value='$sControlValue' maxlength='$CHARACTER_MAXIMUM_LENGTH' $IS_NULLABLE $readonly placeholder='$fieldTitle' type='text' />";
		}
	}


	return $sHtmlReturn;
}
function createList($arrControlData, $sControlValue, $ides)
{
	$TABLE_NAME					= $arrControlData['TABLE_NAME'];
	$COLUMN_NAME 				= $arrControlData['COLUMN_NAME'];
	$IS_NULLABLE 				= $arrControlData['IS_NULLABLE'];
	$DATA_TYPE 					= $arrControlData['DATA_TYPE'];
	$CHARACTER_MAXIMUM_LENGTH 	= $arrControlData['CHARACTER_MAXIMUM_LENGTH'];
	$ORDINAL_POSITION 			= $arrControlData['ORDINAL_POSITION'];
	$COLUMN_TYPE 				= $arrControlData['COLUMN_TYPE'];
	$COLUMN_COMMENT 			= $arrControlData['COLUMN_COMMENT'];
	$REFERENCED_TABLE_NAME 		= $arrControlData['REFERENCED_TABLE_NAME'];
	$REFERENCED_COLUMN_NAME 	= $arrControlData['REFERENCED_COLUMN_NAME'];
	$IS_NULLABLE 	= $IS_NULLABLE == 'NO' ? " required " : "";
	$permissions 	= getTablePermission($TABLE_NAME);
	$fieldTitle		= (is_null($COLUMN_COMMENT) || ($COLUMN_COMMENT == '') ? $COLUMN_NAME : $COLUMN_COMMENT);

	if ($permissions['add'] || $permissions['update']) {
		$readonly = "";
	} else {
		$readonly = " readonly ";
	}

	$sHtmlReturn 	= "<div class='relList'>";
	if (substr($arrControlData['COLUMN_NAME'],0,4) == 'rel_') {
		$tableRel = explode('_',$arrControlData['COLUMN_NAME']);
		$array = explode(',', $sControlValue);
		$sSql   = "SELECT * FROM `".$tableRel[1]."` WHERE `activo` = 1 ORDER BY `id` ASC;";
		$result = ExecuteSql($sSql, null);
		while ($row = $result->fetch_array(MYSQLI_NUM)) {
			$Id     = $row[0];
			$text   = $row[1];
			$named	= $COLUMN_NAME."[]";
			$key = array_search($Id , $array);
			if (false !== $key) {
				$sSelected = "checked";
			} else {
				$sSelected = "";
			}
			if ($arrControlData['TABLE_NAME'] == $tableRel[1]) {
				if ($ides != $Id) {
					$sHtmlReturn = $sHtmlReturn . "
						<div class='itemRel'>
							<input type='checkbox' name='$named' value='$Id' $sSelected>&nbsp;<label for='$named' class='".$tableRel[1]."'> $text</label><br>
						</div>
					";	
				} 
			} else {
				$sHtmlReturn = $sHtmlReturn . "
				<div class='itemRel'>
					<input type='checkbox' name='$named' value='$Id' $sSelected>&nbsp;<label for='$named' class='".$tableRel[1]."'> $text</label><br>
				</div>";	
			
			}
		}
		if ($sHtmlReturn == '') {
			$sHtmlReturn = '<span>No hay opciones para elegir</span>';
		} else {
			$sHtmlReturn .= "</div>";
		}
	}


	return $sHtmlReturn;
}

function CreateDropDownForTable($sTable, $sName, $id, $selectedId)
{
	$sSql   = "SELECT * FROM $sTable ORDER BY 2 ASC;";

	$result = ExecuteSql($sSql, null);
	$sHTML  = "
		<select name='$sName' id='$id' class='form-control $sName'>
			<option value='null-value'>Nada Seleccionado</option>
	";
	$json = array();
	while ($row = $result->fetch_array(MYSQLI_NUM)) {
		$Id     = $row[0];
		$text   = $row[1];
		$sSelected = "";
		if ($Id == $selectedId)
			$sSelected = "selected='selected'";

		$sHTML = $sHTML . "<option value='$Id' $sSelected>$text</option>";
	}
	$sHTML = "$sHTML</select>";

	return $sHTML;
}

function CreateDropDownForTableCustom($sTable, $sName, $id, $selectedId, $datatab)
{
	$sSql   = "SELECT * FROM $sTable WHERE $datatab = 1  ORDER BY 2 ASC;";

	$result = ExecuteSql($sSql, null);
	$sHTML  = "
		<select name='$sName' id='$id' class='form-control $sName'>
			<option value='null-value'>Nada Seleccionado</option>
	";
	$json = array();
	while ($row = $result->fetch_array(MYSQLI_NUM)) {
		$Id     = $row[0];
		$text   = $row[1];
		$sSelected = "";
		if ($Id == $selectedId)
			$sSelected = "selected='selected'";

		$sHTML = $sHTML . "<option value='$Id' $sSelected>$text</option>";
	}
	$sHTML = "$sHTML</select>";

	return $sHTML;
}


function createDropZone()
{
	echo "
	<div class='modal fade' tabindex='-1' role='dialog' aria-labelledby='dropZoneTitleLabel' id='modalUpload'>
		<div class='modal-dialog modal-lg' role='document'>
			<div class='modal-content'>
				<div class='modal-header' id='dropZoneTitleLabel'>
					" . getLangVar('DropZoneTitle') . "
					<i class='fa fa-times' data-dismiss='modal' aria-label='Close'></i>
				</div>
				<div class='modal-body'>
					<div class='row'>
						<div class='col-xs-12 col-md-7'>
							<div>
								<form action='_upload.php' class='dropzoneCustom' id='dropzone'>
									<input type='hidden' name='tableName' >
									<input type='hidden' name='columnName' >
									<input type='hidden' name='rowId' >
									<div class='dz-message needsclick'>
									" . getLangVar('DropZoneBody') . "
									</div>
								</form>
							</div>
						</div>
						<div class='col-xs-12 col-md-5'>
							<div id='fileList'>
							</div>
						</div>
					</div>
				</div>				
			</div>
		</div>
	</div>
	";
}

function ChangePass()
{
	echo "
		<div class='modal fade' tabindex='-1' role='dialog' aria-labelledby='changePassTitleLabel' id='modalChangePass'>
			<div class='modal-dialog modal-sm' role='document'>
				<div class='modal-content'>
					<div class='modal-header' id='changePassTitleLabel'>
						" . getLangVar('ChangePassword') . "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
					</div>
					<form action='#' method='post' id='modalChangePassForm'>
						<input type='hidden' name='changePassword' >
						<div class='form-group'>
							<input type='password' class='form-control' name='password'  id='password'  placeholder='password' />
						</div>
						<div class='form-group'>
							<input type='password' class='form-control' name='password2' id='password2' placeholder='password' />
						</div>
						<div class='form-group text-right buttons'>
							<button type='button' class='btn btn-danger btn-sm' data-dismiss='modal'>Cancel</button>&nbsp;&nbsp;&nbsp;
							<button type='submit' class='btn btn-success btn-sm' id='modalChangePassSubmit'>Save</button>
						</div>
						<div class='alert alert-danger hidden' role='alert'
						data-text='" . getLangVar('PassNotMatch') . "' 
						data-text-empty='" . getLangVar('PassEmpty') . "'
						data-text-error='" . getLangVar('Error') . "'
						>" . getLangVar('PassNotMatch') . "</div>
						<div class='alert alert-success hidden' role='alert'>" . getLangVar('PassChanged') . "</div>
					</form>
				</div>
			</div>
		</div>
	";
}

function checkRowWidth()
{
	echo "
		<div class='row'>
			<div class='          col-xs-12' id='checkRowWidth_0'>xs</div>
			<div class='hidden-xs col-xs-12' id='checkRowWidth_1'>xs</div>
			<div class='hidden-sm col-xs-12' id='checkRowWidth_2'>sm</div>
			<div class='hidden-md col-xs-12' id='checkRowWidth_3'>md</div>
			<div class='hidden-lg col-xs-12' id='checkRowWidth_4'>lg</div>
		</div>
		<script>
			$( window ).resize(function() {
				$('#checkRowWidth_0').html($('#checkRowWidth_0').width());
			});
		</script>
		";
}
