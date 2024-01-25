<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Print_job extends MY_Controller {
	protected function rule() {
		$this->activateRightsSystem();
		$crd = array(
			'crd_list,
			crd_list_datatable,
			booking_list,
			booking_list_datatable,
			print_badges,
			' => array(
				'rule' => '@'
			)
		);
		$this->load->model('usermdl');
		$this->myparent = base_url('print-job.html');

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
		$this->load->view('print_job/list');
	}

	function crd_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('E.id,
				  E.exhibition_title,
				  L.location_title				  
				  ', false)
			->add_column('total_orders', function ($row) {
				if ($this->userdata->user_group_id == SALES_PERSON) {
					$this->db->where('booked_by', $this->userdata->id);
				}
				return $this->db
					->where('exhibition_id', $row['id'])
					->where('is_canceled', 0)
					->count_all_results('es_exhibition_booking');
			}, NULL)
			->add_column('col_action', function ($row) {
				$id = $row['id'];

				$html = '<a href="' . base_url() . 'print-job-exhibitors.html?id=' . urlencode(myid($id)) . '">View</a>';

				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('E.is_deleted', 0)
			->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
			->from('es_exhibitions as E');

		print ($this->datatables->generate());
	}

	function booking_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('print_job/booking_list');
	}

	function booking_list_datatable() {
		$this->checkEditId();

		$this->load->library('datatables');
		$this->datatables
			->select('B.id,
				  C.company,			  
				  ', false)

			->add_column('total_badges', function ($row) {

				$total = $this->db
					->where('booking_id', $row['id'])
					->where('is_active', 1)
					->count_all_results('es_exhibition_badges');

				return $total;
			}, NULL)

			->add_column('printed_badges', function ($row) {
				$total = $this->db
					->where('booking_id', $row['id'])
					->where('is_active', 1)
					->where('is_printed', 1)
					->count_all_results('es_exhibition_badges');

				return $total;
			}, NULL)

			->add_column('remaining_badges', function ($row) {
				$total = $this->db
					->where('booking_id', $row['id'])
					->where('is_active', 1)
					->where('is_printed', 0)
					->count_all_results('es_exhibition_badges');

				return $total;
			}, NULL)

			->add_column('col_action', function ($row) {
				$id = $row['id'];

				$remaining = $this->db
					->where('booking_id', $row['id'])
					->where('is_active', 1)
					->where('is_printed', 0)
					->where('is_hold', 0)
					->count_all_results('es_exhibition_badges');
				$html = '';
				if ($remaining == 0) {
					$html = '<span class="text-success">All Printed</span>';
				}
				else if ($remaining <= 20) {
					$badges_ids = array();
					$badges = $this->db
						->where('booking_id', $id)
						->where('is_active', 1)
						->where('is_printed', 0)
						->where('is_hold', 0)
						->get('es_exhibition_badges')
						->result();
					foreach ($badges as $badge) {
						$badges_ids[] = $badge->id;
					}

					$html = '<a href="' . base_url() . 'print-multiple-badges.html?booking_id=' . urlencode(myid($id)) . '&badges='.implode(',', $badges_ids).'" target="_blank">Print All</a>';
				}
				else {
					$first_badges_ids = array();
					$last_badges_ids = array();
					$first_badges = $this->db
						->limit(10)
						->order_by('id', 'ASC')
						->where('booking_id', $id)
						->where('is_active', 1)
						->where('is_printed', 0)
						->where('is_hold', 0)
						->get('es_exhibition_badges')
						->result();
					$last_badges = $this->db
						->limit(10)
						->order_by('id', 'DESC')
						->where('booking_id', $id)
						->where('is_active', 1)
						->where('is_printed', 0)
						->where('is_hold', 0)
						->get('es_exhibition_badges')
						->result();

					foreach ($first_badges as $f) {
						$first_badges_ids[] = $f->id;
					}
					foreach ($last_badges as $l) {
						$last_badges_ids[] = $l->id;
					}

					$html = '<a href="' . base_url() . 'print-multiple-badges.html?booking_id=' . urlencode(myid($id)) . '&badges='.implode(',', $first_badges_ids).'" target="_blank">First 10 Remaining</a>';
					$html .= ' | <a href="' . base_url() . 'print-multiple-badges.html?booking_id=' . urlencode(myid($id)) . '&badges='.implode(',', $last_badges_ids).'" target="_blank">Last 10 Remaining</a>';
				}

				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('B.exhibition_id', $this->formdata->id)
			->where('B.is_canceled', 0)
			->where('B.is_approved', 1)
			->join('es_customers as C', 'B.customer_id = C.id')
			->from('es_exhibition_booking as B');

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
			$this->download_qr($qr, $filename);
		}
	}

	function print_badges() {
		//$booking_id = $this->input->get('booking_id');
		$badges = $this->input->get('badges');
		$badges = explode(',', $badges);

		$html = '';

		foreach ($badges as $badge_id) {
			$badge = $this->db
				->where('id', $badge_id)
				->where('is_printed', 0)
				->where('is_hold', 0)
				->get('es_exhibition_badges')
				->row();

			if (!isset($badge)) {
				show_404();
				die();
			}

			if ($badge->badge_type == 'trade_visitor') {
				$company = $badge->company;
			} else {
				$booking = $this->db
					->where('id', $badge->booking_id)
					->get('es_exhibition_booking')
					->row();
				$company_data = $this->db
					->where('id', $booking->customer_id)
					->get('es_customers')
					->row();
				$company = $company_data->company;
			}
			$this->load->library('QRGenerator');
			// $qr_data = $badge->id . ' - ' . $badge->full_name . ' - ' . $booking->customer_id . ' - ';
			// $qr_data .= ($badge->nationality == 'Pakistani') ? $badge->cnic : $badge->passport;
		 $qr_data =  'BEGIN:VCARD
VERSION:3.0
N:'.$badge->full_name.';
TITLE:'.$badge->designation.'
ORG:'.$company.'
TEL;TYPE=WORK;CELL:+'.$badge->mobile.'
EMAIL;TYPE=WORK:'.strtolower($badge->email).'
END:VCARD';
			$qr = new QRGenerator($qr_data);
			$qr = $qr->generate();
			$output = uniqid(time()) . '.png';
			$this->download_qr($qr, $output);

			if (!is_null($badge->barcode_data) && $badge->barcode_data != '') {
				$barcode_data = $badge->barcode_data;
			} else {
				$alpha_seed = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'); // and any other characters
				$number_seed = str_split('0123456789'); // and any other characters

				$random_alpha = '';
				foreach (array_rand($alpha_seed, 2) as $k) $random_alpha .= $alpha_seed[$k]; // get 2 alpha characters

				$random_number = '';
				foreach (array_rand($number_seed, 4) as $k) $random_number .= $number_seed[$k]; // get 4 number characters

				$barcode_data = $random_number . '' . $random_alpha . '' . $badge->id;

				$this->db
					->where('id', $badge_id)
					->update('es_exhibition_badges', array(
						'barcode_data' => $barcode_data
					));
			}
			$this->load->helper ("barcode");
			$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
			$barcode = $generator->getBarcode($barcode_data  , $generator::TYPE_CODE_128_B);


			$html .= $this->load->view('print_job/print_multiple_badge', array(
				'qr_link' => 'uploads/qr-codes/' . $output,
				'barcode_data' => $barcode,
				'company' => $company,
				'badge' => $badge,
			), true);

		} // end foreach


		foreach ($badges as $badge_id) {
			$this->db
				->where('id', $badge_id)
				->update('es_exhibition_badges', array(
					'is_printed' => 1,
					'print_on' => date('Y-m-d H:i:s')
				));
		}

		//$card_size_w = 78.74;
		$card_size_w = 82.74;
		$card_size_h = 48.26;

		//echo $html; die();
		set_time_limit(0);
		$this->load->helper ("pdf-loader");
		try {
			$html2pdf = new HTML2PDF('L', array($card_size_w, $card_size_h), 'en', true, 'UTF-8', array(0, 0, 0, 0));
			$html2pdf->pdf->SetDisplayMode('fullpage');
			//$html2pdf->addFont('impact', '', ('assets/fonts/impact.php'));
			//$html2pdf->setDefaultFont('impact');
			//$html2pdf->pdf->AddFont('Impact', '', 'assets/fonts/impact.php');
			//$html2pdf->pdf->SetFont('Impact', '', 8, 'assets/fonts/impact.php');
			$html2pdf->writeHTML($html);
			$html2pdf->Output('print-badge-'.time().'.pdf');
		}
		catch(HTML2PDF_exception $e) {
			$msg = $e->getMessage ();
			//$msg = base64_encode($msg);
			//$msg = trim ($msg);
			print_r($msg);
		}
	}
}