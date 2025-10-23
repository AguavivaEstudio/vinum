<?php
include '_includes.php';
checkSecurity();

$tableName	= $_REQUEST ['tableName'];
$columnName	= $_REQUEST ['columnName'];
$rowId		= $_REQUEST ['rowId'];


$actualPath = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$actualPath = str_replace('panel/_getFileList.php', 'uploads', $actualPath);
?>
<ol class='sortable' data-table='sys_files'>
	<?php
	$sSql = "SELECT `id`, `tableName`, `columnName`, `rowId`, `fileName`, `publish`, `order`, `comment` FROM `sys_files` WHERE `tableName` = ? AND `columnName` = ? AND `rowId` = ? ORDER BY `order`;";
	$result = ExecuteSql($sSql, array(null, $tableName, $columnName, $rowId));

	while ($row = $result -> fetch_array(MYSQLI_ASSOC)) {
		$id         = $row['id'];
		$rowId      = $row['rowId'];
		$fileName   = $row['fileName'];
		$publish    = $row['publish'];
		$comment    = $row['comment'];

		$fileExtention = explode(".", $fileName);
		$fileExtention = strtolower(end($fileExtention));

		$chkPublish = CreateCheckBox('sys_files', 'publish', $id, $publish, '', 'clsPublish');

		if (($fileExtention == 'png') || ($fileExtention == 'gif') || ($fileExtention == 'jpg') || ($fileExtention == 'jpeg') || ($fileExtention == 'webp')){
			$bEditableImage = true;
			$bEditLink	= true;
			$btnHtml	= "<button type='button' class='btn editFile' data-image='file_$id'><i class='fa fa-pencil' aria-hidden='true'></i>" . getLangVar('Edit') . "</button>";
		} else {
			$bEditableImage = false;
			$bEditLink	= false;
			$btnHtml	= "";
		}

		echo "<li data-rowId='$id' class='liFiles'><div class='row'>";
		$visible = getLangVar('Visible');
		$hidden  = getLangVar('Hidden');
		if ($publish == 1) {
			$visHid = $visible;
		} else {
			$visHid = $hidden;
		}
		if ($bEditableImage == true){

			// if (file_exists("../uploads/" . $fileName)) {
			// 	echo "../uploads/" . $fileName;
			// } else {
			// 	echo "false../uploads/" . $fileName;
			// }
			echo "
				<div class='col-xs-6 imageContainer'>
					<img src='$actualPath/$fileName' id='file_$id' alt='$fileName' class='fileUploaded' />
				</div>

				<div class='col-xs-6 colBtn100'>
					<input type='text' name='copete' id='copete' class='copete' placeholder='Copete' value='$comment' onBlur='setFileComment($id, this.value);'>
					<button type='button' class='btn checked_$publish clsPublish' data-table_name='sys_files' data-column_name='publish' data-rowid='$id' checked='checked' data-visible='$visible' data-hidden='$hidden'><i class='fa fa-eye' aria-hidden='true'></i><i class='fa fa-eye-slash' aria-hidden='true'></i><label>$visHid</label></button>
					<button type='button' class='btn delete deletePopUp' data-table='sys_files' data-rowId='$id'><i class='fa fa-times' aria-hidden='true'></i>" . getLangVar('Delete2') . "</button>
				</div>
			";
		} else {
			echo "
				<div class='col-xs-12 text-center'>
					<a href='$actualPath/$fileName' class='externalLink'>$fileName</a>
				</div>

				<div class='col-xs-6 colBtn100'>
					<button type='button' class='btn checked_$publish clsPublish' data-table_name='sys_files' data-column_name='publish' data-rowid='$id' checked='checked' data-visible='$visible' data-hidden='$hidden'><i class='fa fa-eye' aria-hidden='true'></i><i class='fa fa-eye-slash' aria-hidden='true'></i><label>$visHid</label></button>
				</div>
				<div class='col-xs-6 colBtn100'>
					<button type='button' class='btn delete deletePopUp' data-table='sys_files' data-rowId='$id'><i class='fa fa-times' aria-hidden='true'></i>" . getLangVar('Delete2') . "</button>
				</div>
				";
		}
		echo "</div></li>";
		echo "
					</div>
				</div>
			</div>
		</li>
		";
	}
	?>
</ol>