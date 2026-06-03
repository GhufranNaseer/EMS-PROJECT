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
		
		// https://bsms.its.com.pk/api.php?key=be6596824b3f1995e9394c005238bd16&msgdata=test&receiver=923331347148&sender=BADAR EXPO

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