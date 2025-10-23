<?php
ob_start();
include '_includes.php';

$sSql = "SELECT `key`, `value` FROM `sys_config`;";
$result = ExecuteSql($sSql, null);

$_SESSION['sysConfig'] = array();
while ($row = $result -> fetch_array(MYSQLI_ASSOC)) {
	$key = $row['key'];
	$val = $row['value'];
	$_SESSION['sysConfig'][$key] = $val;
}

CreateHeadder();

$rowCount = -1;
$loginError = 0;

if (isset($_POST['forgetPass'])){
	$sEmail = $_POST['forgetPass'];

	$sSql = "SELECT password FROM vwuserspermissions WHERE email = ?;";
	$result = ExecuteSql($sSql, array(null, $sEmail));
	$row = $result -> fetch_array(MYSQLI_ASSOC);

	$arrReturn = getMailParam('forgotPass');

	if (!is_null($arrReturn['body'])){
		phpMail( $arrReturn['aFrom'], $arrReturn['aTo'], $arrReturn['aCc'], $arrReturn['aBcc'], $arrReturn['subject'], $arrReturn['body'], $arrReturn['altBody'], $arrReturn['aAttachment']);
	} else {
		$loginError = 1;
	}
}

if (isset($_POST['email']) && isset($_POST['password'])){
	$sEmail = $_POST['email'];
	$sPass  = $_POST['password'];

	$sSql = "SELECT id, email, MAX(sysadmin) AS sysadmin, count(1) AS rowCount FROM vwuserspermissions WHERE email = ? AND password = ? GROUP BY id, email;";
	$sSql = "SELECT id, email, MAX(sysadmin) AS sysadmin, count(1) AS rowCount FROM 
	(select `usr`.`id` AS `id`,`usr`.`email` AS `email`,`usr`.`password` AS `password`,`usr`.`profile` AS `profile`,`pro`.`sysadmin` AS `sysadmin`,`per`.`sys_tables` AS `sys_tables`,`per`.`sys_tags` AS `sys_tags`,`per`.`view` AS `view`,`per`.`add` AS `add`,`per`.`update` AS `update`,`per`.`remove` AS `remove`,`per`.`full` AS `full` from ((`sys_users` `usr` left join `sys_profiles` `pro` on((`usr`.`profile` = `pro`.`id`))) left join `sys_permissions` `per` on((`pro`.`id` = `per`.`sys_profiles`))) where (`usr`.`active` = 1))
	as vwuserspermissions WHERE email = ? AND password = ? GROUP BY id, email;";
	
	$result = ExecuteSql($sSql, array(null, $sEmail, $sPass));
	$row = $result -> fetch_array(MYSQLI_ASSOC);

	$rowCount = $row['rowCount'];

	$loginError = 1;
}
if ($rowCount > 0){
	$_SESSION['USER_id']       = $row['id'];
	$_SESSION['USER_email']    = $row['email'];
	$_SESSION['USER_sysadmin'] = $row['sysadmin'];

	$sSql = "
			SELECT `tab`.`table`, `per`.`sys_tables`, `per`.`sys_tags`, `per`.`view`, `per`.`add`, `per`.`update`, `per`.`remove`, `per`.`full`
			  FROM `sys_users` `usr`
			  LEFT JOIN `sys_profiles`    `pro` ON `usr`.`profile`    = `pro`.`id`
			  LEFT JOIN `sys_permissions` `per` ON `pro`.`id`         = `per`.`sys_profiles`
			  LEFT JOIN `sys_tables`      `tab` ON `per`.`sys_tables` = `tab`.`id`
			 WHERE `usr`.`id` = ?
			";
	$result = ExecuteSql($sSql, array(null, $_SESSION['USER_id']));

	$json = array( );
	while ($row = $result -> fetch_array(MYSQLI_ASSOC)) {  
		$json[] = $row;
	}
	$_SESSION['USER_permission'] = $json;

	logAction('sign in', '');
	header('Location: admin.php');
}
?>
<body id='bodyLogin'>
    <div class='container-fluid' id='login'>
    	<div class='row'>
    		<div class='hidden-xs col-sm-3 col-md-4'></div>
    		<div class='col-xs-12 col-sm-6 col-md-4' id='loginDiv'>
    			<div class='headder'>
					<h1 class='alignCenter'><?php echo $_SESSION['sysConfig']['ProjectName'] ?></h1>
					<h3 class='alignCenter'><?php echo getLangVar('PanelTitle') ?></h3>
				</div>
				<form name='frmLogin' action='index.php' method='post' id='frmLogin'>
					<div class='form-group'>
						<input class='form-control' type='email'    name='email'    id='email'    placeholder='eM@il'    required autofocus>
						<i class="fa fa-envelope" aria-hidden="true"></i>
					</div>
					<div class='form-group'>
						<input class='form-control' type='password' name='password' id='password' placeholder='Password' required>
						<i class="fa fa-lock" aria-hidden="true"></i>
					</div>
					<div class='form-group'>
						<button class='form-control submit' type='submit'><?php echo getLangVar('Login') ?></button>
					</div>
					<div class='form-group'>
						<div class='divLine'></div>
					</div>
					<div class='form-group'>
						<button class='form-control forgot' type='button' data-toggle='modal' data-target='#modalPass'><?php echo getLangVar('ForgotPassword') ?></button>
					</div>
					<div class='NombrePanel'>
						<strong>CARDUMEN</strong> VERSIÓN 2.0 - 2020 - &reg; <strong>1NA Digital</strong> <br>
						<small>Powered by Aguaviva</small>
					</div>
				</form>
			</div>
    		<div class='hidden-xs col-sm-3 col-md-4'></div>
    	</div>
	<?php
	if ($loginError == 1) {
		echo "
			<div class='modal fade modalAutoOpen' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'>
				<div class='modal-dialog modal-sm' role='document'>
					<div class='modal-content modalContentError'>
						<div class='modal-header'>
							ERROR
						</div>
						<div class='modal-body'>
							" . getLangVar('LoginError') . "
						</div>
					</div>
				</div>
			</div>
			";
	}
	?>
    </div>

	<?php
	echo "
		<div class='modal fade' tabindex='-1' role='dialog' id='modalPass'>
			<div class='modal-dialog modal-sm' role='document'>
				<div class='modal-content modalContentError'>
					<div class='modal-header'>
						" . getLangVar('ForgotPassword') . "
					</div>
					<div class='modal-body'>
						<form name='forgetPass' action='index.php' method='post' id='forgetPass'>
							<div class='form-group'>
								<input type='email' class='form-control' name='forgetPass' placeholder='eM@il' required>
								<i class='fa fa-envelope' aria-hidden='true'></i>
							</div>
							<div class='form-group'>
								<button class='form-control forgot' type='submit' data-toggle='modal' data-target='#modalPass'>" . getLangVar('ForgotPassword') . "</button>
							</div>
						</form>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	";
	?>
</body>
</html>