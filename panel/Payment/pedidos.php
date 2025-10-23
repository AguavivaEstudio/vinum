<?php
include '_includes.php';
checkSecurity();

CreateHeadder();
$tableSelected = 21;
?>

<body>
	<div class='container-fluid' id='tablaPedidos'>
		<?php
		createMenu();
		echo "<h1 id='divError' class='error'></h1>";
		$sSql = "SELECT `group`, `menuText`, `table`, `columns` FROM `sys_tables` WHERE `id` = ?;";
		$result = ExecuteSql($sSql, array(null, $tableSelected));
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$group    = $row['group'];
		$menuText = $row['menuText'];
		$table    = $row['table'];
		$columns  = $row['columns'];

		$permissions = getTablePermission($tableSelected);

		echo "<div class='divMenuOption'>";
		if (($permissions['view'] != 1) || (is_null($table))) {
			echo "<h1 class='error'>" . getLangVar('AuthorizationDenied') . "</h1>";
		} else {
			echo "<h4 class='colorGrey_2'>";
			echo "	<a href='admin.php'>Home</a> <i class='fa fa-angle-right' aria-hidden='true'></i>";
			echo "	$group <span class='colorOrange_1'><i class='fa fa-angle-right' aria-hidden='true'></i> $menuText</span>";
			echo "</h4>";
			echo "</div>";
		?>
			<div class="custom-options">
				<div class="opciones">
					<span>Pagado:</span>
					<select v-model="pagado" @change="filter()">
						<option value="2">Todos</option>
						<option value="1">Si</option>
						<option value="0">No</option>
					</select>
				</div>
				<div class="opciones">
					<span>Enviado:</span>
					<select v-model="enviado" @change="filter()">
						<option value="2">Todos</option>
						<option value="1">Si</option>
						<option value="0">No</option>
					</select>
				</div>
				<div class="opciones">
					<span>Cancelado:</span>
					<select v-model="cancelado" @change="filter()">
						<option value="2">Todos</option>
						<option value="1">Si</option>
						<option value="0">No</option>
					</select>
				</div>
			</div>
			<table class='table table-bordered table-striped table-hover dt-responsive nowrap dataTable' data-table='<?php echo $table; ?>'>
				<thead>
					<tr>
						<th></th>
						<template v-for="(column, index) in columns">
							<th v-if="column.showInList && index <= <?php echo $columns ?>" class='text-center'>
								{{column.displatName}}
							</th>
						</template>
					</tr>
				</thead>
				<tbody>
					<template v-for="row in rows">
						<tr v-bind:class="{ hidden: !row.display }">
							<td><i class="fa fa-eye" aria-hidden="true" @click='viewPedido(row)'></i></td>
							<template v-for="(column, index) in columns">
								<td v-if="column.showInList && index <= <?php echo $columns ?>" class='text-center'>
									<template v-if="column.COLUMN_TYPE == 'tinyint(1)'">
										<span class='hidden'>{{row[column.COLUMN_NAME]}}</span>
										<input type='checkbox' value='1' v-bind:checked="row.value" v-model="row[column.COLUMN_NAME]" @click="checkData(row, column.COLUMN_NAME)" :disabled="(column.COLUMN_NAME=='pagado') || (column.COLUMN_NAME=='enviado' && row[column.COLUMN_NAME] == '1')" />
									</template>
									<template v-else>
										{{row[column.COLUMN_NAME]}}
									</template>
								</td>
							</template>
						</tr>
					</template>
				</tbody>
			</table>
		<?php
		}
		?>

		<div class="modal fade bd-example-modal-lg" id="pedidoDetalle" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog " role="document">
				<div class="modal-content modal-lg">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Detalle de pedido</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="container-fluid">
							<div class="row">
								<template v-for="column in columns">
									<template v-if="column.COLUMN_NAME != 'id'">
										<template v-if="column.COLUMN_TYPE == 'tinyint(1)'">
											<div class="col-xs-6 col-sm-4">
												<label>{{column.displatName}}: </label>
												<input type='checkbox' value='1' v-bind:checked="selectedRow.value" v-model="selectedRow[column.COLUMN_NAME]" @click="checkData(selectedRow, column.COLUMN_NAME)" :disabled="(column.COLUMN_NAME=='pagado') || (column.COLUMN_NAME=='enviado' && selectedRow[column.COLUMN_NAME] == '1')" />
											</div>
										</template>
										<template v-else-if="column.COLUMN_NAME == 'items'">
											<div class="col-xs-12">
												<label>{{column.displatName}}: </label>
												<table class='table table-bordered table-hover dt-responsive nowrap'>
													<thead>
														<tr>
															<th class='text-center' scope="row">Código</th>
															<th class='text-center'>Título</th>
															<th class='text-right '>Cantidad</th>
															<th class='text-right price'>Precio</th>
															<th class='text-right total'>Total</th>
														</tr>
													</thead>
													<tbody>
														<tr v-for="item in selectedRow.items">
															<th class='text-center'>{{item.codigo}}</th>
															<td>{{item.titulo}}</td>
															<td class='text-right'>{{item.cantidad}}</td>
															<td class='text-right'>$ {{item.precio}}</td>
															<td class='text-right'>$ {{item.total}}</td>
														</tr>
													</tbody>
													<tfoot>
														<tr>
															<th class='total'>Total</th>
															<th></th>
															<th class='text-right font-weight-bold'>{{selectedRow.cantitadUnidades}}</th>
															<th></th>
															<th class='text-right font-weight-bold'>$ {{selectedRow.totalPedido}}</th>
														</tr>
													</tfoot>
												</table>
											</div>
										</template>
										<template v-else>
											<div class="col-xs-6 col-sm-4">
												<label>{{column.displatName}}: </label>
												<div class='dato'>{{selectedRow[column.COLUMN_NAME]}}</div>
											</div>
										</template>
									</template>
								</template>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-success2" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	createDropZone();
	ChangePass();
	?>
	<script src='js/pedidos.min.js'></script>
</body>

</html>