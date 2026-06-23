<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_email extends Initialize {
	protected function rule() {

		$crd = array(
			'send_messages,send_text_message,temp_excel_create,send_thank_you_emails' => array(
				'rule' => '*'
			)
		);

		return array_merge($crd);
	}

	function send_messages() {
		$this->load->model('email_configuration_model');
		$this->load->helper('phpmailer');

		$settings = $this->email_configuration_model->get_mailer_settings();
		$limit = max(1, (int) $settings['emails_per_cron']);
		$retry_attempts = max(1, (int) $settings['retry_attempts']);

		$this->db
			->where('status', 'processing')
			->where('processing_started_on <', date('Y-m-d H:i:s', strtotime('-30 minutes')))
			->update('es_emails_cron', array(
				'status' => 'pending',
				'processing_started_on' => null,
				'updated_on' => date('Y-m-d H:i:s')
			));

		$emails = $this->email_configuration_model->get_pending_emails($limit, $retry_attempts);

		foreach ($emails as $email) {
			if (!$this->email_configuration_model->mark_processing($email->id)) {
				continue;
			}

			$title = (isset($email->from_name) && !is_null($email->from_name)) ? $email->from_name : PROJECT_NAME;
			

			$message_text = $this->load->view('email', array(
				'event_name' => $title,
				'message' => $email->message
			), true);

			$ReceiverName = $email->email;
			$ReceiverEmail = $email->email;
			$SenderName = $title;
			$SenderEmail = $settings['mail_from_email'];
			$Subject = $email->subject;
			$Message = $message_text;
			$CcEmail = '';
			$attempts = isset($email->attempts) ? ((int) $email->attempts + 1) : 1;
			$result = array('success' => false, 'error' => 'Receiver email is empty', 'debug' => '');

			if($ReceiverEmail !=""){
				$result = sendMail($ReceiverName, $ReceiverEmail, $Subject, $Message, $SenderName, $SenderEmail, $CcEmail, array(
					'settings' => $settings,
					'echo' => true,
					'debug' => $settings['smtp_debug'] === 'yes',
				));
			}

			if ($result['success']) {
				$this->email_configuration_model->mark_sent($email->id);

				$this->email_configuration_model->log_email(array(
					'email_queue_id' => $email->id,
					'email_type' => $email->type,
					'recipient_email' => $ReceiverEmail,
					'subject' => $Subject,
					'driver' => $settings['mail_driver'],
					'status' => 'sent',
					'attempts' => $attempts,
					'debug_message' => $settings['smtp_debug'] === 'yes' ? $result['debug'] : null,
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

				if ($email->type && $email->type == 'THANK_YOU_EXHIBITOR') {
					try {
						$log_info = json_decode($email->data);
						if (isset($log_info) && isset($log_info->event_id) && isset($log_info->exhibitor_id)) {
							$this->db
								->where('event_id', $log_info->event_id)
								->where('exhibitor_id', $log_info->exhibitor_id)
								->update('es_event_email_log', array(
									'status' => 'sent',
									'sent_at' => date('Y-m-d H:i:s')
								));
						}
					} catch (Exception $e) {

					}
				}
			} else {
				$this->email_configuration_model->mark_failed($email->id, $result['error'], $retry_attempts);

				$this->email_configuration_model->log_email(array(
					'email_queue_id' => $email->id,
					'email_type' => $email->type,
					'recipient_email' => $ReceiverEmail,
					'subject' => $Subject,
					'driver' => $settings['mail_driver'],
					'status' => $attempts >= $retry_attempts ? 'failed' : 'retry',
					'attempts' => $attempts,
					'error_message' => $result['error'],
					'debug_message' => $settings['smtp_debug'] === 'yes' ? $result['debug'] : null,
				));

				if ($email->type && $email->type == 'THANK_YOU_EXHIBITOR') {
					try {
						$log_info = json_decode($email->data);
						if (isset($log_info) && isset($log_info->event_id) && isset($log_info->exhibitor_id)) {
							$status = $attempts >= $retry_attempts ? 'failed' : 'pending';
							$this->db
								->where('event_id', $log_info->event_id)
								->where('exhibitor_id', $log_info->exhibitor_id)
								->update('es_event_email_log', array(
									'status' => $status,
									'error_message' => $result['error']
								));
						}
					} catch (Exception $e) {

					}
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

	function send_thank_you_emails() {
		$this->load->model('email_configuration_model');
		$current_date = date('Y-m-d');
		$events = $this->db
			->where('is_deleted', 0)
			->where('booking_expire_date <', $current_date)
			->get('es_exhibitions')
			->result();

		$queued_count = 0;

		foreach ($events as $event) {
			// 1. Try to fetch event-specific template
			$template = $this->db
				->where('title', 'THANK_YOU_EXHIBITOR')
				->where('exhibition_id', $event->id)
				->get('email_template')
				->row();

			// 2. Fallback to global template if no event-specific template exists
			if (!$template) {
				$template = $this->db
					->where('title', 'THANK_YOU_EXHIBITOR')
					->where('(exhibition_id IS NULL OR exhibition_id = 0)', NULL, FALSE)
					->get('email_template')
					->row();
			}

			if (!$template) {
				continue;
			}

			$exhibitors = $this->db
				->select('B.id as booking_id, C.id as customer_id, C.name as exhibitor_name, C.company as exhibitor_company, C.email as exhibitor_email')
				->from('es_exhibition_booking as B')
				->join('es_customers as C', 'B.customer_id = C.id')
				->where('B.exhibition_id', $event->id)
				->where('B.is_approved', 1)
				->where('B.is_canceled', 0)
				->get()
				->result();

			foreach ($exhibitors as $exhibitor) {
				if (empty($exhibitor->exhibitor_email)) {
					continue;
				}

				$already_logged = $this->db
					->where('event_id', $event->id)
					->where('exhibitor_id', $exhibitor->customer_id)
					->where('template_id', $template->id)
					->count_all_results('es_event_email_log');

				if ($already_logged > 0) {
					continue;
				}

				$subject = $template->subject;
				$message = $template->message;

				$replacements = array(
					'{EVENT_NAME}' => $event->exhibition_title,
					'{EXHIBITOR_NAME}' => $exhibitor->exhibitor_name,
					'{EXHIBITOR_COMPANY}' => $exhibitor->exhibitor_company,
				);

				foreach ($replacements as $key => $val) {
					$subject = str_replace($key, $val, $subject);
					$message = str_replace($key, $val, $message);
				}

				$this->db->trans_start();

				$log_data = array(
					'event_id' => $event->id,
					'exhibitor_id' => $exhibitor->customer_id,
					'template_id' => $template->id,
					'status' => 'pending',
					'created_on' => date('Y-m-d H:i:s'),
				);
				$this->db->insert('es_event_email_log', $log_data);

				$queue_data = array(
					'type' => 'THANK_YOU_EXHIBITOR',
					'data' => json_encode(array(
						'event_id' => $event->id,
						'exhibitor_id' => $exhibitor->customer_id,
					)),
					'from_name' => $event->exhibition_title,
					'email' => $exhibitor->exhibitor_email,
					'subject' => $subject,
					'message' => $message,
					'created_on' => date('Y-m-d H:i:s'),
				);
				$this->db->insert('es_emails_cron', $queue_data);

				$this->db->trans_complete();
				$queued_count++;
			}
		}

		echo 'Queued ' . $queued_count . ' thank you emails.';
		exit;
	}
}
