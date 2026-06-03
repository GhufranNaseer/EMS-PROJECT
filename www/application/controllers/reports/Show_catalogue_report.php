<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Show_catalogue_report extends MY_Controller
{
	protected function rule()
	{
		$this->activateRightsSystem();
		$crd = array(
			'crd_list,crd_exhibition_list,business_sector_list,product_list,product_report_export,
            business_sector_report_export,catalogue_report_export,
			' => array(
				'rule' => '@'
			),
			'crd_list_datatable,
            crd_exhibition_list_datatable,
			' => array(
				'rule' => '@'
			)
		);
		$this->load->model('usermdl');
		$this->myparent = base_url('stalls.html');
		return array_merge($crd);
	}

	protected function checkEditId()
	{
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

	protected function getOrderData()
	{
		$id = $this->input->get('order_id');
		if (is_null($id))
			show_404();
		$id = $this->db->escape($id);
		$key = config_item('encryption_key');
		$key = $this->db->escape($key);
		$r = $this->db
			->where("MD5(CONCAT($key,`id`)) = $id")
			->get('es_exhibition_booking');
		if ($r->num_rows() == 0)
			show_404();
		$this->orderdata = $r->row();
	}

	function crd_list()
	{
		$this->load->view('includes/after_login/head');
		$this->load->view('show_catalogue_report/list');
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
				$html = '<a href="' . base_url() . 'show_catalogue_status-report-list.html?id=' . urlencode(myid($id)) . '">Catalogue Report</a>';
				$html .= ' | <a href="' . base_url() . 'show_business_sector-report-list.html?id=' . urlencode(myid($id)) . '">Business Sectors</a>';
				$html .= ' | <a href="' . base_url() . 'show_product-report-list.html?id=' . urlencode(myid($id)) . '">Catalogue Products</a>';

				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('E.is_deleted', 0)
			->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
			->from('es_exhibitions as E');

		print ($this->datatables->generate());
	}

	function crd_exhibition_list()
	{
		$this->load->view('includes/after_login/head');
		$this->load->view('show_catalogue_report/exhibition_list');
	}

	function crd_exhibition_list_datatable()
	{
		$this->checkEditId();

		$this->load->library('datatables');
		$this->datatables
			->select('B.id,
            		B.exhibition_id,
            		F.booking_id,
                     C.company,
				  ', false)

			->unset_column('F.booking_id')
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'show_catalogue-report-export?id=' . urlencode(myid($id)) . '">View</a>';
				return "<div class='text-center'>{$html}</div>";
			}, NULL)

			->add_column('company_logo', function ($row) {

				$data = $this->db
					->where('exhibition_id', $row['exhibition_id'])
					->where('booking_id', $row['id'])
					->where('form_id', 3)
					->get('es_exhibition_booking_forms_data')
					->row();
				$html='';
				if (isset($data)) {
					$data = json_decode($data->form_data);
					if(isset($data->exhibit) && isset($data->exhibit->company_logo)){
						$html = '<a href="'.base_url('client/'.str_replace('uploaded:', '', $data->exhibit->company_logo[0])).'" target="_blank">Download</a>';
					}
				}

				return $html;
			}, NULL)


			->add_column('Principal_logo', function ($row) {

				$data = $this->db
					->where('exhibition_id', $row['exhibition_id'])
					->where('booking_id', $row['id'])
					->where('form_id', 3)
					->get('es_exhibition_booking_forms_data')
					->row();
				$html='';
				if (isset($data)) {


					$data = json_decode($data->form_data);
					if ($data->principle->is_active == 1) {
						$logo_count = 1;
						foreach ( $data->principle->principles_list as $logo){
							if (!isset($logo->company_logo)) continue;
							// print_r($data->principle); die;
							$html .= '<a href="'.base_url('client/'.$logo->company_logo).'" target="_blank">Logo '.$logo_count.'</a>, <br>';
							$logo_count++;
						}
					}
				}

				return $html;
			}, NULL)

			->add_column('company_add', function ($row) {

				$data = $this->db
					->where('exhibition_id', $row['exhibition_id'])
					->where('booking_id', $row['id'])
					->where('form_id', 3)
					->get('es_exhibition_booking_forms_data')
					->row();
				$html='';
				if (isset($data)) {
					$data = json_decode($data->form_data);
					if(isset($data->exhibit) && isset($data->exhibit->company_ad)){
						foreach ($data->exhibit->company_ad as $ad) {
							$html = '<a href="'.base_url('client/'.str_replace('uploaded:', '', $ad)).'" target="_blank">Download</a>';
						}
					}
				}

				return $html;
			}, NULL)


			->add_column('col_action5', function ($row) {
				$id = $row['exhibition_id'];
				$booking_id = $row['booking_id'];
				$html = '<a href="' . base_url() . 'show_business_sector-report-list.html?id=' . urlencode(myid($id)) . '&booking_id='.$booking_id.'">View</a>';
				return "<div class='text-center'>{$html}</div>";
			}, NULL)

			->add_column('col_action6', function ($row) {
				$id = $row['exhibition_id'];
				$booking_id = $row['booking_id'];
				$html = '<a href="' . base_url() . 'show_product-report-list.html?id=' . urlencode(myid($id)) . '&booking_id='.$booking_id.'">View</a>';
				return "<div class='text-center'>{$html}</div>";
			}, NULL)


			->add_column('last_update_on', function ($row) {

				$data = $this->db
					->where('exhibition_id', $row['exhibition_id'])
					->where('booking_id', $row['id'])
					->where('form_id', 3)
					->get('es_exhibition_booking_forms_data')
					->row();
				$html='';

				if (isset($data)) {
					$html = '<p>'.$data->created_on.'</p>';
				}
				return $html;
			}, NULL)

			->unset_column('B.exhibition_id')
			->where('B.exhibition_id', $this->formdata->id)
			->where('B.is_canceled', 0)
			->where("F.form_data!=","")
			->join('es_exhibition_booking_forms_data as F', 'B.id = F.booking_id AND F.form_id = 3')
			->join('es_customers as C', 'B.customer_id = C.id')
			->join('es_customer_contact_persons as A', 'B.contact_person_id = A.id')
			->from('es_exhibition_booking as B');


		print ($this->datatables->generate());
	}

	function business_sector_list()
	{
		$this->load->view('includes/after_login/head');
		$this->load->view('show_catalogue_report/business_list');
	}

	function product_list()
	{
		$this->load->view('includes/after_login/head');
		$this->load->view('show_catalogue_report/product_list');
	}

	function product_report_export() {
		$fileName = 'Product Report.xls';

		$event_dates = $this->db
			->where(mycolumn('exhibition_id'), $_GET['id'])
			->get('es_exhibition_date')
			->result();
		$header_html = '';
		if (!empty($event_dates)) {
			$header_html .= '<tr><th colspan="5" align="left"><h4>Report: '.(explode('.', $fileName)[0]).'</h4></th></tr>';
			$header_html .= '<tr><td colspan="5" align="right">Print Date: '.date('d/m/Y h:i A').'</td></tr>';
			$header_html .= '<tr><td colspan="5" align="right">Event Date: '.date('d/m/Y', strtotime($event_dates[0]->date)).' - '.date('d/m/Y', strtotime(end($event_dates)->date)).'</td></tr>';
			$header_html .= '<tr><td colspan="5"></td></tr>';
		}

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

		$html .= '<table class="table table-bordered table-striped" id="crud-table" width="100%">
                            <thead>
                            '.$header_html.'
                            <tr class="">
                                <th class="text-center" width="4%">#</th>
                                <th width="20%">Company Name</th>
                                <th width="20%">Product Category</th>
                                <th width="20%">Product Name</th>
                                <th width="20%">Product Type</th>
                            </tr>
                            </thead>
                            <tbody>';

		if ($this->input->get('booking_id')) {
			$this->db->where('F.booking_id', $this->input->get('booking_id'));
		}

		$rows = $this->db
			->select('F.*, C.company')
			->where(mycolumn('F.exhibition_id'), $_GET['id'])
			->where('F.form_id', 3)
			->join('es_exhibition_booking as B', 'F.booking_id = B.id', 'LEFT')
			->join('es_customers as C', 'B.customer_id = C.id', 'LEFT')
			->get('es_exhibition_booking_forms_data as F')
			->result();


		$count = 1;


		foreach ($rows as $row) {
			$data = (isset($row->form_data)) ? json_decode($row->form_data) : null;

			$products = (isset($data->products) && isset($data->products->product)) ? $data->products->product : array();

			foreach ($products as $product) {
				$html .= '<tr>
                                <td>'.$count.'</td>
                                <td>'.$row->company.'</td>
                                <td>'.$product->category.'</td>
                                <td>'.$product->name.'</td>
                                <td>'.$product->type.'</td>
                            </tr>';

				$count++;
			}

		}





		$html .= "</tbody></table>";
		$html .= "</body>";
		$html .= "</html>";

		header("Content-Type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment;filename=".$fileName);
		header("Cache-Control: max-age=0");
		echo $html;
		die();
	}

	function business_sector_report_export(){

		$fileName = 'Business Sector.xls';

		$event_dates = $this->db
			->where(mycolumn('exhibition_id'), $_GET['id'])
			->get('es_exhibition_date')
			->result();
		$header_html = '';
		if (!empty($event_dates)) {
			$header_html .= '<tr><th colspan="6" align="left"><h4>Report: '.(explode('.', $fileName)[0]).'</h4></th></tr>';
			$header_html .= '<tr><td colspan="6" align="right">Print Date: '.date('d/m/Y h:i A').'</td></tr>';
			$header_html .= '<tr><td colspan="6" align="right">Event Date: '.date('d/m/Y', strtotime($event_dates[0]->date)).' - '.date('d/m/Y', strtotime(end($event_dates)->date)).'</td></tr>';
			$header_html .= '<tr><td colspan="6"></td></tr>';
		}

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

		$html .= '<table class="table table-bordered table-striped" id="crud-table" width="100%">
                            <thead>
                            '.$header_html.'
                            <tr class="">
                                <th class="text-center" width="4%">#</th>
                                <th width="20%">Company Name</th>
                                <th width="20%">Type</th>
                                <th width="20%">Business Sector</th>
                                <th width="20%">Business Type</th>
                                <th width="20%">Business Area</th>
                            </tr>
                            </thead>
                            <tbody>';

		if ($this->input->get('booking_id')) {
			$this->db->where('F.booking_id', $this->input->get('booking_id'));
		}

		$rows = $this->db
			->select('F.*, C.company')
			->where(mycolumn('F.exhibition_id'), $_GET['id'])
			->where('F.form_id', 3)
			->join('es_exhibition_booking as B', 'F.booking_id = B.id', 'LEFT')
			->join('es_customers as C', 'B.customer_id = C.id', 'LEFT')
			->get('es_exhibition_booking_forms_data as F')
			->result();


		$count = 1;


		foreach ($rows as $row) {
			$data = (isset($row->form_data)) ? json_decode($row->form_data) : null;

			$main_sectors = (isset($data->products) && isset($data->products->main)) ? $data->products->main : array();
			$other_sectors = (isset($data->products) && isset($data->products->other)) ? $data->products->other : array();

			foreach ($main_sectors as $main_sector) {
				$html .= '<tr>
                                    <td>'.$count.'</td>
                                    <td>'.$row->company.'</td>
                                    <td>Main</td>
                                    <td>'.$main_sector->sector.'</td>
                                    <td>'.$main_sector->type.'</td>
                                    <td>'.$main_sector->area.'</td>
                                    </tr>';

				$count++;
			}
			foreach ($other_sectors as $other_sector) {
				$html .= '<tr>
                                    <td>'.$count.'</td>
                                    <td>'.$row->company.'</td>
                                    <td>Other</td>
                                    <td>'.$other_sector->sector.'</td>
                                    <td>'.$other_sector->type.'</td>
                                    <td>'.$other_sector->area.'</td>
                                    </tr>';

				$count++;
			}

		}





		$html .= "</tbody></table>";
		$html .= "</body>";
		$html .= "</html>";

		header("Content-Type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment;filename=".$fileName);
		header("Cache-Control: max-age=0");
		echo $html;
		die();

	}

	function catalogue_report_export(){


		$html = $this->load->view('show_catalogue_report/print_catalogue', array(

		), true);

		//echo $html; die();
		set_time_limit(0);
		$this->load->helper ("pdf-loader");
		try {
			$html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8', array(10, 10, 10, 10));
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