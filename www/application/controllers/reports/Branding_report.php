<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Branding_report extends MY_Controller
{
	protected function rule()
	{
		$this->activateRightsSystem();
		$crd = array(
			'crd_list,crd_exhibition_list,export_report,
			' => array(
				'rule' => '@'
			),
			'crd_list_datatable,crd_exhibition_list_datatable,ajax_get_form_data,
			' => array(
				'rule' => '@',
				'ajaxOnly' => true
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
		$this->load->view('branding_report/list');
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
					->where('is_approved', 1)
					->count_all_results('es_exhibition_booking');
			}, NULL)
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'branding_report-list.html?id=' . urlencode(myid($id)) . '">Report</a>';

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
		$this->load->view('branding_report/exhibition_list');
	}

	function export_report() {
		$fileName = 'Branding Report.xls';

		$event_dates = $this->db
			->where(mycolumn('exhibition_id'), $_GET['id'])
			->get('es_exhibition_date')
			->result();
		$header_html = '';
		if (!empty($event_dates)) {
			$header_html .= '<tr><th colspan="10" align="left"><h4>Report: '.(explode('.', $fileName)[0]).'</h4></th></tr>';
			$header_html .= '<tr><td colspan="10" align="right">Print Date: '.date('d/m/Y h:i A').'</td></tr>';
			$header_html .= '<tr><td colspan="10" align="right">Event Date: '.date('d/m/Y', strtotime($event_dates[0]->date)).' - '.date('d/m/Y', strtotime(end($event_dates)->date)).'</td></tr>';
			$header_html .= '<tr><td colspan="10"></td></tr>';
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

		$html .= '<table class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            '.$header_html.'
                            <tr class="">
                                    <th>#</th>
                                    <th>Company Name</th>
                                    <th>Branding Catagory</th>
                                    <th>Branding Sub Catagory</th>
                                    <th>Quantity</th>
                                    <th>Ad Size(MB\'s)</th>
                                    <th>Download</th>
                                    <th>update Date</th>
                                    <th>Time</th>
                                    <th>Cost</th>
                            </tr>
                            </thead>
                            <tbody>';

                            $rows = $this->db
                                // ->select('F.*, C.company')
                                ->where(mycolumn('exhibition_id'), $_GET['id'])
                                ->where('form_id', 25)
                                //->where('B.is_canceled', 0)
                                //->where('B.is_approved', 1)
                                ->get('es_exhibition_booking_forms_data')
                                ->result();


                            $count = 1;
                            foreach ($rows as $row) {
                                $data = (isset($row->form_data)) ? json_decode($row->form_data) : null;

                                if (!is_null($data) && isset($data->Branding)) {
                                    foreach ($data->Branding as $d) {

                                        $main=$this->db
                                            ->where('id', $d->main_category)
                                            ->get('es_inventory_category')
                                            ->row();
                                        $sub=$this->db
                                            ->where('id', $d->sub_category)
                                            ->get('es_inventory_category')
                                            ->row();
                                        $html .='<tr>
                                                            <td>'.($count+1).'</td>
                                                            <td>'.$d->company.'</td>
                                                            <td>'.$main->category_title.'</td>
                                                            <td>'.$sub->category_title.'</td>
                                                            <td>'.$d->quantity.'</td>
                                                            <td></td>
                                                            <td></td>
                                                            <td>'.$d->date.'</td>
                                                            <td>'.$d->time.'</td>
                                                            <td>'.$d->cost.'</td>
                                                            </tr>';

                                    }
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
}