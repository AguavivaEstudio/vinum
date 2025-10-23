<?php
require 'PHPMailer/PHPMailerAutoload.php';
require 'config.php';

function phpMail($aFrom, $aTo, $aCc, $aBcc, $subject, $body, $altBody, $aAttachment){
	$mail = new PHPMailer;
	//$mail -> SMTPDebug = 3;										// Enable verbose debug output
	$mail -> isSMTP();												// Set mailer to use SMTP
	$mail -> isHTML(true);											// Set email format to HTML
	$mail -> Host		= $_SESSION['sysConfig']['SMTP_Host'];		// Specify main and backup SMTP servers
	$mail -> Port		= $_SESSION['sysConfig']['SMTP_Port'];		// TCP port to connect to
	$mail -> Username	= $_SESSION['sysConfig']['SMTP_User'];		// SMTP username
	$mail -> Password	= $_SESSION['sysConfig']['SMTP_Pass'];		// SMTP password
	$mail -> SMTPAuth	= $_SESSION['sysConfig']['SMTP_Auth'];		// Enable SMTP authentication
	$mail -> SMTPSecure	= $_SESSION['sysConfig']['SMTP_Secure'];	// Enable TLS encryption, `ssl` also accepted ('tls', 'ssl' or '')

	$mail -> setFrom($aFrom['mail'], $aFrom['name']);

	foreach ($aTo as $i => $value) {
		$mail -> addAddress($aTo[$i]['mail'], $aTo[$i]['name']);		// Add a recipient
	}

	if(!is_null($aCc)){
		foreach ($aCc as $i => $value) {
			$mail -> AddCC($aCc[$i]['mail'], $aCc[$i]['name']);	// Add a recipient Cc
		}
	}

	if(!is_null($aBcc)){
		foreach ($aBcc as $i => $value) {
			$mail -> AddBCC($aBcc[$i]['mail'], $aBcc[$i]['name']);	// Add a recipient Bcc
		}
	}

	if(!is_null($aAttachment)){
		foreach ($aAttachment as $i => $value) {
			$mail -> addAttachment($aAttachment[$i]);					// Add attachments
		}
	}

	$mail -> Subject	= $subject;
	$mail -> Body		= $body;
	$mail -> AltBody	= $altBody;

	$mail->send();
}

// $arrReturn = getMailParam(null);
// phpMail( $arrReturn['aFrom'], $arrReturn['aTo'], $arrReturn['aCc'], $arrReturn['aBcc'], $arrReturn['subject'], $arrReturn['body'], $arrReturn['altBody'], $arrReturn['aAttachment']);
?>