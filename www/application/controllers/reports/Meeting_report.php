<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meeting_report extends MY_Controller
{
    protected function rule()
    {
        $this->activateRightsSystem();
        $crd = array(
            'crd_list,crd_exhibition_list,
			' => array(
                'rule' => '@'
            ),
            'crd_list_datatable,crd_exhibition_list_datatable,get_meeting_details_ajax,
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
        $this->load->view('meeting_report/list');
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
                    ->where('is_approved', 1)
                    ->count_all_results('my_appointments_datatable');
            }, NULL)
            ->add_column('col_action', function ($row) {
                $id = $row['id'];
                $html = '<a href="' . base_url() . 'meeting-report_status-list.html?id=' . urlencode(myid($id)) . '">Report</a>';

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
        $this->load->view('meeting_report/exhibition_list');
    }

    function crd_exhibition_list_datatable()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id,
            		exhibition_id,
                   
                  appointment_from_company_name,
                  appointment_to_company_name,
                     exhibition_day,
                     appointment_date,
                     appointment_time,
                     user_type_to,
                     user_type_from,
                     appointment_to,
                     appointment_from,
                     IF(is_canceled = 1, "<span class=\'label label-danger\'>Canceled</span>",
                    	IF(is_approved = 1, "<span class=\'label label-success\'>Approved</span>", "<span class=\'label label-warning\'>Pending</span>")) as status,
					is_conducted,
					appointment_feedback,
					is_canceled,
                    is_approved,
   				  ', false)


			->unset_column('is_conducted')
			->unset_column('appointment_feedback')
			->unset_column('is_canceled')
			->unset_column('is_approved')
            ->unset_column('exhibition_id')
            ->unset_column('user_type_to')
            ->unset_column('user_type_from')
            ->unset_column('appointment_to')
            ->unset_column('appointment_from')

			->add_column('agenda', function ($row) {
				$id = $row['id'];
				
				return '<div class="text-center"><button type="button" class="btn btn-link" onclick="show_agenda(\''.myid($id).'\')">View Details</button></div>';
			}, NULL)

			// ->add_column('col_conducted', function ($row) {
			// 	$id = $row['id'];
			// 	$is_conducted = $row['is_conducted'];
				
			// 	if ($row['is_approved'] == 1) {
			// 		if ($row['is_conducted'] == 1) {
			// 			return '<div class="text-left small">'.$row['appointment_feedback'].'</div>';
			// 		} else {
			// 			return '<div class="text-center small">N/A</div>';
			// 		}
			// 	} else {
			// 		return '<div class="text-center">-</div>';
			// 	}
			// }, NULL)

            //->where('is_approved', 1)
			->where('is_deleted', 0)
			->where('is_canceled', 0)
            ->where(mycolumn('exhibition_id'), $this->input->get('id'))
            ->from('my_appointments_datatable');

		if ($this->input->get('export_type') && $this->input->get('export_type') != '') {

			$this->datatables->add_column('agenda_of_meeting', function ($row) {
				$data = $this->db->select('agenda_of_meeting')->where('id', $row['id'])->get('es_exhibition_appointments')->row();
				return $data->agenda_of_meeting;
			}, NULL);
			
			$this->datatables->add_column('discussion_points', function ($row) {
				$data = $this->db->select('discussion_points')->where('id', $row['id'])->get('es_exhibition_appointments')->row();
				return $data->discussion_points;
			}, NULL);

			$this->datatables->add_column('is_conducted', function ($row) {
				return ($row['is_conducted'] == 1) ? 'Yes' : 'No';
			}, NULL);

			$this->datatables->add_column('appointment_feedback', function ($row) {
				return $row['appointment_feedback'];
			}, NULL);

		} else {
			
		}


		if ($this->input->get('filter_status') && $this->input->get('filter_status') != '') {
			if ($this->input->get('filter_status') == 'approved') {
				$this->datatables->where('is_approved', 1);
			} else if ($this->input->get('filter_status') == 'pending') {
				$this->datatables->where('is_approved', 0);
			}
		}

        print ($this->datatables->generate());
    }

	function get_meeting_details_ajax() {

		$id = $this->input->post('id');
		
		$meeting = $this->db->where(mycolumn(), $id)->get('es_exhibition_appointments')->row();


		echo json_encode(array(
			'error' => 0,
			'message' => 'Meeting details!',
			'data' => $meeting,
		));
		die;
	}
}