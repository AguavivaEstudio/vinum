<?php
$_languages = array();

if (isset($_GET['lang'])) {
	if ($_GET['lang'] == 'es') {
		$_SESSION['lang'] = 0;
	} else {
		$_SESSION['lang'] = 1;
	}
}

if(!isset ($_SESSION['lang'])){
	$_SESSION['lang'] = 0;
}

function getLangVar($txt) {
	global $_languages;
	$txt = $_languages[$txt][$_SESSION['lang']];
	return $txt;
}

$_languages['PanelTitle']           = array('Panel de Contenidos', 'Content Manager');
$_languages['Login']				= array('Ingresar', 'Login');
$_languages['ForgotPassword']		= array('Olvidé mi contraseña', 'Forgot Password');
$_languages['LoginError']           = array('Usuario o contraseña incorrecta', 'Incorrect user or password');
$_languages['AuthorizationDenied']  = array('Autorizacion denegada', 'Authorization denied');
$_languages['ChangePassword']		= array('Cambiar contraseña', 'Change Password');
$_languages['PassNotMatch']			= array('Las contraseñas no coinciden', 'Passwords do not match');
$_languages['PassChanged']			= array('Las contraseña se actualizó correctamente!', 'Password changed!');
$_languages['PassEmpty']			= array('Las contraseña está vacia!', 'Password empty!');
$_languages['Error']				= array('Error!', 'Error!');
$_languages['Add']					= array('Agregar Registro', 'Add Record');
$_languages['Delete']				= array('Borrar Registro(s)', 'Delete Row(s)');
$_languages['Delete2']				= array('Eliminar', 'Delete');
$_languages['Edit']					= array('Editar', 'Edit');
$_languages['Visible']				= array('Visible', 'Visible');
$_languages['Hidden']				= array('Oculto', 'Hidden');

$_languages['PanelBodyTextL1']		= array('Bienvenido a su panel de administraci&oacute;n', 'Welcome to your admin panel');
$_languages['PanelBodyTextL2']		= array('SELECCIONE UNA OPCI&Oacute;N DEL MEN&Uacute; PARA COMENZAR A TRABAJAR', 'SELECT A MENU OPTION TO START');

$_languages['Save']					= array('Grabar', 'Save');
$_languages['Cancel']				= array('Cancelar', 'Cancel');

$_languages['DropZoneTitle']		= array('UPLOAD DE ARCHIVOS', 'FILES UPLOAD');
$_languages['DropZoneBody']			= array('Arrastre sus archivos<br>o haga click aquí', 'Drop files here<br>or click to upload');
?>