<?php
function getMailParam($mailType){
	switch ($mailType) {
		case 'forgotPass':
			$sEmail = $_POST['forgetPass'];

			$sSql	= "SELECT password FROM vwuserspermissions WHERE email = ?;";
			$result = ExecuteSql($sSql, array(null, $sEmail));
			$row	= $result -> fetch_array(MYSQLI_ASSOC);
			$pass 	= $row['password'];


			$aFrom			= ['mail' => $_SESSION['sysConfig']['SMTP_User'], 'name' => $_SESSION['sysConfig']['SMTP_UserName']];
			$aTo			= [['mail' => $sEmail, 'name' => '']];
			$aCc			= null;
			$aBcc			= null;
			$subject		= "Recupero de password";

			if (!is_null($pass)){
				$body			= "Su contraseña es: $pass";
			} else {
				$body			= null;
			}

			$altBody		= $body;
			$aAttachment	= null;

			break;
		case 'opt2':
			break;
		default:
			$aFrom			= ['mail' => $_SESSION['sysConfig']['SMTP_User'], 'name' => $_SESSION['sysConfig']['SMTP_UserName']];
			$aTo			= [['mail' => 'emiber@gmail.com', 'name' => 'Emiliano Berestovoy']];
			$aCc			= null;
			$aBcc			= null;
			$subject		= "Subject del eM@il con ñ y &aacute;";
			$body			= "Este es el cuerpo <b>HTML</b> del correo con ñ y á.";
			$altBody		= "Este es el cuerpo en texto plano del correo con ñ y á.";
			$aAttachment	= null;
	}

	$arrReturn = [
		'aFrom'			=> $aFrom,
		'aTo'			=> $aTo,
		'aCc'			=> $aCc,
		'aBcc'			=> $aBcc,
		'subject'		=> $subject,
		'body'			=> $body,
		'altBody'		=> $altBody,
		'aAttachment'	=> $aAttachment
	];

	return $arrReturn;
}

?>