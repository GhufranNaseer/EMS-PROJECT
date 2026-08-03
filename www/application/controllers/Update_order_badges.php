<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Update_order_badges extends MY_Controller {
	protected function rule() {
		$this->activateRightsSystem();
		$crd = array(
			'crd_event_list,
			crd_list' => array(
				'rule' => '@'
			),
			'crd_list_datatable,
			crd_event_list_datatable,
			' => array(
				'rule' => '@',
				'ajaxOnly' => true
			)
		);
		$crd_dit = array(
			'crd_edit' => array(
				'rule' => '@'
			),
			'crd_edit_validate' => array(
				'ajaxOnly' => true,
				'rule' => '@'
			),
			'crd_edit_submit' => array(
				'rule' => '@'
			)
		);
		$this->load->model('usermdl');
		$this->myparent = base_url('packages.html');
		return array_merge($crd, $crd_dit);
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

	function crd_event_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('update_order_badges/event_list');
	}

	function crd_event_list_datatable() {
		$this->load->library('datatables');
		$this->datatables
			->select('E.id,
				  E.exhibition_title,
				  L.location_title				  
				  ', false)
			->add_column('total_items', function ($row) {
				return $this->db
					->where('exhibition_id', $row['id'])
					->where('is_active', 1)
					->where('is_deleted', 0)
					->count_all_results('es_packages');
			}, NULL)
			->add_column('col_action', function ($row) {
				$id = $row['id'];

				$html = '<a href="' . base_url() . 'update-order-badges-packages.html?id=' . urlencode(myid($id)) . '">Details</a>';

				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('E.is_deleted', 0)
			->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
			->from('es_exhibitions as E');

		print ($this->datatables->generate());
	}

	function crd_list() {
        $this->checkEditId();

        $this->load->view('includes/after_login/head');
        $this->load->view('update_order_badges/list');
	}


	function crd_list_datatable() {
        $this->checkEditId();

        $this->load->library('datatables');
        $this->datatables
            ->select('B.id,
            		B.exhibition_id,
                     C.company,
                     CONCAT(U.user_first_name, " ", U.user_last_name) as booked_by,
                     B.booking_date,
                     
                     IF(B.is_approved = 1, "<span class=\'label label-success\'>Approved</span>",
                     IF(B.booking_type = "confirmed", "<span class=\'label label-success\'>Confirm Sale</span>",
				  	 IF(B.booking_type = "tentative", "<span class=\'label label-warning\'>Tentative</span>", "N/A"))) as booking_type,
				  	 
                     
                     CONCAT(B.booking_price_type, " ", B.booking_total) as booking_total
				  ', false)

            ->unset_column('B.exhibition_id')
            ->add_column('col_action', function ($row) {
                $exhibition_id = $row['exhibition_id'];
                $id = $row['id'];
                $html = '<a href="' . base_url() . 'update-order-badges-edit.html?id=' . urlencode(myid($exhibition_id)) . '&order_id=' . urlencode(myid($id)) . '">Update Order Badges</a>';

                return "<div class='text-center'>{$html}</div>";
            }, NULL)
            ->where('B.exhibition_id', $this->formdata->id)
            ->where('B.is_canceled', 0)
            ->join('es_customers as C', 'B.customer_id = C.id')
            ->join('users as U', 'B.booked_by = U.id')
            ->from('es_exhibition_booking as B');

        if ($this->userdata->user_group_id == MANAGER) {
            $this->datatables->where('B.booking_type', 'confirmed');
        }

        if ($this->userdata->user_group_id == SALES_PERSON) {
            $this->datatables->where('B.booked_by', $this->userdata->id);
        }

        print ($this->datatables->generate());
	}


	function crd_edit() {
		$this->checkEditId();

		$order_id = $this->input->get('order_id');

		$this->order_data = $this->db
            ->where(mycolumn(), $order_id)
            ->get('es_exhibition_booking')
            ->row();

		$this->load->view('includes/after_login/head');
		$this->load->view('update_order_badges/edit');
	}

	function crd_edit_validate() {
		$this->checkEditId();
        $order_id = $this->input->get('order_id');

        $this->order_data = $this->db
            ->where(mycolumn(), $order_id)
            ->get('es_exhibition_booking')
            ->row();

		$this->form_validation->set_rules('badges_total_limit', 'badges_total_limit*badges_total_limit', 'trim|required');

		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}
		else {
			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function crd_edit_submit() {
		if ($this->crd_edit_validate() !== true)
			show_404();


		//echo '<pre>';
		//print_r($this->order_data); die;

        $this->db
            ->where('id', $this->order_data->id)
            ->update('es_exhibition_booking', array(
                'badges_total_limit' => $this->input->post('badges_total_limit'),
                'visitor_badges_limit' => $this->input->post('visitor_badges_limit'),
                'allow_invitation_edit' => ($this->input->post('allow_invitation_edit') ? 1 : 0),
            ));

        $this->db
            ->where('booking_id', $this->order_data->id)
            ->delete('es_exhibition_badges_limit');

        if ($this->input->post('badges')) {

            $this->db
                ->where('booking_id', $this->order_data->id)
                ->delete('es_exhibition_badges_limit');

            $badge_data = array();
            foreach ($this->input->post('badges') as $key => $item) {
                $badge_data[] = array(
                    'exhibition_id' => $this->formdata->id,
                    'booking_id' => $this->order_data->id,
                    'badge_type' => $item['badge_type'],
                    'invitation_type' => $key,
                    'quantity' => (array_key_exists('has_item', $item) ? $item['quantity'] : 0),
                    'is_active' => (array_key_exists('has_item', $item) ? 1 : 0),
					'created_on' => date('Y-m-d H:i:s')
                );
            }
            if (count($badge_data) > 0) {
                $this->db->insert_batch('es_exhibition_badges_limit', $badge_data);
            }
        }

		$this->db
			->insert('es_exhibition_booking_logs', array(
				'booking_id' => $this->order_data->id,
				'user_id' => $this->userdata->id,
				'message' => 'User '.$this->userdata->user_first_name.' '.$this->userdata->user_last_name.' update badges quantity for the booking!',
				'created_on' => date('Y-m-d H:i:s')
			));

		$this->session->set_flashdata('message', 'Badges has been updated successfully');
		redirect(base_url('update-order-badges-packages.html?id=' . myid($this->formdata->id)));
	}


}