<?php
class Funcs extends CI_Model
{

	function send_email_bk($to_email, $subject = '', $message = "", $event_name) {
		$config = Array(
			'protocol'  => 'smtp',
			'smtp_host' => 'mail.exhibit.com.pk',
			'smtp_port' => 465,
			'smtp_user' => 'donotreply@exhibit.com.pk',
			'smtp_pass' => 'badar@123',
			'charset'   => 'utf-8',
			'mailtype'  => 'html'
		);
		$this->load->library('email');
		$this->email->initialize($config);
		$this->email->set_mailtype("html");
		$this->email->set_newline("\r\n");
		$event_name = (!$event_name) ? PROJECT_NAME : $event_name;

		$message_text = $this->load->view('email', array(
			'event_name' => $event_name,
			'message' => $message
		), true);

		$this->email->clear();
		//$this->email->from('no-reply@'.EMAIL_DOMAIN_NAME );
		$this->email->from('donotreply@exhibit.com.pk');
		$this->email->to($to_email);
		$this->email->subject($subject);
		$this->email->message($message_text);

		//Send mail
		if ($this->email->send()) {
			//echo $this->email->print_debugger();
			//echo 'email send';
			return true;
		} else {
			//echo $this->email->print_debugger();
			//echo 'not email send';
			return false;
		}
	}

	function send_email_x($to_email, $subject = '', $message = "", $event_name) {

		$event_name = (!$event_name) ? PROJECT_NAME : $event_name;

		$message_text = $this->load->view('email', array(
			'event_name' => $event_name,
			'message' => $message
		), true);

		$id = 'myform' . uniqid(time()) . rand(0, 9999);
		echo '<form method="post" action="http://ideas.exhibit.com.pk/postmail_SSL.php" id="'.$id.'">
			<input type="hidden" name="rName" value="'.$to_email.'">
			<input type="hidden" name="rEmail" value="'.$to_email.'">
			<input type="hidden" name="sName" value="IDEAS 2018">
			<input type="hidden" name="sEmail" value="donotreply@exhibit.com.pk">
			<input type="hidden" name="subject" value="'.$subject.'">
			<input type="hidden" name="message" value="fsfsdfd">
			<input type="hidden" name="ccEmail" value="">
			<input type="hidden" name="post" value="1">
			</form>';

		echo '<script>document.getElementById("'.$id.'").submit();</script>';

	}

	function send_email($to_email, $subject = '', $message = "", $event_name) {
		require_once 'PHPMailer/PHPMailerAutoload.php';

		//require_once(APPPATH."models/PHPMailer/class.smtp.php");
		//require_once(APPPATH."models/PHPMailer/class.phpmailer.php");

		$mail = new PHPMailer();
		//Set Enable SMTP debugging.
		$mail->SMTPDebug = 1;
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
		$mail->From = 'donotreply@exhibit.com.pk';
		// Change Sender Name  //
		$mail->FromName = 'IDEAS 2018';


		// Set Receiver Information //
		$mail->addAddress($to_email,$to_email);

		$CcEmail = "";
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

		$event_name = (!$event_name) ? PROJECT_NAME : $event_name;

		/*$event_name = (!$event_name) ? PROJECT_NAME : $event_name;

		$message_text = $this->load->view('email', array(
			'event_name' => $event_name,
			'message' => $message
		), true);*/

		$mail->Subject = $subject;
		$mail->Body = 'Test message from cron';
		$mail->AltBody = "This is the plain text version of the email content";


		if(!$mail->send()) {
			echo "Mailer Error: " . $mail->ErrorInfo;
		}
		else
		{
			echo "Message has been sent successfully ".$to_email;
		}

	}

	function make_image_string($images_array = array()) {
		$images = array();
		foreach ($images_array as $img) {
			if (strpos($img, 'uploaded:') === false) {
				$images[] = $img;
			} else {
				$images[] = explode('uploaded:', $img)[1];
			}
		}

		return implode(',', $images);
	}

	function set_input_data_list($type, $data) {
		if ($data && $data != null && $data != '') {
			$check = $this->db
				->where('type', $type)
				->where('data', $data)
				->count_all_results('input_data_list');
			if ($check == 0) {
				$this->db
					->insert('input_data_list', array(
						'type' => $type,
						'data' => $data
					));
			}
		}
		return true;
	}

	function get_input_data_list($type) {
		$result = $this->db
			->where('type', $type)
			->order_by('data', 'ASC')
			->get('input_data_list')
			->result();

		return $result;
	}

	function print_input_data_list($type) {
		$result = $this->get_input_data_list($type);

		$html = '';
		if ($result) {
			foreach ($result as $item) {
				echo '<option value="'.$item->data.'">'.$item->data.'</option>';
			}
		}

		return $html;
	}

	function convertNumberToWord($num = false) {
		$num = str_replace(array(',', ' '), '' , trim($num));
		if(! $num) {
			return false;
		}
		$num = (int) $num;
		$words = array();
		$list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
			'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
		);
		$list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
		$list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
			'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
			'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
		);
		$num_length = strlen($num);
		$levels = (int) (($num_length + 2) / 3);
		$max_length = $levels * 3;
		$num = substr('00' . $num, -$max_length);
		$num_levels = str_split($num, 3);
		for ($i = 0; $i < count($num_levels); $i++) {
			$levels--;
			$hundreds = (int) ($num_levels[$i] / 100);
			$hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
			$tens = (int) ($num_levels[$i] % 100);
			$singles = '';
			if ( $tens < 20 ) {
				$tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
			} else {
				$tens = (int)($tens / 10);
				$tens = ' ' . $list2[$tens] . ' ';
				$singles = (int) ($num_levels[$i] % 10);
				$singles = ' ' . $list1[$singles] . ' ';
			}
			$words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
		} //end for loop
		$commas = count($words);
		if ($commas > 1) {
			$commas = $commas - 1;
		}
		return strtoupper(implode(' ', $words));
	}
	
	function send_sms($phone_number, $msg = '') {
		
		if (!$phone_number) {
			return false;
		}
		
		$result = @file_get_contents("http://api.m4sms.com/api/Sendsms?id=ideas72&pass=1172&mobile=".urlencode($phone_number)."&brandname=IDEAS&msg=" . urlencode($msg), null);

		if ($result !== false) {
			return true;
		} else {
			return false;
		}
	}
}