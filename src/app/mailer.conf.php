<?php

function getMailer() {
	$mail = new PHPMailer(true);
	//$mail->SMTPDebug = 3;
	$mail->SMTPOptions = array(
	    'ssl' => array(
	        'verify_peer' => false,
	        'verify_peer_name' => false,
	        'allow_self_signed' => true
	    )
	);
	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'smtp.gmail.com';						  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'ait2ndhand2017@gmail.com';         // SMTP username
	$mail->Password = 'AIT2ndhand2017RoGPgA';             // SMTP password
	$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 587;                                    // TCP port to connect to
	$mail->setFrom('ait2ndhand2017@gmail.com', 'Second Hand Shop');		// From header
	$mail->addReplyTo('ait2ndhand2017@gmail.com', 'Second Hand Shop');	// Reply-To header

	return $mail;
}