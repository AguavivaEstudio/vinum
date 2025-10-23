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
if(isset($_REQUEST['tn'])) {
	$clientName = $_REQUEST['tn'];
} else {
	$clientName = "";
}

$permissions = getTablePermission($tableSelected);
if ($tableSelected == 18) {
	$tablenombre 	= 'blog_contenido';
	$tabletop 		= 'blog';
	$tablerow		= 'blog';
	$t 				= 'blog_contenido';
}


if (count($_POST) > 0){

	$arrParameters = array(null);
	if ($tableSelected == 18) {
		$columnNames 	= explode(',', 'name,blog,tipo,destacado,texto,video,url_txt,url_link,activo,orden');
	}

	if ($_POST['id'] == '0'){
		if ($permissions['add'] == 1){
			$sSql = "INSERT INTO `".$tablenombre."` (";
			$sSqlAux = "";

			foreach ($columnNames as $key => $value) {
				$colName = $columnNames[$key];
				if (substr($colName,0,4) == 'rel_') {
					$colValue = "";
					foreach ($_POST[$colName] as $valor) {
						$colValue .= $valor.',';
					}
					$colValue = rtrim($colValue, ", ");
					if ($colValue == "") {
						$colValue = '1'; 
					}
				} else {
					if (isset($_POST[$colName])) { $colValue = $_POST[$colName]; } else { $colValue = '0'; }
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
			$sSql = "UPDATE `".$tablenombre."` SET ";

			foreach ($columnNames as $key => $value) {
				$colName = $columnNames[$key];
				if (substr($colName,0,4) == 'rel_') {
					$colValue = "";
					foreach ($_POST[$colName] as $valor) {
						$colValue .= $valor.',';
					}
					$colValue = rtrim($colValue, ", ");
					if ($colValue == "") {
						$colValue = '1'; 
					}
				} else {
					if (isset($_POST[$colName])) { $colValue = $_POST[$colName]; } else { $colValue = '0'; }
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
	header("Location: content.php?t=$tableSelected&id=" . $_REQUEST[$tablerow]."&cliente=$clientName");
}

if(isset($_REQUEST['p'])) {
	$produccion = $_REQUEST['p'];
} else {
	$tableSelected = "";
}

if($_REQUEST['id']!=0) {
	$id = $_REQUEST['id'];
	$titleNewUpdate = ' Update ';
} else {
	$id = 0;
	$titleNewUpdate = ' New ';
}

$sSql = "SELECT `group`, `menuText`, `table` FROM `sys_tables` WHERE `id` = ?;";
$result = ExecuteSql($sSql, array(null, $tableSelected));
$row = $result -> fetch_array(MYSQLI_ASSOC);
$group    = $row['group'];
$menuText = $row['menuText'];
$table    = $row['table'];

$arrSql = CreateSqlQuery('contenido', false);


?>
<body>
    <div class='container-fluid'>
    <?php
    createMenu();
    echo "<h1 id='divError' class='error'></h1>";

	if (($permissions['view'] != 1) || (is_null($table))){
		echo "<h1 class='error'>" . getLangVar('AuthorizationDenied') . "</hi>";
	} else {
		echo "<div class='divMenuOption'>
		<h4 class='colorGrey_2'>
			<a href='admin.php'>Home</a>
			<i class='fa fa-angle-right' aria-hidden='true'></i> <a href='admin.php?t=$tableSelected'>".ucfirst($tabletop)."</a>
			<i class='fa fa-angle-right' aria-hidden='true'></i> Detalle
			<i class='fa fa-angle-right' aria-hidden='true'></i> $clientName
			<i class='fa fa-angle-right' aria-hidden='true'></i> <span class='colorOrange_1'>  $titleNewUpdate</span>
		</h4>";

		echo "</div>";
		if ($permissions['add'] || $permissions['update']){
			echo "<form name='frmAddUpdate' method='POST' action='contentDetail.php?t=$tableSelected&tn=$clientName'>";
			echo "<input type='hidden' name='id' value='$id'>";
			echo "<input type='hidden' name='nivel' value='$clientName'>";
			echo "<input type='hidden' name='$tablerow' value='$produccion'>";
		}

		if ($id != 0){
			$sSql = "SELECT * FROM `".$tablenombre."` WHERE `id` = ?;";
			$result = ExecuteSql($sSql, array(null, $id));
			$row = $result -> fetch_array(MYSQLI_ASSOC);

			$name = $row['name'];
			$tipo = $row['tipo'];
			$destacado = $row['destacado'];
			$texto = $row['texto'];
			$video = $row['video'];
			$url_txt = $row['url_txt'];
			$url_link = $row['url_link'];
			$activo = $row['activo'];
			$orden = $row['orden'];
			$classHidden = '';
		} else {
			$name 	= '';
			$tipo 	= '';
			$icono 	= '';
			$destacado 	= '';
			$texto 	= '';
			$video 	= '';
			$url_txt = '';
			$url_link = '';
			$activo = '';
			$orden 	= '';

			$classHidden = 'hidden';
		}

		echo "

		<div class='form-group'>
			<div class='row'>
				<div class='col-xs-12 col-sm-3 col-sm-offset-2'> <label for='tipo'>Tipo</label> </div>
				<div class='col-xs-12 col-sm-5'>
				" . CreateDropDownForTable('contenido_tipo', 'tipo', 'tipo', $tipo) . "
				</div>
				<div class='hidden-xs col-sm-2'>
				</div>
			</div>
		</div>

		<div class='form-group'>
			<div class='row'>
				<div class='col-xs-12 col-sm-3 col-sm-offset-2'> <label for='activo'>Publicado</label> </div>
				<div class='col-xs-12 col-sm-5'>
				" . CreateCheckBox($tablenombre, 'activo', $id, $activo, '', '') . "
				</div>
				<div class='hidden-xs col-sm-2'>
				</div>
			</div>
		</div>

		<div class='form-group'>
			<div class='row'>
				<div class='col-xs-12 col-sm-3 col-sm-offset-2'> <label for='orden'>Órden</label> </div>
				<div class='col-xs-12 col-sm-5'>
				<input class='form-control' name='orden' id='orden' value='$orden' maxlength='10' required placeholder='Órden' type='number' />
				</div>
				<div class='hidden-xs col-sm-2'>
				</div>
			</div>
		</div>

		<div class='form-group'>
			<div class='row'>
				<div class='col-xs-12 col-sm-3 col-sm-offset-2'> <label for='name'>Titulo</label> </div>
				<div class='col-xs-12 col-sm-5'>
				<input class='form-control' name='name' id='name' value='$name' maxlength='60' required placeholder='Titulo' type='text' />
				</div>
				<div class='hidden-xs col-sm-2'>
				</div>
			</div>
		</div>

		<div class='form-group tipoDestacado $classHidden'>
			<div class='row'>
				<div class='col-xs-12 col-sm-3 col-sm-offset-2'> <label for='destacado'>destacado</label> </div>
				<div class='col-xs-12 col-sm-5'><input class='form-control' name='destacado' id='destacado' value='$destacado' maxlength='200' placeholder='Frase aquí'></div>
				<div class='hidden-xs col-sm-2'>
				</div>
			</div>
		</div>

		<div class='form-group tipoTxt $classHidden'>
			<div class='row'>
				<div class='col-xs-12 col-sm-3 col-sm-offset-2'> <label for='texto'>Texto</label> </div>
				<div class='col-xs-12 col-sm-5'><textarea class='form-control' rows='4' cols='50' name='texto' id='texto'>$texto</textarea></div>
				<div class='hidden-xs col-sm-2'>
				</div>
			</div>
		</div>

		<div class='form-group tipoVideo $classHidden'>
			<div class='row'>
				<div class='col-xs-12 col-sm-3 col-sm-offset-2'> <label for='video'>Video</label> </div>
				<div class='col-xs-12 col-sm-5'>
				<input class='form-control' name='video' id='video' value='$video' maxlength='60' placeholder='Video' type='text' />
				</div>
				<div class='hidden-xs col-sm-2'>
				</div>
			</div>
		</div>

		<div class='form-group tipoLink $classHidden'>
			<div class='row'>
				<div class='col-xs-12 col-sm-3 col-sm-offset-2'> <label for='dato'>Columna 1</label> </div>
				<div class='col-xs-12 col-sm-5'>
				<input class='form-control' name='url_txt' id='url_txt' value='$url_txt' maxlength='60' placeholder='URL Texto' type='text' /></div>
				<div class='hidden-xs col-sm-2'>
				</div>
			</div>
		</div>

		<div class='form-group tipoLink $classHidden'>
			<div class='row'>
				<div class='col-xs-12 col-sm-3 col-sm-offset-2'> <label for='dato'>Columna 1</label> </div>
				<div class='col-xs-12 col-sm-5'>
				<input class='form-control' name='url_link' id='url_link' value='$url_link' maxlength='60' placeholder='URL Link' type='text' /></div>
				<div class='hidden-xs col-sm-2'>
				</div>
			</div>
		</div>
		";

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
			echo "</form>";
		}
	}
    ?>
    </div>
</body>
</html>