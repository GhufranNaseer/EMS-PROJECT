<?php
class Funcs extends CI_Model
{

	function send_email($to_email, $subject = '', $message = "", $event_name) {
		$this->load->library('email');
		$this->email->set_mailtype("html");

		$event_name = (!$event_name) ? PROJECT_NAME : $event_name;

		$message_text = $this->load->view('email', array(
			'event_name' => $event_name,
			'message' => $message
		), true);

		$this->email->clear();
		$this->email->from('no-reply@'.EMAIL_DOMAIN_NAME );
		$this->email->to($to_email);
		$this->email->subject($subject);
		$this->email->message($message_text);

		//Send mail
		if ($this->email->send()) {
			return true;
		} else {
			return false;
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

	function print_input_data_list($type, $selected = '') {
		$result = $this->get_input_data_list($type);

		$html = '';
		if ($result) {
			foreach ($result as $item) {
				$s = ($selected == $item->data) ? 'selected' : '';
				echo '<option value="'.$item->data.'" '.$s.'>'.$item->data.'</option>';
			}
		}

		return $html;
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