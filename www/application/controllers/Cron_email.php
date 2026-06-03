<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_email extends Initialize {
	protected function rule() {

		$crd = array(
			'send_messages,send_text_message,temp_excel_create' => array(
				'rule' => '*'
			)
		);

		return array_merge($crd);
	}

	function send_messages() {
		$emails = $this->db
			->where('is_sent', 0)
			->limit(10)
			->get('es_emails_cron')
			->result();
		$this->load->helper('phpmailer');
		foreach ($emails as $email) {

			$title = (isset($email->from_name) && !is_null($email->from_name)) ? $email->from_name : PROJECT_NAME;
			

			$message_text = $this->load->view('email', array(
				'event_name' => $title,
				'message' => $email->message
			), true);

			$ReceiverName = $email->email;
			$ReceiverEmail = $email->email;
			$SenderName = $title;
			$SenderEmail = 'donotreply@exhibit.com.pk';
			$Subject = $email->subject;
			$Message = $message_text;
			$CcEmail = 'exhibit332@gmail.com';

			if($ReceiverEmail !=""){
				echo $mail = sendMail($ReceiverName,$ReceiverEmail,$Subject,$Message,$SenderName,$SenderEmail,$CcEmail);
			}

			$this->db
				->where('id', $email->id)
				->update('es_emails_cron', array(
					'is_sent' => 1,
					'sent_on' => date('Y-m-d H:i:s')
				));

			if ($email->type && $email->type == 'EVENT_INVITATION') {
				try {
					$booking = json_decode($email->data);

					if (isset($booking) && $booking->order_id) {
						$this->db
						->where('id', $booking->order_id)
						->update('es_exhibition_booking', array(
							'invitation_sent' => 1
						));
					}
				} catch (Exception $e) {

				}
			}
		}

		echo 'done';
		exit;
	}




	function send_text_message() {
		$messages = $this->db
			->where('is_send', 0)
			->limit(5)
			->get('es_sms_notification')
			->result();

		foreach ($messages as $message) {

			if ($this->funcs->send_sms($message->phone_number, $message->text_message)) {
				$this->db
					->where('id', $message->id)
					->update('es_sms_notification', array(
						'is_send' => 1,
						'send_on' => date('Y-m-d H:i:s'),
					));
			}

		}

		echo 'done';
		exit;
	}

	function temp_excel_create() {
		$data = $this->db
			->select('
			B.id,
			B.badge_type,
			B.full_name,
			B.nationality,
			IF(B.nationality="Pakistani", B.cnic, B.passport) as cnic_passport,
			B.barcode_data,
			B.mobile,
			C.company,
			CONCAT("'.base_url('client/').'", B.user_image) as user_image_link,
			B.print_on,
			')
			->where('B.exhibition_id', 13)
			->where('B.is_active', 1)
			->join('es_exhibition_booking as O', 'B.booking_id = O.id')
			->join('es_customers as C', 'O.customer_id = C.id')
			->from('es_exhibition_badges as B')
			->get()
			->result();

		$html = '<html>';
		$html .= '<meta http-equiv="Content-Type" content="text/html; charset=Windows-1252">';
		$html .= '<head>';
		$html .= '<style>
		*, body {
		background: transparent;
		}
		table, td {
			border: 1px solid #ccc;
		}
		</style>';
		$html .= '</head>';
		$html .= '<body>';

		$html .= '<table class="table table-bordered"><thead>';
		$html .= '<tr>';
		$html .= '<th>SN</th>';
		$html .= '<th>PersonID</th>';
		$html .= '<th>Badge</th>';
		$html .= '<th>First Name</th>';
		$html .= '<th>Last Name</th>';
		$html .= '<th>CNIC / Passport</th>';
		$html .= '<th>Badge Code</th>';
		$html .= '<th>Mobile</th>';
		$html .= '<th>OrgName</th>';
		$html .= '<th>ImagePath</th>';
		$html .= '<th>Badge Count</th>';
		$html .= '<th>Print Date</th>';
		$html .= '</tr>';
		$html .= '</tr></thead><tbody>';

		foreach ($data as $k => $d) {
			$html .= '<tr>';
			$html .= '<td>'.($k+1).'</td>';
			$html .= '<td>'.$d->id.'</td>';
			$html .= '<td>'.$d->badge_type.'</td>';
			$html .= '<td>'.$d->full_name.'</td>';
			$html .= '<td></td>';
			$html .= '<td>'.$d->cnic_passport.'</td>';
			$html .= '<td>'.$d->barcode_data.'</td>';
			$html .= '<td>'.$d->mobile.'</td>';
			$html .= '<td>'.$d->company.'</td>';
			$html .= '<td>'.$d->user_image_link.'</td>';
			$html .= '<td></td>';
			$html .= '<td>'.$d->print_on.'</td>';
			$html .= '</tr>';
		}

		$html .= "</tbody>";
		$html .= "</table>";
		$html .= "</body>";
		$html .= "</html>";

		header("Content-Type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment;filename=badge_report".date('YmdHis').".xls");
		header("Cache-Control: max-age=0");
		echo $html;
		die();


	}
}