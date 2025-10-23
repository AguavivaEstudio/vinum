
let pedidosVue = new Vue({
	el: '#tablaPedidos',
	data: {
		columns: [],
		rows: [],
		selectedRow: {},
		pagado: 2,
		enviado: 2,
		cancelado: 0
	},
	methods: {
		initialize() {
			$.get("pedidosGetData.php", function (result) {
				pedidosVue.columns = result.columns;
				pedidosVue.rows = result.rows;
				pedidosVue.rows.forEach(
					element => {
						element.display = !element.cancelado;
						element.items = JSON.parse(element.items);
					});
				setTimeout(function () {
					createDataTable();
					$('input[type=checkbox]').css('display', 'block');
				}, 300);
			});
		},
		checkData(row, columnName) {
			let checked = 0;
			if ($(event.target).prop("checked")) {
				checked = 1;
			}
			SubmitData('_updateChk.php?t=pedidos&c=' + columnName + '&i=' + row.id + '&v=' + checked, '', 'divError', false);
			setTimeout(function () {
				if ((columnName == 'enviado') && (checked == 1)) {
					$.get('../mailpedido.php?preference_id=' + row.preference_id)
						.done(function (data) { console.log('Correo enviado correctamente'); })
						.fail(function (data) { console.log('Error al enviar el correo'); });
				}
			}, 1000);
		},
		viewPedido(row) {
			this.selectedRow = row;
			row.totalPedido = 0;
			row.cantitadUnidades = 0;
			row.items.forEach(item => {
				item.total = item.precio * item.cantidad;
				row.totalPedido = row.totalPedido + item.total;
				row.cantitadUnidades = row.cantitadUnidades + item.cantidad;
			});
			$('#pedidoDetalle').modal('show');
		},
		filter() {
			this.rows.forEach(element => {
				let display = true;
				display = display && ((this.pagado == 2) || (this.pagado == 1 && element.pagado) || (this.pagado == 0 && !element.pagado));
				display = display && ((this.enviado == 2) || (this.enviado == 1 && element.enviado) || (this.enviado == 0 && !element.enviado));
				display = display && ((this.cancelado == 2) || (this.cancelado == 1 && element.cancelado) || (this.cancelado == 0 && !element.cancelado));
				element.display = display;
			});
		}
	}
});

pedidosVue.initialize();