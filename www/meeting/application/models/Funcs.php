<?php
class Funcs extends CI_Model
{



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

		$params = array(
			'key' => 'be6596824b3f1995e9394c005238bd16',
			'msgdata' => $msg,
			'receiver' => $phone_number,
			'sender' => 'BADAR EXPO',
		);

		// $result = @file_get_contents("http://api.m4sms.com/api/Sendsms?id=ideas72&pass=1172&mobile=".urlencode($phone_number)."&brandname=IDEAS&msg=" . urlencode($msg), null);
		$result = @file_get_contents("https://bsms.its.com.pk/api.php?" . http_build_query($params), null);

		if ($result !== false) {
			return true;
		} else {
			return false;
		}
	}
}