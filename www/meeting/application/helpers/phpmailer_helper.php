<?php




function sendMail($ReceiverName, $ReceiverEmail,$Subject,$Message, $SenderName, $SenderEmail, $CcEmail){
	require_once '../PHPMailer/PHPMailerAutoload.php';
	$mail = new PHPMailer();
	//Set Enable SMTP debugging.
	// $mail->SMTPDebug = 1;
	//Set PHPMailer to use SMTP.
	$mail->isSMTP();
//Set SMTP host name
	$mail->Host = "mail.exhibit.com.pk";
	$mail->SMTPOptions = array(
		'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
		)
	);
	//Set this to true if SMTP host requires authentication to send email
	$mail->SMTPAuth = true;
	//Provide username and password
	$mail->Username = "donotreply@exhibit.com.pk";
	$mail->Password = "badar@123";
	//If SMTP requires TLS encryption then set it
	$mail->SMTPSecure = "ssl  ";
	$mail->Host = 'ssl://mail.exhibit.com.pk:465';

//Set TCP port to connect to
	$mail->Port = 465;


//Set Sender Information //
	$mail->From = $SenderEmail;

// Change Sender Name  //
	$mail->FromName = $SenderName;


	// Set Receiver Information //
	$mail->addAddress($ReceiverEmail,$ReceiverName);

// Set Mail CC / BCC //
	if($CcEmail !=""){
		$mail->addCC($CcEmail);
	}

	$mail->addReplyTo('facilitation@exhibit.com.pk','IDEAS Facilitation');
	$mail->addBCC('ieeep2018@gmail.com');


//Attachments//
	//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name


	$mail->isHTML(true);

	$mail->Subject = $Subject;
	$mail->Body = $Message;
	$mail->AltBody = "This is the plain text version of the email content";

	if(!$mail->send())
	{
		echo "Mailer Error: " . $mail->ErrorInfo;
	}
	else
	{
		echo "Message has been sent successfully ".$ReceiverName."  ". $ReceiverEmail;;
	}


}

