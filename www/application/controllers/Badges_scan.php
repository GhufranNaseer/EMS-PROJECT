<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Badges_scan extends MY_Controller {

	protected function rule () {
		//$this->activateRightsSystem();
		$common =  array (
			'scan,scan_id' => array (
				'rule' => '*'
			)
		);

		//$this->load->model('usermdl');
		return array_merge($common);
	}


	function scan() {
		$this->load->view('badges_scan');
	}

	function scan_id(){

		$badge_id = $this->input->post('id');
		$badge_id = str_replace(' ', '', $badge_id);
		// format badge id
		/*if (strpos($badge_id, '-') >= 0) {
			$badge_id = explode('-', $badge_id);
			$badge_id = end($badge_id);
		}
		$badge_id = trim($badge_id);*/

		//$badge_id = substr($badge_id, 6, strlen($badge_id));

		$html="";

		//$data = $this->db
		//	->select('C.company, B.*')
			//->where('B.id',$badge_id)
		//	->where('B.barcode_data',$badge_id)
		//	->join('es_exhibition_booking as O', 'B.booking_id = O.id')
		//	->join('es_customers as C', 'O.customer_id = C.id')
		//	->get('es_exhibition_badges as B')
		//	->row();
			
		$data = $this->db
			->select('C.company, B.*')
			//->where('B.id', $badge_id)
			->where('B.barcode_data', $badge_id)
			->join('es_exhibition_booking as O', 'B.booking_id = O.id', 'LEFT')
			->join('es_customers as C', 'O.customer_id = C.id', 'LEFT')
			->get('es_exhibition_badges as B')
			->row();

		if ($data) {

			$html .= '<tr class="bg-green text-center"><td colspan="2"><h2>FOUND</h2></td></tr>';
			$html .= '<tr>';
			$html .= '<td style="width: 80%;">
                <strong style="margin-bottom: 3px;">'. $data->full_name .'</strong><br>
                '.$data->company.'<br>
                <br>';

                if ($data->nationality == 'Pakistani') {
                    $html .= 'CNIC# ' . $data->cnic . ' - ' . $data->nationality . ' - ID #' . $data->id;
                } else {
					$html .= 'Passport# ' . $data->passport . ' - ' . $data->nationality . ' - ID #' . $data->id;
                }

            $html .= '</td>
			<td style="width: 20%;" class="text-right">';

                if (!is_null($data->user_image)) {
					$html .= '<img src="'.base_url('client/' . $data->user_image).'" alt="" style="width: 100px; height: 100px">';
                } else {
					//$html .= '<img src="'.base_url($qr_link).'" alt="" style="width: 50px; height: 50px">';
                }

			$html .= '</td>';
			$html .= "</tr>";

		} else {
			// not found
			$html .= '<tr class="bg-red text-center"><td colspan="2"><h4>NOT FOUND</h4></td></tr>';
		}


		echo $html;
		exit;
	}

}