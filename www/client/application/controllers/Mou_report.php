<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mou_report extends MY_Controller
{

    function crd_list()
    {
        $this->load->view('includes/after_login/head');
        $this->load->view('mou_report/exhibition_list');
    }

    function crd_list_datatable()
    {

    	$condition = "((B.user_type_from = 'exhibitor' AND B.request_from_id = ".$this->userdata->id.") OR (B.user_type_to = 'exhibitor' AND B.request_to_id = ".$this->userdata->id."))";

		if ($this->input->get('filter_status') && $this->input->get('filter_status') == 'my_requests') {
			$condition = "(B.user_type_from = 'exhibitor' AND B.request_from_id = ".$this->userdata->id.")";
		}

    	$this->load->library('datatables');
        $this->datatables
            ->select('B.id,
					B.exhibition_id,
                    C.company as request_from_comapny,
                    D.company as request_to_comapny,
                    B.exhibition_day,
                    B.mou_sign_date,
                    B.mou_sign_time,
                    IF(B.is_canceled = 1, "<span class=\'label label-danger\'>Decline</span>",
                    	IF(B.is_approved = 1, "<span class=\'label label-success\'>Approved</span>", "<span class=\'label label-warning\'>Pending</span>")) as status,
					B.is_canceled,
                    B.is_approved,
                    B.user_type_to,
                    B.request_to_id,
   				  ', false)

			->unset_column('B.exhibition_id')
			->unset_column('B.is_canceled')
			->unset_column('B.is_approved')
			->unset_column('B.user_type_to')
			->unset_column('B.request_to_id')

            ->add_column('col_action', function ($row) {
                $id = $row['id'];
                $html = '';
				if ($row['is_approved'] == 0) {
					$html .= '<a href="' . base_url() . 'mou-delete.html?id=' . urlencode(myid($id)) . '" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you would like to delete the MoU request?\')">Delete</a> ';
				}
                
				
                return "<div class='text-right'>{$html}</div>";
            }, NULL)

            ->where('B.exhibition_id', $this->event->id)
            ->where('B.is_deleted', 0)
            ->where($condition)
			->join('es_customers as C', 'B.request_from_id = C.id')
			->join('es_customers as D', 'B.request_to_id = D.id')
            ->from('es_exhibition_mou_sign as B');

        if ($this->input->get('filter_status') && $this->input->get('filter_status') != '') {
        	if ($this->input->get('filter_status') == 'approved') {
				$this->datatables->where('is_approved', 1);
			} else if ($this->input->get('filter_status') == 'pending') {
				$this->datatables->where('is_approved', 0);
			} else if ($this->input->get('filter_status') == 'my_requests') {
				$this->datatables->where('is_approved', 0);
			}
		}
		if ($this->input->get('filter_day') && $this->input->get('filter_day') != '') {
			$this->datatables->where('exhibition_day', $this->input->get('filter_day'));
		}

		
        print ($this->datatables->generate());
    }

    function delete_meeting() {
		$this->db
			->where(mycolumn(), $this->input->get('id'))
			->update('es_exhibition_mou_sign', array(
				'is_approved' => 0,
				'approved_on' => null,
				'is_canceled' => 0,
				'canceled_on' => null,
				'is_deleted' => 1,
				'deleted_on' => date('Y-m-d H:i:s'),
			));

		$this->session->set_flashdata('message', 'MoU request has been deleted successfully');
		redirect(base_url('mou_sign.html'));
	}
}