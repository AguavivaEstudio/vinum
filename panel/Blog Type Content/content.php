<?php
include '_includes.php';
checkSecurity();

CreateHeadder();

if(isset($_REQUEST['t'])) {
	$tableSelected = $_REQUEST['t'];
} else {
	$tableSelected = "";
}
if(isset($_REQUEST['cliente'])) {
	$clientName = $_REQUEST['cliente'];
} else {
	$clientName = '';
}

?>
<body>
    <div class='container-fluid'>
    <?php
    createMenu();
    echo "<h1 id='divError' class='error'></h1>";

	$sSql = "SELECT `table` FROM `sys_tables` WHERE `id` = ?;";
	$result = ExecuteSql($sSql, array(null, $tableSelected));
	$row = $result -> fetch_array(MYSQLI_ASSOC);
	$table    = $row['table'];

	$permissions = getTablePermission($tableSelected);

	$id = $_REQUEST['id'];

	echo "<div class='divMenuOption'>";
	if (($permissions['view'] != 1) || (is_null($table))){
		echo "<h1 class='error'>" . getLangVar('AuthorizationDenied') . "</h1>";
	} else {
		if ($tableSelected == 18) {
			$tablenombre = 'blog';
			$rowdetail = 'blog';
			$table2 = "blog_contenido";
		}
		echo "
		<h4 class='colorGrey_2'>
			<a href='admin.php'>Home</a>
			<i class='fa fa-angle-right' aria-hidden='true'></i> <a href='admin.php?t=$tableSelected'>".ucfirst($tablenombre)."</a>
			<i class='fa fa-angle-right' aria-hidden='true'></i> Detalle
			<i class='fa fa-angle-right' aria-hidden='true'></i> <span class='colorOrange_1'>$clientName</span>
		</h4>";

		echo "<div id='tableAddDeleteBTN'>";
		if ($permissions['add']){
			echo "
				<button type='button' class='btn btn-success btn-sm newItemDetail' data-table='$table' data-td='$tableSelected' data-prodId='$id' data-tn='$clientName' data-id='0'>
					<i class='fa fa-plus' aria-hidden='true'></i><span>" . getLangVar('Add') . "</span>
				</button>
			";
		}
		echo "</div>";
		echo "</div>";

		echo "<table class='table table-bordered table-striped table-hover dt-responsive nowrap dataTable' data-table='$table'>";
		echo "<thead><tr><th></th><th>Tipo</th><th>Título</th><th>Publicado</th><th>Órden</th><th>Imagenes</th></tr></thead>";
		echo "<tbody>";


		$disabled = "disabled";
		if ($permissions['update'] == 1)
			$disabled = "";


		$sSql = "SELECT * FROM ".$table2." WHERE `".$rowdetail."` = ?;";
		$result = ExecuteSql($sSql, array(null, $id));
		while ($row = $result -> fetch_array(MYSQLI_ASSOC)) {
			echo "<tr data-rowId='" . $row['id'] . "' >";
			echo "<td class='text-center tdTableControls'>";
			if ($permissions['delete']){
				echo "
					<i class='fa fa-window-close delete' aria-hidden='true' data-table='".$table2."' data-rowId='" . $row['id'] . "'></i>
				";
			}
			if ($permissions['view'] || $permissions['update']){
				echo "<i class='fa fa-pencil-square newItemDetail' aria-hidden='true' data-td='$tableSelected' data-prodId='$id' data-id='" . $row['id'] . "' data-tn='" . $clientName . "'></i>";
			}
			echo "</td>";
			echo "<td>";
			echo $row['tipo'];
			echo "</td>";
			echo "<td>";
			echo $row['name'];
			echo "</td>";
			echo "<td class='text-center'>" . CreateCheckBox($table2, 'activo', $row['id'], $row['activo'], $disabled, 'clsPublish') . "</td>";
			echo "<td>";
			echo $row['orden'];
			echo "</td>";
			echo "<td class='text-center'>";
			if (($row['tipo'] != 'video') && ($row['tipo'] != 'texto') && ($row['tipo'] != 'url') && ($row['tipo'] != 'destacado')) { 
				echo "<i class='fa fa-picture-o' data-table='".$table2."' data-column='file_img' data-id='" . $row['id'] . "' aria-hidden='true'></i>";
			}
			echo "</td>";
			echo "</tr>";
		}
		echo "</tbody></table>";
		}
		echo "</div>";

	createDropZone();
	ChangePass(); 
	?>
</body>
</html>