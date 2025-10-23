$(document).ready(function() {
    $('.modalAutoOpen').modal('show');
    $('.externalLink').attr('target', '_blank');

    if ($('#tablaPedidos').length === 0) {
        createDataTable();
    }

    $('textarea').ckeditor();

    $(document).on('click', "a[data-target='_blank']", function() { $(this).attr("target", "_blank") });
    $(document).on('click', '.clsPublish', function() { checkData(this); });
    $(document).on('click', '.delete', function() { deleteRow(this); });
    $(document).on('click', '.newItem', function() { addRow(this); });
    $(document).on('click', '.btnDeleteMultiple', function() { deleteSelected(this); });
    $(document).on('click', '.fa-upload', function() { uploadFile(this); });
    $(document).on('click', '.fa-picture-o', function() { uploadFile(this); });
    $(document).on('click', '.editEspecies', function() { modalEspecies(this); });
    $(document).on('click', '.chkEspecie', function() { checkEspecie(this); });
    $(document).on('click', '.editContent', function() { editContent(this); });
    $(document).on('click', '.editCustom', function() { editCustom(this); });
    $(document).on('click', 'ol.sortable', function() { sortData(this); });
    $(document).on('click', '.editFile', function() { launchEditor(this); });
    $(document).on('click', '#btnCancellForm', function() { history.back(1); });
    $(document).on('click', '.newItemDetail', function() { newProductionDetail(this); });
    $(document).on('click', '.fileUploaded', function() { editImage(this); });

    $(document).on('change', '#type', function() { shoWHideControls(this); });

    $(document).on('hover', '.newItem', function() {
        $('.btnDeleteMultiple').hide();
        alert(1);
    });

    shoWHideControls($('#type'));

    $('.newItem').hover(
        function() { $('.btnDeleteMultiple').addClass('hidden'); },
        function() { $('.btnDeleteMultiple').removeClass('hidden'); }
    );

    $(document).on('click', '#modalChangePassSubmit', function(event) {
        event.preventDefault();
        changePasswordSubmit();
    });

    $('#modalChangePass').on('hidden.bs.modal', function(e) {
        changePasswordReset();
    });

    $(window).resize(function() {
        setNavVarPadding();
    });
    setNavVarPadding();

    $("input[type='search']").attr('placeholder', '   Buscar...');


    if ($("#dropzone")[0]) {
        var myDropzone = new Dropzone('#dropzone');

        myDropzone.on('success', function() {
            updateFileList();
        });
    }
});

function createDataTable() {
    $('.dataTable').DataTable({
        'order': [
            [1, 'desc']
        ],
        'lengthMenu': [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        stateSave: true,
        language: {
            lengthMenu: 'Mostrar _MENU_ registros',
            zeroRecords: 'No hay datos',
            info: 'Mostrando página _PAGE_ de _PAGES_',
            infoEmpty: 'No hay datos',
            infoFiltered: '(filtrado de _MAX_ registros)',
            search: '',
            paginate: {
                previous: '‹',
                next: '›'
            },
            aria: {
                paginate: {
                    previous: 'Previous',
                    next: 'Next'
                }
            }
        },
        responsive: true,
        'columnDefs': [{ 'width': '10px', 'targets': 0 }]
    });
}

function setNavVarPadding() {
    height = $('#divMenu .navbar-brand h3').css('height');
    height = height.replace('px', '');
    height = parseInt(height) + 10;
    height = height + 'px';
    $('#divMenu .navbar-brand h3').css('padding-left', height);
}

function changePasswordReset() {
    $('#password').val('');
    $('#password2').val('');
    $('#modalChangePassForm .alert').addClass('hidden');
    $('#modalChangePassForm .buttons').removeClass('hidden');
    $('#modalChangePassForm .alert-danger').html($('#modalChangePassForm .alert-danger').attr('data-text'));
}

function changePasswordSubmit() {
    pass1 = $('#password').val();
    pass2 = $('#password2').val();

    $('#modalChangePassForm .alert').addClass('hidden');

    $('#modalChangePassForm .alert-danger').html($('#modalChangePassForm .alert-danger').attr('data-text'));
    if (pass1 == '') {
        $('#modalChangePassForm .alert-danger').html($('#modalChangePassForm .alert-danger').attr('data-text-empty'));
        $('#modalChangePassForm .alert-danger').removeClass('hidden');
    } else if (pass1 == pass2) {
        if (SubmitData('_changePassword.php', $("#modalChangePassForm").serialize(), '', false)) {
            $('#modalChangePassForm .alert-success').removeClass('hidden');
            $('#modalChangePassForm .buttons').addClass('hidden');
        } else {
            $('#modalChangePassForm .alert-danger').html($('#modalChangePassForm .alert-danger').attr('data-text-error'));
            $('#modalChangePassForm .alert-danger').removeClass('hidden');
        }
    } else {
        $('#modalChangePassForm .alert-danger').removeClass('hidden');
    }
}

function sortData(oList) {
    $(oList).sortable({
            onDrop: function($item, container, _super) {
                _super($item, container);
                $(oList).find('li').each(function() {
                    li = $(this);
                    id = li.attr('data-rowId');
                    order = li.index() + 1;
                    bRequestOk = SubmitData('_saveOrder.php?id=' + id + '&order=' + order, '', '', false);
                });
            }
        }

    );
}

// var featherEditor = new Aviary.Feather({
// 	apiKey: '4f65fd6ab8d8419c85c4e6332f8845b9',
// 	tools: ['crop', 'resize'],
// 	onSave: function (imageID, newURL) {
// 		var img = document.getElementById(imageID);
// 		imgSrc = img.src;
// 		bRequestOk = SubmitData("_saveEditedPic.php?o=" + imgSrc + "&e=" + newURL, '', '', false);

// 		//time = new Date().getTime();
// 		//img.src = imgSrc + '?t=' + time;
// 		img.src = imgSrc;

// 		if (bRequestOk == true)
// 			return false;
// 	},
// 	onClose(isDirty) {
// 		if (!isDirty)
// 			featherEditor.close();
// 	},
// 	onError(errorObj) {
// 		alert(errorObj);
// 	}
// });

function launchEditor(oBtn) {
    oFile = document.getElementById($(oBtn).attr('data-image'));
    id = $(oFile).attr('id');
    src = $(oFile).attr('src');

    featherEditor.launch({
        image: id,
        url: src
    });
    return false;
}

function newProductionDetail(oBtn) {
    prod = $(oBtn).attr('data-prodId');
    id = $(oBtn).attr('data-id');
    td = $(oBtn).attr('data-td');
    tn = $(oBtn).attr('data-tn');
    if (tn == '') {
        document.location.href = 'contentDetail.php?t=' + td + '&p=' + prod + '&id=' + id;
    } else {
        document.location.href = 'contentDetail.php?t=' + td + '&p=' + prod + '&id=' + id + '&tn=' + tn;
    }
}

function shoWHideControls(oControl) {
    value = $(oControl).val();
    $('.tipoVideo').addClass('hidden');
    $('.tipoTxt').addClass('hidden');
    $('.tipoLink').addClass('hidden');
    $('.tipoDestacado').addClass('hidden');
    $('.tipoImagen').addClass('hidden');
    
    if (value == 'video') {
        $('.tipoVideo').removeClass('hidden');
    }
    if (value == 'text' || value == 'imagen|text') {
        $('.tipoTxt').removeClass('hidden')
    }
    if (value == 'video') {
        $('.tipoVideo').removeClass('hidden')
    }
    if (value == 'url') {
        $('.tipoLink').removeClass('hidden');
    }
    if (value == 'image') {
        $('.tipoImagen').removeClass('hidden');
    }
}

function editContent(oControl) {
    tableId = $(oControl).attr('data-tableId');
    rowId = $(oControl).attr('data-id');
    document.location.href = 'content.php?t=' + tableId + '&id=' + rowId;
}

function editCustom(oControl) {
    tableId = $(oControl).attr('data-tableId');
    rowId = $(oControl).attr('data-id');
    clientId = $(oControl).attr('data-cliente');
    document.location.href = 'content.php?t=' + tableId + '&id=' + rowId + '&cliente=' + clientId;
}

function uploadFile(oUp) {
    $('#modalUpload .dz-preview').remove();

    tableName = $(oUp).attr('data-table');
    columnName = $(oUp).attr('data-column');
    rowId = $(oUp).attr('data-id');

    $("#modalUpload input[name='tableName']").val(tableName);
    $("#modalUpload input[name='columnName']").val(columnName);
    $("#modalUpload input[name='rowId']").val(rowId);

    bRequestOk = SubmitData('_getFileList.php?tableName=' + tableName + '&columnName=' + columnName + '&rowId=' + rowId, '', 'fileList', false);

    $('#modalUpload').modal('show');
    $('.externalLink').attr('target', '_blank');
}

function updateFileList() {
    $('#modalUpload .dz-preview').remove();
    tableName = $("#modalUpload input[name='tableName']").val();
    columnName = $("#modalUpload input[name='columnName']").val();
    rowId = $("#modalUpload input[name='rowId']").val();

    bRequestOk = SubmitData('_getFileList.php?tableName=' + tableName + '&columnName=' + columnName + '&rowId=' + rowId, '', 'fileList', false);
}

function checkData(oCheck) {
    var sTable = $(oCheck).attr('data-TABLE_NAME');
    var sColumn = $(oCheck).attr('data-COLUMN_NAME');
    var sid = $(oCheck).attr('data-rowId');

    if ($(oCheck).is('input[type=checkbox]')) {
        if (oCheck.checked)
            checked = 1;
        else
            checked = 0;
    } else {
        if ($(oCheck).hasClass('checked_0')) {
            $(oCheck).addClass('checked_1');
            $(oCheck).removeClass('checked_0');
            $(oCheck).children('label').html($(oCheck).attr('data-visible'));
            checked = 1;
        } else {
            $(oCheck).addClass('checked_0');
            $(oCheck).removeClass('checked_1');
            $(oCheck).children('label').html($(oCheck).attr('data-hidden'));
            checked = 0;
        }
    }

    bRequestOk = SubmitData('_updateChk.php?t=' + sTable + '&c=' + sColumn + '&i=' + sid + '&v=' + checked, '', 'divError', false);
}

function deleteRow(oCheck) {
    if (confirm('Are you sure about that???')) {
        var sTable = $(oCheck).attr('data-table');
        var sid = $(oCheck).attr('data-rowId');

        bRequestOk = SubmitData('_delete.php?t=' + sTable + '&i=' + sid, '', 'divError', false);

        if (bRequestOk == true) {
            if ($(oCheck).hasClass('deletePopUp')) {
                $(oCheck).parent().parent().parent().remove();
            } else {
                document.location.href = document.location.href;
            }
        }
    }
}

function addRow(oBtn) {
    var sTable = $(oBtn).attr('data-table');
    document.location.href = "add.php?t=" + sTable;
}

function deleteSelected(oCheck) {
    bRequestAll = true;
    if (confirm('Are you sure about that???')) {
        var sTable = $(oCheck).attr('data-table');
        $('table').each(function() {
            oTable = $(this);
            if (oTable.attr('data-table') == sTable) {
                oTable.find('.chkDeleteMultiple:checked').each(function() {
                    sid = $(this).attr('data-id');
                    bRequestOk = SubmitData('_delete.php?t=' + sTable + '&i=' + sid, '', 'divError', false);
                    bRequestAll = bRequestAll && bRequestOk;
                });
            }
        });
        if (bRequestAll == true) {
            document.location.href = document.location.href;
        }
    }
}

function SubmitData(pURL, arrControlsData, pElementToSet, pShowAlert) {
    var bRequestOk;
    var arrData = new Object();

    bRequestOk = true;
    /*
    if (arrControlsData.length > 0){
        arrControlsData = arrControlsData.split('|');
        for (var i = 0; i < arrControlsData.length; i++){
            arrData[arrControlsData[i]] = document.getElementById(arrControlsData[i]).value;
        }
    }
    */
    $.ajax({
        type: "POST",
        url: pURL,
        dataType: 'html',
        cache: false,
        data: arrControlsData,
        error: function() {
            bRequestOk = false;
            alert('Error al cargar la página');
        },
        success: function(msg) {
            if (pElementToSet != '') {
                document.getElementById(pElementToSet).innerHTML = msg;
            }
            if (pShowAlert)
                alert(msg);
        }
    });
    return bRequestOk;
}

///////////////// Custom /////////////////
function modalEspecies(oControl) {
    project = $(oControl).attr('data-id');
    bRequestOk = SubmitData('_includesCustomEspecies.php?method=get&project=' + project, '', 'especiesSeleccionadas', false);
    $('#modalEspecies').modal('show');
}

function checkEspecie(oCheck) {
    var project = $(oCheck).attr('data-proyecto');
    var especie = $(oCheck).attr('data-especie');

    if (oCheck.checked) checked = 1;
    else checked = 0;

    bRequestOk = SubmitData('_includesCustomEspecies.php?method=set&project=' + project + '&especie=' + especie + '&value=' + checked, '', '', false);
}

function setFileComment(id, comment) {
    $.ajax({
        type: "POST",
        url: "_setFileComment.php",
        dataType: 'html',
        cache: false,
        data: { id: id, comment: comment },
        error: function() {
            alert('Error al guardar el copete');
        },
        success: function(msg) {}
    });
}
function editImage(img) {
    console.log(img);
    document.location.href = "editImage.php";
} 