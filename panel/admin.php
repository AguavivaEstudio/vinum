<?php
include '_includes.php';
checkSecurity();

CreateHeadder();

if(isset($_REQUEST['t'])) {
	$tableSelected = $_REQUEST['t'];
} else {
	$tableSelected = "";
}

?>
<body>
    <div class='container-fluid'>
    <?php
    createMenu();
    echo "<h1 id='divError' class='error'></h1>";
    if ($tableSelected != ""){
		$sSql = "SELECT `group`, `menuText`, `table`, `columns` FROM `sys_tables` WHERE `id` = ?;";
		$result = ExecuteSql($sSql, array(null, $tableSelected));
		$row = $result -> fetch_array(MYSQLI_ASSOC);
		$group    = $row['group'];
		$menuText = $row['menuText'];
		$table    = $row['table'];
		$columns  = $row['columns'];

		$permissions = getTablePermission($tableSelected);

		echo "<div class='divMenuOption'>";
		if (($permissions['view'] != 1) || (is_null($table))){
			echo "<h1 class='error'>" . getLangVar('AuthorizationDenied') . "</h1>";
		} else {
	    	echo "<h4 class='colorGrey_2'>";
	    	echo "	<a href='admin.php'>Home</a> <i class='fa fa-angle-right' aria-hidden='true'></i>";
	    	echo "	$group <span class='colorOrange_1'><i class='fa fa-angle-right' aria-hidden='true'></i> $menuText</span>";
	    	echo "</h4>";
	    	echo "<div id='tableAddDeleteBTN'>";
			if ($permissions['add']){
				echo "
					<button type='button' class='btn btn-success btn-sm newItem' data-table='$tableSelected'>
						<i class='fa fa-plus' aria-hidden='true'></i><span>" . getLangVar('Add') . "</span>
					</button>
				";
			}

			if ($permissions['delete']){
				echo "
					<button type='button' class='btn btn-danger btn-sm btnDeleteMultiple' data-table='$table' title=' - Delete Selected - '>
						<i class='fa fa-times' aria-hidden='true'></i><span>" . getLangVar('Delete') . "</span>
					</button>
				";
			}
			echo "</div>";
			echo "</div>";

			$arrSql = CreateSqlQuery($table, true);
			?>
			<table class='table table-bordered table-striped table-hover dt-responsive nowrap dataTable' data-table='<?php echo $table; ?>'>
				<thead>
					<tr>
						<th></th>
						<?php
						$i = 0;
						foreach ($arrSql[0] as $value){
							if (($value['ORDINAL_POSITION'] > 1) && (($value['COLUMN_TYPE'] == 'tinyint(1)') || (substr($value['COLUMN_NAME'], 0, 5) == 'file_') || ($i <= $columns) || ($columns == 0))){
								$columnName = (is_null($value['COLUMN_COMMENT']) || ($value['COLUMN_COMMENT'] == '') ? $value['COLUMN_NAME'] : $value['COLUMN_COMMENT']);
								echo "<th class='text-center'>" . $columnName . "</th>";
							}
							$i++;
						}
						if ($table == 'blogs' || $table == 'proyectos') {
							echo "<th class='text-center'>Contenido</th>";
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?php
					$result = ExecuteSql($arrSql[1], null);
					while ($row = $result -> fetch_array(MYSQLI_ASSOC)) {
						echo "<tr data-rowId='" . $row['id'] . "' >";
						echo "<td class='text-center tdTableControls'>";
						if ($permissions['delete']){
							echo "
								<input type='checkbox' class='check_box chkDeleteMultiple' data-id='" . $row['id'] . "' id='chk_$tableSelected-" . $row['id'] . "' />
								<label for='chk_$tableSelected-" . $row['id'] . "'></label>
								<i class='fa fa-window-close delete' aria-hidden='true' data-table='" . $value['TABLE_NAME'] . "' data-rowId='" . $row['id'] . "'></i>
							";
						}
						if ($permissions['view'] || $permissions['update']){
							echo "<a href='add.php?t=$tableSelected&id=" . $row['id'] . "'><i class='fa fa-pencil-square' aria-hidden='true'></i></a>";
						}
						echo "</td>";
						$i = 0;
						foreach ($arrSql[0] as $value){
							$TABLE_NAME  = $value['TABLE_NAME'];
							$COLUMN_NAME = $value['COLUMN_NAME'];
							if (($value['ORDINAL_POSITION'] > 1) && (($value['COLUMN_TYPE'] == 'tinyint(1)') || (substr($COLUMN_NAME, 0, 5) == 'file_') || ($i <= $columns) || ($columns == 0))){
								if ($value['COLUMN_TYPE'] == 'tinyint(1)') {
									$disabled = "disabled";
									if ($permissions['update'] == 1)
										$disabled = "";
										echo "<td class='text-center'>" . CreateCheckBox($TABLE_NAME, $COLUMN_NAME, $row['id'], $row[$COLUMN_NAME], $disabled, 'clsPublish') . "</td>";
								}
								elseif (substr($COLUMN_NAME, 0, 5) == 'file_') {
									echo "<td class='text-center'><i class='fa fa-upload' data-table='$TABLE_NAME' data-column='$COLUMN_NAME' data-id='" . $row['id'] . "' aria-hidden='true'></i></td>";

								}
								else {
									echo "<td>" . $row[$COLUMN_NAME] . "</td>";
								}
							}
							$i++;
						}
						if ($table == 'blogs' || $table == 'proyectos') {
							echo "<td class='text-center'><i class='fa fa-edit editCustom' data-table='$TABLE_NAME' data-tableId='$tableSelected' data-id='" . $row['id'] . "' data-cliente='" . $row['name'] . "' aria-hidden='true'></i></td>";
						}
						echo "</tr>";
					}
					?>
				</tbody>
			</table>
			<?php
		}
    } else {

    	echo "
			<h1 class='text-center'>" . getLangVar('PanelBodyTextL1') . "</h1>
			<h2 class='text-center'>" . getLangVar('PanelBodyTextL2') . "</h2>
    	";

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

		echo "<div class='homeMenuMain'>";
		echo "	<div class='row'>";
		while ($row = $result -> fetch_array(MYSQLI_ASSOC)) {
			$id       = $row["id"];
			$table    = $row["table"];
			$menuText = $row["menuText"];
			$link     = $row["link"];
			$icon     = $row["icon"];

			$active   = "";

			if (is_null($icon))
				$icon = 'question';


			if (is_null($link) or ($link == ''))
				$link = "admin.php?t=$id";

			echo "
				<div class='col-xs-12 col-sm-4 col-md-3'>
					<div class='homeMenu'>
						<a href='$link'><i class='fa fa-$icon' aria-hidden='true'></i> $menuText</a>
					</div>
				</div>
			";
		}
		echo "	</div>";
		echo "</div>";
    }
    ?>
    </div>
	<?php
	createDropZone();
	ChangePass();
	// createModalEspecies();
	?>
	
</body>
</html>