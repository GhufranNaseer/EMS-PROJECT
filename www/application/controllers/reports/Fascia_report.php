<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fascia_report extends MY_Controller
{
	protected function rule()
	{
		$this->activateRightsSystem();
		$crd = array(
			'crd_list,crd_exhibition_list,
			' => array(
				'rule' => '@'
			),
			'crd_list_datatable,crd_exhibition_list_datatable,ajax_get_form_data,
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
		$this->load->view('fascia_report/list');
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
				$html = '<a href="' . base_url() . 'fascia_report-list.html?id=' . urlencode(myid($id)) . '">Report</a>';

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
		$this->load->view('fascia_report/exhibition_list');
	}

	function crd_exhibition_list_datatable()
	{
		$this->checkEditId();
		$this->load->library('datatables');
		$this->datatables
			->select('B.id,
            		B.exhibition_id,
                     C.Company,
				  ', false)

            ->add_column('hall_name', function ($row) {
                $halls = $this->db
                    ->where('S.booking_id', $row['id'])
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
            }, NULL)

            ->add_column('stall_name', function ($row) {
                $stalls = $this->db
                    ->where('S.booking_id', $row['id'])
                    ->join('es_exhibition_stalls as C', 'S.stall_id = C.id')
                    ->get('es_exhibition_booking_stalls as S')
                    ->result();
                $stall_name="";
                foreach ($stalls as $stall)
                {
                    $stall_name .=$stall->stall_name.' ,';
                }
                return rtrim($stall_name,',');
            }, NULL)
            ->add_column('stall_size', function ($row) {
				$stall_size = $this->db
					->select('SUM(C.stall_size) as total_size')
					->where('S.booking_id', $row['id'])
					->join('es_exhibition_stalls as C', 'S.stall_id = C.id')
					->get('es_exhibition_booking_stalls as S')
					->row();

                return $stall_size->total_size . ' sqm';
            }, NULL)

			->add_column('fascia', function ($row) {

				$data = $this->db
					->where('exhibition_id', $row['exhibition_id'])
					->where('booking_id', $row['id'])
					->where('form_id', 1)
					->get('es_exhibition_booking_forms_data')
					->row();
				$html='';
				if (isset($data)) {
					$data = json_decode($data->form_data);
					if(isset($data->shell_stall_data)){
						$html .= rtrim(str_replace('_', ' ', $data->shell_stall_data->stall_fascia_name ). ',',',');
					}
				}

                return $html;
			}, NULL)



			->unset_column('B.exhibition_id')
			->where('B.exhibition_id', $this->formdata->id)
			->where('B.is_canceled', 0)
            ->like("F.form_data","shell_stall_data")
			->where('B.is_approved', 1)
			->join('es_customers as C', 'B.customer_id = C.id')
            ->join('es_exhibition_booking_forms_data as F', 'B.id = F.booking_id AND F.form_id = 1')
			->from('es_exhibition_booking as B');


		print ($this->datatables->generate());
	}

}