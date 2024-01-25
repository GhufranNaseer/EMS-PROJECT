<html>
<head>

    <style>

        input {
            width: 300px;
            margin-bottom: 10px;
            height: 25px;
            padding: 10px;
            font-size: 14px;
        }

    </style>
</head>

<body>

<div style="width: 500px; padding: 25px;">
    <form method="post" style="width: 100%">

        <label>Receiver Name</label><br> <input type="tex" name="rName" required><br>
        <label>Receiver Email Address</label> <br> <input type="email" required name="rEmail"><br>
        <label>Sender Name</label> <br> <input type="text" name="sName" required><br>
        <label>Sender Email Address</label> <br> <input type="email" name="sEmail" required><br><br>

        <label>Subject</label> <br> <input type="text" name="subject" required style="width: 350px"><br>
        <label>Message</label> <br> <textarea typeof="text" name="message" style="width: 350px; height: 100px" required></textarea>


        <br>
        <br>
        <label>Cc Email Address (Optional)</label> <br> <input type="email" name="ccEmail"><br>
        <br>
        <br>

        <input type="submit" name="post" style="height:40px; width: 200px; float: left">
        <br>
        <br>
        <br>
    </form>


</div>


</body>
</html>




<?php

if(isset($_POST['post'])){

	$ReceiverName = $_POST['rName'];
	$ReceiverEmail = $_POST['rEmail'];
	$SenderName = $_POST['sName'];
	$SenderEmail = $_POST['sEmail'];
	$Subject = $_POST['subject'];
	$Message = $_POST['message'];
	$CcEmail = $_POST['ccEmail'];

	if($ReceiverEmail !=""){

		echo $mail = sendMail($ReceiverName,$ReceiverEmail,$Subject,$Message,$SenderName,$SenderEmail,$CcEmail);
//    if($mail){
//
//        echo "Email Send Sucessfully  ".$ReceiverName."  ". $ReceiverEmail;
//    }
	}

}


function sendMail($ReceiverName, $ReceiverEmail,$Subject,$Message, $SenderName, $SenderEmail, $CcEmail){
	require 'PHPMailer/PHPMailerAutoload.php';
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
	$mail->SMTPSecure = "ssl";
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
	//$mail->addBCC('bcc@example.com');


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

