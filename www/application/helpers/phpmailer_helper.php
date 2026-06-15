<?php

function sendMail($ReceiverName, $ReceiverEmail, $Subject, $Message, $SenderName, $SenderEmail, $CcEmail, $options = array())
{
	require_once 'PHPMailer/PHPMailerAutoload.php';

	$CI =& get_instance();
	$settings = isset($options['settings']) ? $options['settings'] : null;
	$echo = array_key_exists('echo', $options) ? (bool) $options['echo'] : true;
	$debug = array_key_exists('debug', $options) ? (bool) $options['debug'] : false;

	if (!is_array($settings)) {
		$CI->load->model('email_configuration_model');
		$settings = $CI->email_configuration_model->get_mailer_settings();
	}

	$mail = new PHPMailer();
	$debugOutput = '';

	if ($settings['mail_driver'] === 'smtp') {
		$mail->isSMTP();
		$mail->Host = ($settings['mail_encryption'] === 'ssl' && strpos($settings['mail_host'], 'ssl://') !== 0)
			? 'ssl://' . $settings['mail_host']
			: $settings['mail_host'];
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);
		$mail->SMTPAuth = true;
		$mail->Username = $settings['mail_username'];
		$mail->Password = $settings['mail_password'];

		if ($settings['mail_encryption'] !== 'starttls') {
			$mail->SMTPSecure = $settings['mail_encryption'];
		} else {
			$mail->SMTPSecure = 'tls';
			$mail->SMTPAutoTLS = true;
		}

		$mail->Port = (int) $settings['mail_port'];
	} else {
		$mail->isMail();
	}

	if ($debug) {
		$mail->SMTPDebug = 2;
		$mail->Debugoutput = function ($str, $level) use (&$debugOutput) {
			$debugOutput .= trim($str) . "\n";
		};
	}

	$mail->From = !empty($SenderEmail) ? $SenderEmail : $settings['mail_from_email'];
	$mail->FromName = !empty($SenderName) ? $SenderName : $settings['mail_from_name'];
	$mail->addAddress($ReceiverEmail, $ReceiverName);

	if ($CcEmail != "") {
		$mail->addCC($CcEmail);
	}

	$mail->addReplyTo($settings['mail_from_email'], $settings['mail_from_name']);
	$mail->isHTML(true);
	$mail->Subject = $Subject;
	$mail->Body = $Message;
	$mail->AltBody = "This is the plain text version of the email content";

	$success = $mail->send();
	$error = $success ? '' : $mail->ErrorInfo;

	$result = array(
		'success' => $success,
		'message' => $success ? "Message has been sent successfully " . $ReceiverName . "  " . $ReceiverEmail : "Mailer Error: " . $error,
		'error' => $error,
		'debug' => $debugOutput,
	);

	if ($echo) {
		echo $result['message'];
	}

	return $result;
}

