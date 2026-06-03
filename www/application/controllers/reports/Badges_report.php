<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Badges_report extends MY_Controller {
	protected function rule() {
		$this->activateRightsSystem();
		$crd = array(
			'crd_list,
			crd_list_datatable,
			crd_badge_list,
			badges_edit,
			crd_badge_list_datatable,
			crd_invitation_list,
			crd_invitation_list_datatable,
			print_badge,
			hold_badge,
			reset_badge_print,
			badge_edit_submit,
			badge_edit_validate,
			pdf_export_report,
			' => array(
				'rule' => '@'
			)
		);

		$this->load->model('usermdl');
		return array_merge($crd);
	}

	protected function checkEditId() {
		$id = $this->input->get('id');
		if (is_null($id))
			show_404();
		$id = $this->db->escape($id);
		$key = config_item('encryption_key');
		$key = $this->db->escape($key);
		$r = $this->db
			->where("MD5(CONCAT($key,`id`)) = $id")
			->get('es_exhibitions');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();
	}

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('badges_report/list');
	}

	function crd_list_datatable()
	{
		$this->load->library('datatables');
		$this->datatables
			->select('E.id,
				  E.exhibition_title,
				  L.location_title				  
				  ', false)
			->add_column('total_orders', function ($row) {
				return $this->db
					->where('exhibition_id', $row['id'])
					->where('is_canceled', 0)
					->count_all_results('es_exhibition_booking');
			}, NULL)
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'badges-report.html?id=' . urlencode(myid($id)) . '">Badges Report</a>';
				$html .= ' | <a href="' . base_url() . 'invitation-report.html?id=' . urlencode(myid($id)) . '">Invitation Report</a>';

				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('E.is_deleted', 0)
			->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
			->from('es_exhibitions as E');

		print ($this->datatables->generate());
	}


	function crd_badge_list() {
		
		$this->load->view('includes/after_login/head');
		$this->load->view('badges_report/badges_list');
	}

	function crd_badge_list_datatable() {
		$this->checkEditId();

		$this->load->library('datatables');
		$this->datatables
			->select('B.id,
            		B.exhibition_id,
            		B.booking_id,
            		IF(B.nationality="Pakistani", "Pakistan", B.nationality) as country,
                    C.company,
                    B.full_name,
                    B.designation,
                    B.cnic,
                    B.passport,
                    CONCAT("<img src=\''.base_url('client/').'", B.user_image, "\' height=\'40px\' />") as user_image, 
                    CONCAT("'.base_url('client/').'", B.user_image) as user_image_link, 
                    B.barcode_data,
                    DATE_FORMAT(B.created_on, "%d, %b %Y") as created_on,
                    B.collection_person_name,
                    B.collection_person_cnic,
                    B.is_printed,
                    B.is_hold,
                    IF(B.is_hold=1, "<span class=\"label label-info\">Hold</span>",IF(B.is_printed=1, "<span class=\"label label-success\">Printed</span>" , "<span class=\"label label-danger\">Not Printed</span>" )) as print_status
				  ', false)

			->unset_column('user_image_link')
			->unset_column('B.exhibition_id')
			->unset_column('B.booking_id')
			->unset_column('B.is_printed')
            ->unset_column('B.is_hold')

			->add_column('hall_name', function ($row) {
				$halls = $this->db
					->where('S.booking_id', $row['booking_id'])
					->join('es_location_halls as C', 'S.hall_id = C.id')
					->get('es_exhibition_booking_stalls as S')
					->result();
				$hall_name="";
				$check_halls = array();
				foreach ($halls as $hall)
				{
					if(in_array($hall->hall_title, $check_halls)) {
						continue;
					}
					$hall_name .=$hall->hall_title.' ,';
					$check_halls[] = $hall->hall_title;
				}
				return rtrim($hall_name,',');
			}, NULL, 4)

			->add_column('stall_name', function ($row) {
				$stalls = $this->db
					->where('S.booking_id', $row['booking_id'])
					->join('es_exhibition_stalls as C', 'S.stall_id = C.id')
					->get('es_exhibition_booking_stalls as S')
					->result();
				$stall_name="";
				foreach ($stalls as $stall)
				{
					$stall_name .=$stall->stall_name.' ,';
				}
				return rtrim($stall_name,',');
			}, NULL, 5)

			->add_column('action', function ($row) {
				$badge_id = $row['id'];

				//print_r($row);die();
				if ($row['is_printed'] == 0 && $row['is_hold'] == 0) {
					$html = '<a href="'.base_url('view-badges.html?id=' . myid($badge_id)).'" target="_blank">View Badge</a> | ';
					$html .= '<a href="'.base_url('hold-badges.html?id=' . myid($badge_id)).'&event_id='.myid($this->formdata->id).'">Hold Badge</a>';
				} else {
					$html = '<a href="'.base_url('reset-badges-print.html?id=' . myid($badge_id)).'&event_id='.myid($this->formdata->id).'" onclick="return confirm(\'Are you sure you want to reset badge status?\')">Reset Print</a>';
				}
				return $html;
			}, NULL)
			->add_column('edit', function ($row) {
				$badge_id = $row['id'];
				$html = '';
				if ($row['is_printed'] == 0 && $row['is_hold'] == 0) {
					$html = '<a href="' . base_url() . 'badges-edit.html?id=' . myid($badge_id) . '">Edit</a>';
				}
				return "<center>{$html}</center>";
			}, NULL)
            -> add_column('check', function ($row){
                $badge_id = $row['id'];
                if ($row['is_printed'] == 0 && $row['is_hold'] == 0) {
                $htmi='<input type="checkbox" class="check_box" value="'.$badge_id.'">';
                }
                else{
                    $htmi='';
                }
                return $htmi;
            } , NULL)



			->where('B.exhibition_id', $this->formdata->id)
			->where('B.is_active', 1)
			->join('es_exhibition_booking as O', 'B.booking_id = O.id')
			->join('es_customers as C', 'O.customer_id = C.id')
			->from('es_exhibition_badges as B');

		if ($this->input->get('filter_badge')) {
            $this->datatables->where('B.badge_type', $this->input->get('filter_badge'));
        }
        if ($this->input->get('filter_customer')) {
            $this->datatables->where('O.customer_id', $this->input->get('filter_customer'));
        }

        if (!$this->input->is_ajax_request()) {
			$headers = explode(',', $this->input->get('headers'));
			$headers[] = 'Image Link';
			$_GET['headers'] = implode(',', $headers);

			$this->datatables->add_column('image_link', function ($row) {
				return $row['user_image_link'];
			}, NULL);
		}

		print ($this->datatables->generate());
	}


	function crd_invitation_list() {
		$this->checkEditId();

		$event_invitation_types = $this->db
			->select('invitation_type')
			->where('exhibition_id', $this->formdata->id)
			->where('is_active', 1)
			->group_by('invitation_type')
			->get('es_package_badges')
			->result();

		$available_invitation_types = array();
		foreach ($event_invitation_types as $t) {
			$available_invitation_types[] = $t->invitation_type;
		}


		$this->load->view('includes/after_login/head');
		$this->load->view('badges_report/invitation_list', array(
			'available_invitation_types' => $available_invitation_types
		));
	}

	function crd_invitation_list_datatable() {
		$this->checkEditId();

		$event_invitation_types = $this->db
			->select('invitation_type')
			->where('exhibition_id', $this->formdata->id)
			->where('is_active', 1)
			->group_by('invitation_type')
			->get('es_package_badges')
			->result();

		$available_invitation_types = array();
		foreach ($event_invitation_types as $t) {
			$available_invitation_types[] = $t->invitation_type;
		}

		// $columns = [
		// 	'b.id            AS id',
		// 	'b.exhibition_id AS exhibition_id',
		// 	'b.booking_id    AS booking_id',
		// 	'b.full_name     AS full_name',
		// 	'b.designation   AS designation',
		// 	'b.cnic          AS cnic',
		// 	'b.passport      AS passport',
		// 	'b.mobile        AS mobile',
		// 	'c.company       AS company',
		// 	'c.address       AS company_address',
		// 	'(SELECT created_on FROM es_exhibition_badges_invitation WHERE badge_id = B.id GROUP BY badge_id ORDER BY created_on DESC) AS last_update_date'
		// ];

		// foreach ($available_invitation_types as $invitation_type) {
		// 	if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == $invitation_type) {
		// 		$columns[] = "IF(((SELECT COUNT(0) FROM `es_exhibition_badges_invitation` WHERE ((`es_exhibition_badges_invitation`.`badge_id` = `b`.`id`) AND (`es_exhibition_badges_invitation`.`invitation_type` = '".$invitation_type."'))) > 0),1,0) AS `has_".$invitation_type."`";
		// 	}
		// }
		// echo '<pre>'; print_r(implode(', ', $columns)); die;

		$this->load->library('datatables');
		$this->datatables
		->select('
				b.id            AS id,
				b.exhibition_id AS exhibition_id,
			  	b.booking_id    AS booking_id,
				b.full_name     AS full_name,
				b.designation   AS designation,
				b.cnic          AS cnic,
				b.passport      AS passport,
				b.mobile        AS mobile,
				c.company       AS company,
				c.address       AS company_address,
				(SELECT created_on FROM es_exhibition_badges_invitation WHERE badge_id = b.id GROUP BY badge_id ORDER BY created_on DESC) AS last_update_date
			', false)
			->unset_column('exhibition_id')
			->unset_column('booking_id')
			->unset_column('last_update_date')
			->where('b.exhibition_id', $this->formdata->id)
			->where('b.is_active', 1)
			->join('es_exhibition_booking as o', 'b.booking_id = o.id', 'LEFT')
			->join('es_customers as c', 'o.customer_id = c.id', 'LEFT')
			->from('es_exhibition_badges as b');
			
			$this->datatables->where('(SELECT COUNT(*) FROM es_exhibition_badges_invitation 
                            WHERE badge_id = b.id) > 0');

		$this->datatables->add_column('last_update_date', function ($row) {
			$date = $row['last_update_date'];
			if (!$date) return '-';

			return date('d, M Y', strtotime($date));
		}, NULL);

		foreach ($available_invitation_types as $invitation_type) {
			if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == $invitation_type) {
				$this->datatables->add_column($invitation_type, function ($row) use ($invitation_type) {
					$check = $this->db
						->where('es_exhibition_badges_invitation.badge_id', $row['id'])
						->where('es_exhibition_badges_invitation.invitation_type', $invitation_type)
						->count_all_results('es_exhibition_badges_invitation');
					
					$html = ($check > 0) ? '<i class="fa fa-check text-green"><span>Yes</span></i>' : '<i class="fa fa-times text-red"><span>No</span></i>';

					return '<div class="text-center">' . $html . '</div>';
				}, NULL);
			}
		}

		if (!$this->input->is_ajax_request()) {
			$this->db->order_by('company', 'ASC');
		}

		print ($this->datatables->generate());
		die;

		// ***** BOTTOM CODE AND THIS VIEW IS NOW NOT IN USE *****

		/*
		 CREATE VIEW invitation_list_datatable AS
			SELECT
			  `b`.`id`            AS `id`,
			  `b`.`exhibition_id` AS `exhibition_id`,
			  `b`.`booking_id`    AS `booking_id`,
			  `c`.`company`       AS `company`,
			  `c`.`address`       AS `company_address`,
			  `b`.`full_name`     AS `full_name`,
			  `b`.`designation`   AS `designation`,
			  `b`.`mobile`        AS `mobile`,
			  `b`.`cnic`          AS `cnic`,
			  `b`.`passport`      AS `passport`,
			  `b`.`is_active`     AS `is_active`,
			  (SELECT created_on FROM es_exhibition_badges_invitation WHERE badge_id = B.id GROUP BY badge_id ORDER BY created_on DESC) AS last_update_date,
			  IF(((SELECT COUNT(0) FROM `es_exhibition_badges_invitation` WHERE ((`es_exhibition_badges_invitation`.`badge_id` = `b`.`id`) AND (`es_exhibition_badges_invitation`.`invitation_type` = 'inauguration'))) > 0),1,0) AS `has_inauguration`,
			  IF(((SELECT COUNT(0) FROM `es_exhibition_badges_invitation` WHERE ((`es_exhibition_badges_invitation`.`badge_id` = `b`.`id`) AND (`es_exhibition_badges_invitation`.`invitation_type` = 'seminar'))) > 0),1,0) AS `has_seminar`,
			  IF(((SELECT COUNT(0) FROM `es_exhibition_badges_invitation` WHERE ((`es_exhibition_badges_invitation`.`badge_id` = `b`.`id`) AND (`es_exhibition_badges_invitation`.`invitation_type` = 'sideline_conference'))) > 0),1,0) AS `has_sideline_conference`,
			  IF(((SELECT COUNT(0) FROM `es_exhibition_badges_invitation` WHERE ((`es_exhibition_badges_invitation`.`badge_id` = `b`.`id`) AND (`es_exhibition_badges_invitation`.`invitation_type` = 'governor_reception'))) > 0),1,0) AS `has_governor_reception`,
			  IF(((SELECT COUNT(0) FROM `es_exhibition_badges_invitation` WHERE ((`es_exhibition_badges_invitation`.`badge_id` = `b`.`id`) AND (`es_exhibition_badges_invitation`.`invitation_type` = 'gala_dinner'))) > 0),1,0) AS `has_gala_dinner`,
			  IF(((SELECT COUNT(0) FROM `es_exhibition_badges_invitation` WHERE ((`es_exhibition_badges_invitation`.`badge_id` = `b`.`id`) AND (`es_exhibition_badges_invitation`.`invitation_type` = 'closing_ceremony'))) > 0),1,0) AS `has_closing_ceremony`,
			  IF(((SELECT COUNT(0) FROM `es_exhibition_badges_invitation` WHERE ((`es_exhibition_badges_invitation`.`badge_id` = `b`.`id`) AND (`es_exhibition_badges_invitation`.`invitation_type` = 'karachi_air_show'))) > 0),1,0) AS `has_karachi_air_show`,
			  IF(((SELECT COUNT(0) FROM `es_exhibition_badges_invitation` WHERE ((`es_exhibition_badges_invitation`.`badge_id` = `b`.`id`) AND (`es_exhibition_badges_invitation`.`invitation_type` = 'cm_reception'))) > 0),1,0) AS `has_cm_reception`
			FROM ((`es_exhibition_badges` `b`
				LEFT JOIN `es_exhibition_booking` `o`
				  ON ((`b`.`booking_id` = `o`.`id`)))
			   LEFT JOIN `es_customers` `c`
				 ON ((`o`.`customer_id` = `c`.`id`)))
		 */

		$this->load->library('datatables');
		$this->datatables
			->select('id,
            		exhibition_id,
            		booking_id,
                    full_name,
                    designation,
                    cnic,
                    passport,
                    mobile,
                    company,                   
                    company_address,
                    DATE_FORMAT(last_update_date, "%d, %b %Y") as last_update_date,                   
                    has_inauguration,
					has_seminar,
					has_sideline_conference,
					has_governor_reception,
					has_gala_dinner,
					has_closing_ceremony,
					has_karachi_air_show,
					has_cm_reception,
				  ', false)

			->unset_column('exhibition_id')
			->unset_column('booking_id')
			->unset_column('has_inauguration')
			->unset_column('has_seminar')
			->unset_column('has_sideline_conference')
			->unset_column('has_governor_reception')
			->unset_column('has_gala_dinner')
			->unset_column('has_closing_ceremony')
			->unset_column('has_karachi_air_show')
			->unset_column('has_cm_reception')

			->where('exhibition_id', $this->formdata->id)
			->where('is_active', 1)
			->from('invitation_list_datatable');

		if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'inauguration') {
			$this->datatables->add_column('inauguration', function ($row) {
				$html = $row['has_inauguration'];
				$html = ($html == 1) ? '<i class="fa fa-check text-green"><span>Yes</span></i>' : '<i class="fa fa-times text-red"><span>No</span></i>';
				return '<div class="text-center">' . $html . '</div>';
			}, NULL);
		}
		if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'seminar') {
			$this->datatables->add_column('seminar', function ($row) {
				$html = $row['has_seminar'];
				$html = ($html == 1) ? '<i class="fa fa-check text-green"><span>Yes</span></i>' : '<i class="fa fa-times text-red"><span>No</span></i>';
				return '<div class="text-center">' . $html . '</div>';
			}, NULL);
		}
		if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'sideline_conference') {
			$this->datatables->add_column('sideline_conference', function ($row) {
				$html = $row['has_sideline_conference'];
				$html = ($html == 1) ? '<i class="fa fa-check text-green"><span>Yes</span></i>' : '<i class="fa fa-times text-red"><span>No</span></i>';
				return '<div class="text-center">' . $html . '</div>';
			}, NULL);
		}
		if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'governor_reception') {
			$this->datatables->add_column('governor_reception', function ($row) {
				$html = $row['has_governor_reception'];
				$html = ($html == 1) ? '<i class="fa fa-check text-green"><span>Yes</span></i>' : '<i class="fa fa-times text-red"><span>No</span></i>';
				return '<div class="text-center">' . $html . '</div>';
			}, NULL);
		}
		if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'gala_dinner') {
			$this->datatables->add_column('gala_dinner', function ($row) {
				$html = $row['has_gala_dinner'];
				$html = ($html == 1) ? '<i class="fa fa-check text-green"><span>Yes</span></i>' : '<i class="fa fa-times text-red"><span>No</span></i>';
				return '<div class="text-center">' . $html . '</div>';
			}, NULL);
		}
		if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'closing_ceremony') {
			$this->datatables->add_column('closing_ceremony', function ($row) {
				$html = $row['has_closing_ceremony'];
				$html = ($html == 1) ? '<i class="fa fa-check text-green"><span>Yes</span></i>' : '<i class="fa fa-times text-red"><span>No</span></i>';
				return '<div class="text-center">' . $html . '</div>';
			}, NULL);
		}
		if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'karachi_air_show') {
			$this->datatables->add_column('karachi_air_show', function ($row) {
				$html = $row['has_karachi_air_show'];
				$html = ($html == 1) ? '<i class="fa fa-check text-green"><span>Yes</span></i>' : '<i class="fa fa-times text-red"><span>No</span></i>';
				return '<div class="text-center">' . $html . '</div>';
			}, NULL);
		}
		if (!$this->input->get('filter_invitation') || $this->input->get('filter_invitation') == 'cm_reception') {
			$this->datatables->add_column('cm_reception', function ($row) {
				$html = $row['has_cm_reception'];
				$html = ($html == 1) ? '<i class="fa fa-check text-green"><span>Yes</span></i>' : '<i class="fa fa-times text-red"><span>No</span></i>';
				return '<div class="text-center">' . $html . '</div>';
			}, NULL);
		}

		if ($this->input->get('filter_invitation') && $this->input->get('filter_invitation') != '') {
			$this->datatables->where('has_' . $this->input->get('filter_invitation'), 1);
		} else {
			$condition = '(has_inauguration = 1 OR 
			has_seminar = 1 OR 
			has_sideline_conference = 1 OR 
			has_governor_reception = 1 OR 
			has_gala_dinner = 1 OR 
			has_closing_ceremony = 1 OR 
			has_karachi_air_show = 1 OR 
			has_cm_reception = 1)';
			$this->datatables->where($condition);
		}

		if (!$this->input->is_ajax_request()) {
			$this->db->order_by('company', 'ASC');
		}

		print ($this->datatables->generate());
	}

	private function download_qr($qr, $filename){
		if (!file_exists('uploads/qr-codes')) {
			mkdir('uploads/qr-codes');
		}
		$size = file_put_contents('uploads/qr-codes/' . $filename, file_get_contents($qr));
		if ($size > 0) {
			return true;
		} else {
			return false;
			// $this->download_qr($qr, $filename);
		}
	}

	private function save_barcode_image($data, $filename){
		if (!file_exists('uploads/qr-codes')) {
			mkdir('uploads/qr-codes');
		}
		$size = file_put_contents('uploads/qr-codes/' . $filename, $data);
		if ($size > 0) {
			return true;
		} else {
			return false;
			// $this->download_qr($qr, $filename);
		}
	}

	function print_badge() {
		$badge_id = $this->input->get('id');

		$this->badge = $this->db
			->where(mycolumn(), $badge_id)
			->get('es_exhibition_badges')
			->row();

		if ($this->badge->badge_type == 'trade_visitor') {
			$company = $this->badge->company;
		} else {
			$booking = $this->db
				->where('id', $this->badge->booking_id)
				->get('es_exhibition_booking')
				->row();

			$company_data = $this->db
				->where('id', $booking->customer_id)
				->get('es_customers')
				->row();

			$company = $company_data->company;
		}

		$this->load->library('QRGenerator');
		 $qr_data =  'BEGIN:VCARD
VERSION:3.0
N:'.$this->badge->full_name.';
TITLE:'.$this->badge->designation.'
ORG:'.$company.'
TEL;TYPE=WORK;CELL:+'.$this->badge->mobile.'
EMAIL;TYPE=WORK:'.$this->badge->email.'
END:VCARD';

		$qr = new QRGenerator($qr_data);
		$qr = $qr->generate();
		$output = uniqid(time()) . '.svg';
		if (!$this->download_qr($qr, $output)) {
			echo 'ERROR: Unable to generate QR Code.'; die;
		}

		if (!is_null($this->badge->barcode_data) && $this->badge->barcode_data != '') {
			$barcode_data = $this->badge->barcode_data;
		} else {
			//$barcode_data = ($this->badge->nationality == 'Pakistani') ? $this->badge->cnic : $this->badge->passport;
			//$barcode_data = $booking->customer_id.'-'.$this->badge->id;

			$alpha_seed = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'); // and any other characters
			$number_seed = str_split('0123456789'); // and any other characters

			$random_alpha = '';
			foreach (array_rand($alpha_seed, 2) as $k) $random_alpha .= $alpha_seed[$k]; // get 2 alpha characters

			$random_number = '';
			foreach (array_rand($number_seed, 4) as $k) $random_number .= $number_seed[$k]; // get 4 number characters

			$barcode_data = $random_number . '' . $random_alpha . '' .$this->badge->id;
			$this->badge->barcode_data = $barcode_data;
			$this->db
				->where('id', $this->badge->id)
				->update('es_exhibition_badges', array(
					'barcode_data' => $barcode_data
					// TODO: save qr_data, qr_image and barcode_image in this table also
				));
		}

		$this->load->helper ("barcode");
		$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
		$barcode = $generator->getBarcode($barcode_data, $generator::TYPE_CODE_128_B);
		$barcode_img = uniqid(time()) . '.png';
		if (!$this->save_barcode_image($barcode, $barcode_img)) {
			echo 'ERROR: Unable to generate barcode.'; die;
		}
		$html = $this->load->view('badges_report/print_badge', array(
			'qr_link' => 'uploads/qr-codes/' . $output,
			'barcode_link' => 'uploads/qr-codes/' . $barcode_img,
			// 'barcode_data' => $barcode,
			'company' => $company,
		), true);

		$this->db
			->where('id', $this->badge->id)
			->update('es_exhibition_badges', array(
				'is_printed' => 1,
				'print_on' => date('Y-m-d H:i:s')
			));

		//$card_size_w = 78.74;
		$card_size_w = 82.74;
		$card_size_h = 48.26;

		//echo $html; die();
		set_time_limit(0);
		$this->load->helper ("pdf-loader");
		try {
			$html2pdf = new HTML2PDF('L', array($card_size_w, $card_size_h), 'en', true, 'UTF-8', array(0, 0, 0, 0));
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->writeHTML($html);
			//$html2pdf->pdf->ImageSVG(
			//	$file= LOCAL_EXHIBIT_URL . ('uploads/qr-codes/' . $output), 
			//	$x=($card_size_w - 22), 
			//	$y=($card_size_h - 26), 
			//	$w=18, 
			//	$h=18, 
			//	$link='', 
			//	$align='', $palign='', $border=0, $fitonpage=false);
			$html2pdf->Output('print-badge-'.$badge_id.'.pdf');
		}
		catch(HTML2PDF_exception $e) {
			$msg = $e->getMessage ();
			//$msg = base64_encode($msg);
			//$msg = trim ($msg);
			print_r($msg);
		}
	}

	function hold_badge() {

		$badge_id = $this->input->get('id');
		$event_id = $this->input->get('event_id');

		$data = $this->db
			->where(mycolumn('id'), $badge_id)
			->get('es_exhibition_badges')
			->row();

		$this->db
			->where(mycolumn('id'), $badge_id)
			->update('es_exhibition_badges', array(
				'is_hold' => 1,
			));
		$this->session->set_flashdata('message', 'Badge has been hold successfully');

		if ($data->badge_type == 'trade_visitor') {
			redirect(base_url('trade-visitor-badges.html?id=' . $event_id));
		} else {
			redirect(base_url('badges-report.html?id=' . $event_id));
		}
	}

	function reset_badge_print() {
		$badge_id = $this->input->get('id');
		$event_id = $this->input->get('event_id');

		$data = $this->db
			->where(mycolumn('id'), $badge_id)
			->get('es_exhibition_badges')
			->row();

		$this->db
			->where(mycolumn(), $badge_id)
			->update('es_exhibition_badges', array(
				'is_printed' => 0,
				'is_hold' => 0,
				'print_on' => null
			));

		if ($data->badge_type == 'trade_visitor') {
			redirect(base_url('trade-visitor-badges.html?id=' . $event_id));
		} else {
			redirect(base_url('badges-report.html?id=' . $event_id));
		}
	}

    function badges_edit() {

	    $data=	$this->db
            ->where(mycolumn('id'), $this->input->get('id'))
            ->get('es_exhibition_badges')
            ->row();
        $this->load->view('includes/after_login/head');
        $this->load->view('badges_report/edit',array('data'=>$data));
    }
    function badge_edit_validate() {
        $this->form_validation->set_rules('name', 'name*Name', 'trim|required');
        $this->form_validation->set_rules('designation', 'designation*Designation', 'trim|required');
        $this->form_validation->set_rules('cnic', 'cnic*cnic', 'trim');
        $this->form_validation->set_rules('passport', 'passport*Passport', 'trim');
        $this->form_validation->set_rules('email', 'email*email', 'trim');
        $this->form_validation->set_rules('mobile', 'mobile*mobile', 'trim');
        $this->form_validation->set_rules('nationality', 'nationality*Nationality', 'trim|required');

        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else {
            return $this->common->doError(func_num_args(), "done", true);
        }
    }

    function badge_edit_submit() {
        if ($this->badge_edit_validate() !== true)
            show_404();
        $data = array(
            'full_name' => $this->input->post('name'),
            'designation' => $this->input->post('designation'),
            'cnic' => $this->input->post('cnic'),
            'passport' => $this->input->post('passport'),
            'email' => $this->input->post('email'),
            'mobile' => $this->input->post('mobile'),
            'nationality' => $this->input->post('nationality'),
            'collection_person_name' => $this->input->post('collection_person_name'),
            'collection_person_cnic' => $this->input->post('collection_person_cnic'),
            'collection_person_phone' => $this->input->post('collection_person_phone'),
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->where(mycolumn(), $this->input->get('id'))
            ->update('es_exhibition_badges');

		$data = $this->db
			->where(mycolumn(), $this->input->get('id'))
			->get('es_exhibition_badges')
			->row();


		if ($data->badge_type == 'visitor') {
			$old_form = $this->db
				->where('exhibition_id', $data->exhibition_id)
				->where('booking_id', $data->booking_id)
				->where('form_id', 10)
				->get('es_exhibition_booking_forms_data')
				->row();

			$all_data = json_decode($old_form->form_data);
			$new_data = array();
			foreach ($all_data->badge as $d) {
				if ($d->badge_id == $data->id) {
					$d->visitor_name = $data->full_name;
					$d->visitor_job_title = $data->designation;
					$d->visitor_mobile = $data->mobile;
					$d->visitor_country = $data->nationality;
					$d->visitor_cnic = $data->cnic;
					$d->visitor_email = $data->email;
					$d->visitor_passport = $data->passport;
				}

				$new_data[] = $d;
			}

			$all_data->badge = $new_data;

			$this->db
				->where('exhibition_id', $data->exhibition_id)
				->where('booking_id', $data->booking_id)
				->where('form_id', 10)
				->update('es_exhibition_booking_forms_data', array(
					'form_data' => json_encode($all_data),
					'modified_on' => date('Y-m-d H:i:s'),
				));
		}

        $this->db->trans_complete();
        $this->session->set_flashdata('message', 'Badges has been updated successfully');
        redirect(base_url('badges-report.html?id=' . myid($data->exhibition_id)));
    }

    function pdf_export_report() {
		$this->checkEditId();

		if ($this->input->get('filter_badge')) {
			$this->db->where('B.badge_type', $this->input->get('filter_badge'));
		}
		if ($this->input->get('filter_customer')) {
			$this->db->where('O.customer_id', $this->input->get('filter_customer'));
		}

		$data = $this->db
			->select('B.id,
            		B.exhibition_id,
            		B.booking_id,
            		IF(B.nationality="Pakistani", "Pakistan", B.nationality) as country,
                    C.company,
                    B.full_name,
                    B.designation,
                    B.cnic,
                    B.passport,
                    B.user_image,
                    B.barcode_data,
                    DATE_FORMAT(B.created_on, "%d, %b %Y") as created_on,
                    B.is_printed,
                    B.is_hold,
                    IF(B.is_hold=1, "<span class=\"label label-info\">Hold</span>",IF(B.is_printed=1, "<span class=\"label label-success\">Printed</span>" , "<span class=\"label label-danger\">Not Printed</span>" )) as print_status
				  ', false)
			->where('B.exhibition_id', $this->formdata->id)
			->where('B.is_active', 1)
			->join('es_exhibition_booking as O', 'B.booking_id = O.id')
			->join('es_customers as C', 'O.customer_id = C.id')
			->from('es_exhibition_badges as B')
			//->limit(200)
			->get()
			->result();


		$html = '<page>';
		$html .= '<style>
				* {
					-webkit-box-sizing: border-box;
					-moz-box-sizing: border-box;
					box-sizing: border-box;
					margin: 0;
					padding: 0;
				}
				page {
					font-family: Helvetica, Arial, sans-serif;
					font-size: 12px;
				}
				.text-right {
					text-align: right;
				}
				.text-left {
					text-align: left;
				}
				.text-center {
					text-align: center;
				}
				.table-no-border {
					margin: 0 !important;
				}
				table {
					border-spacing: 0;
					border-collapse: collapse;
					-webkit-box-sizing: border-box;
					-moz-box-sizing: border-box;
					box-sizing: border-box;
					width: 100%;
				}
				table th,
				table td {
					padding: 5px;
					word-break: break-all;
					white-space: normal;
					vertical-align: top;
				}
				.table-no-border,
				.table-no-border tr,
				.table-no-border th,
				.table-no-border td{
					border: none;
				}
				table, td, th {
					border: 1px solid #ccc;
				}
			</style>';


		$html .= '<table class="table table-bordered" style="width: 100%;"><thead>';


		$event_dates = $this->db
			->where('exhibition_id', $this->formdata->id)
			->get('es_exhibition_date')
			->result();

		if (!empty($event_dates)) {
			$html .= '<tr><th style="width: 100%" colspan="7" align="left"><h4>Report: Badges List</h4></th></tr>';
			$html .= '<tr><td style="width: 100%" colspan="7" align="right">Print Date: '.date('d/m/Y h:i A').'</td></tr>';
			$html .= '<tr><td style="width: 100%" colspan="7" align="right">Event Date: '.date('d/m/Y', strtotime($event_dates[0]->date)).' - '.date('d/m/Y', strtotime(end($event_dates)->date)).'</td></tr>';
			$html .= '<tr><td style="width: 100%" colspan="7"></td></tr>';
		}


		$html .= '<tr>';
		$html .= '<th style="width: 10%">S. No.</th>';
		$html .= '<th style="width: 20%">Name</th>';
		$html .= '<th style="width: 15%">Company</th>';
		$html .= '<th style="width: 15%">Country</th>';
		$html .= '<th style="width: 15%">CNIC</th>';
		$html .= '<th style="width: 15%">Passport</th>';
		$html .= '<th style="width: 10%">Picture</th>';

		$html .= '</tr></thead><tbody>';

		foreach ($data as $key => $r) {
			//$html .= '<img src="'.base_url('client/' . $r->user_image).'" width="15px" height="15px" style="width: 15px; height: 15px;"/>';
			$html .= '<tr>';
			$html .= '<td style="width: 10%">'.($key + 1).'</td>';
			$html .= '<td style="width: 20%">'.$r->full_name.'</td>';
			$html .= '<td style="width: 15%">'.$r->company.'</td>';
			$html .= '<td style="width: 15%">'.$r->country.'</td>';
			$html .= '<td style="width: 15%">'.$r->cnic.'</td>';
			$html .= '<td style="width: 15%">'.$r->passport.'</td>';
			if (false && $r->user_image && $r->user_image != null && $r->user_image != '') {
				//$html .= '<td width="10%">'.base_url('client/' . $r->user_image).'</td>';
				$html .= '<td style="width: 10%"><img src="'.LOCAL_EXHIBIT_URL . ('client/' . $r->user_image).'" width="30" height="30" /></td>';
			} else {
				$html .= '<td style="width: 10%"></td>';
			}
			$html .= '</tr>';
		}

		$html .= "</tbody>";
		$html .= "</table>";
		$html .= "</page>";

		//ini_set('memory_limit', '-1');
		set_time_limit(0);
		$this->load->helper ("pdf-loader");
		try {
			$html2pdf = new HTML2PDF('P', 'A4', 'en');
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->writeHTML($html);
			$html2pdf->Output('badge-report.pdf');
		}
		catch(HTML2PDF_exception $e) {
			$msg = $e->getMessage ();
			//$msg = base64_encode($msg);
			//$msg = trim ($msg);
			print_r($msg);
		}
	}
}