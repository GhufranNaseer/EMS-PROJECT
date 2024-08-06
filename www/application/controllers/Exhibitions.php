<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exhibitions extends MY_Controller {
	protected function rule() {
		$this->activateRightsSystem();
		$crd = array(
			'crd_list,bank_detail_add,bank_detail_add_submit,crd_add,crd_add_submit' => array(
				'rule' => '@'
			),
			'crd_list_datatable,crd_add_validate,bank_detail_add_validate' => array(
				'rule' => '@',
				'ajaxOnly' => true
			)
		);
		$crd_dit = array(
			'crd_edit,crd_delete' => array(
				'rule' => '@'
			),
			'crd_edit_validate, 
			get_inventory_items' => array(
				'ajaxOnly' => true,
				'rule' => '@'
			),
			'crd_edit_submit' => array(
				'rule' => '@'
			)
		);
		$this->load->model('usermdl');
		$this->myparent = base_url('exhibitions.html');
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

	function crd_list() {
		$this->load->view('includes/after_login/head');
		$this->load->view('exhibitions/list');
	}


	function crd_list_datatable() {
		//DATE_FORMAT(E.exhibition_date, "%d, %b %Y"),
		$this->load->library('datatables');
		$this->datatables
			->select('E.id,
				CONCAT("<img src=\'", E.event_logo, "\' height=\'40px\' />") as event_logo,
				  E.exhibition_title,
				  L.location_title,
				  E.booking_expire_date,
				  E.price_type
				  ', false)

			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'exhibitions-edit.html?id=' . urlencode(myid($id)) . '">Edit</a>';

				$html .= ' | <a href="'. base_url() .'bank-details-add.html?exhibition_id='. $id .'"> Bank Details';

				if (ALLOW_DELETION)
					$html .= ' | <a href="' . base_url() . 'exhibitions-delete.html?id=' . urlencode(myid($id)) . '" onclick="return confirm(\'Are you sure want to delete\');">Delete</a>';


				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('E.is_deleted', 0)
			->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
			->from('es_exhibitions as E');

		print ($this->datatables->generate());
	}

	function bank_detail_add() {
		$bank_data = $this->db
				->where('exhibition_id', $this->input->get('exhibition_id'))
				->get('bank_details')
				->row();
		$data['bank_data'] = $bank_data;
		$this->load->view('includes/after_login/head');
        $this->load->view('exhibitions/bank_detail_add', $data);

    }

    function bank_detail_add_submit() {
        if ($this->bank_detail_add_validate() !== true)
            show_404();

		$this->db
            ->where('exhibition_id', $this->input->post('exhibition_id'))
            ->delete('bank_details');	
		

        $data = array(
            'exhibition_id' => $this->input->post('exhibition_id'),
            'bank_name' => $this->input->post('bank_name'),
            'title' => $this->input->post('title'),
            'branch_name' => $this->input->post('branch_name'),
			'branch_code' => $this->input->post('branch_code'),
            'account_no' => $this->input->post('account_no'),
            'iban_no' => $this->input->post('iban_no'),
            'swift_code' => $this->input->post('swift_code'),
            'created_on' => date('Y-m-d H:i:s')
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->insert('bank_details');
        //$id = $this->db->insert_id();
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'Bank details updated successfully');
		
		redirect(base_url('exhibitions.html'));
    }

    function bank_detail_add_validate() {

        $this->form_validation->set_rules('bank_name', 'bank_name*Bank name', 'trim|required');
		$this->form_validation->set_rules('title', 'title*Title of Account', 'trim|required');
		$this->form_validation->set_rules('branch_name', 'branch_name*Branch name', 'trim|required');
		$this->form_validation->set_rules('account_no', 'account_no*Account no', 'trim|required');
		$this->form_validation->set_rules('iban_no', 'iban_no*IBAN No', 'trim|required');
		$this->form_validation->set_rules('swift_code', 'swift_code*Swift Code', 'trim|required');
		$this->form_validation->set_rules('branch_code', 'branch_code*Branch Code', 'trim|required');

        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else
            return $this->common->doError(func_num_args(), "done", true);
    }

	function crd_add_validate() {

		$this->form_validation->set_rules('exhibition_title', 'exhibition_title*Exhibition Title', 'trim|required|is_unique[es_exhibitions.exhibition_title]');
        $this->form_validation->set_rules('event_organizer', 'event_organizer*event organizer', 'trim|required');
		$this->form_validation->set_rules('location_id', 'location_id*Location', 'trim|required');
		$this->form_validation->set_rules('price_type', 'price_type*Price type', 'trim|required');

		$this->form_validation->set_rules('event_logo[0]', 'event_logo[0]*event logo', 'trim|required');
		$this->form_validation->set_rules('event_logo_link', 'event_logo_link*event logo link', 'trim');
		$this->form_validation->set_rules('event_associate_logo[0]', 'event_associate_logo[0]*event associate logo', 'trim|required');
		$this->form_validation->set_rules('associate_logo_link', 'associate_logo_link*associate logo link', 'trim');
		$this->form_validation->set_rules('event_manager_logo[0]', 'event_manager_logo[0]*event manager logo', 'trim|required');
		$this->form_validation->set_rules('manager_logo_link', 'manager_logo_link*manager logo link', 'trim');
		$this->form_validation->set_rules('event_background[0]', 'event_background[0]*event background', 'trim');
		$this->form_validation->set_rules('event_color', 'event_color*event color', 'trim');


		$this->form_validation->set_rules('location_halls[]', 'location_halls[]*location halls', 'trim|required');
		$this->form_validation->set_rules('opening_closing[]', 'opening_closing[]*opening closing date', 'trim|required');
		$this->form_validation->set_rules('stall_builder_contractors[]', 'stall_builder_contractors[]*stall builder contractors', 'trim|required');
		$this->form_validation->set_rules('event_freight_forwarders[]', 'event_freight_forwarders[]*freight forwarders', 'trim|required');


		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}
		else {
			foreach ($this->input->post('opening_closing') as $key => $value) {
				if (trim($value['date']) == '') {
					return $this->common->doError(func_num_args(), "date field is required");
				}
				else if (trim($value['open_time']) == '') {
					return $this->common->doError(func_num_args(), "open time field is required");
				}
				else if (trim($value['closing_time']) == '') {
					return $this->common->doError(func_num_args(), "closing time field is required");
				}
			}



			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function crd_add() {
		$this->load->view('includes/after_login/head');
		$this->load->view('exhibitions/add');
	}

	function crd_add_submit() {
		if ($this->crd_add_validate() !== true)
			show_404();

		/*echo '<pre>';
		print_r($this->input->post());
		die();*/

		$expire_date = $this->input->post('opening_closing');
		$expire_date = end($expire_date)['date'];

		$exhibition_data = array(
            'event_organizer' => $this->input->post('event_organizer'),
			'location_id' => $this->input->post('location_id'),
			'exhibition_title' => $this->input->post('exhibition_title'),
			'booking_expire_date' => date('Y-m-d', strtotime($expire_date)),
			'price_type' => $this->input->post('price_type'),
			'event_color' => $this->input->post('event_color'),
			'event_background' => $this->input->post('event_background')[0],
			'event_logo' => $this->input->post('event_logo')[0],
			'event_logo_link' => $this->input->post('event_logo_link'),
			'associate_logo' => $this->input->post('event_associate_logo')[0],
			'associate_logo_link' => $this->input->post('associate_logo_link'),
			'manager_logo' => $this->input->post('event_manager_logo')[0],
			'manager_logo_link' => $this->input->post('manager_logo_link'),
			'stall_builder_contractors' => implode(',', $this->input->post('stall_builder_contractors')),
			'event_freight_forwarders' => implode(',', $this->input->post('event_freight_forwarders')),
			'created_on' => date('Y-m-d H:i:s'),
		);

		$this->db->insert('es_exhibitions', $exhibition_data);
		$exhibition_id = $this->db->insert_id();

		// halls
		$halls_data = array();
		foreach ($this->input->post('location_halls') as $key => $value) {
			$halls_data[] = array(
				'exhibition_id' => $exhibition_id,
				'location_id' => $this->input->post('location_id'),
				'hall_id' => $value,
			);
		}
		if (count($halls_data) > 0) {
			$this->db->insert_batch('es_exhibition_halls', $halls_data);
		}


		// insert dates
		$exhibition_dates_data = array();
		foreach ($this->input->post('opening_closing') as $key => $value) {
			$exhibition_dates_data[] = array(
				'exhibition_id' => $exhibition_id,
				'date' => date('Y-m-d', strtotime($value['date'])),
				'open_time' => date('H:i:s', strtotime($value['open_time'])),
				'closing_time' => date('H:i:s', strtotime($value['closing_time'])),
			);
		}
		if (count($exhibition_dates_data) > 0) {
			$this->db->insert_batch('es_exhibition_date', $exhibition_dates_data);
		}



		$this->session->set_flashdata('message', 'Exhibition has been created successfully');
		redirect($this->myparent);
	}



	function crd_edit() {
		$this->checkEditId();
		$this->load->view('includes/after_login/head');
		$this->load->view('exhibitions/edit');
	}

	function crd_edit_validate() {
		$this->checkEditId();

		$this->form_validation->set_rules('exhibition_title', 'exhibition_title*Exhibition Title', 'trim|required|set_edit_mood[id.' . $this->formdata->id . ']|is_unique[es_exhibitions.exhibition_title]');
        $this->form_validation->set_rules('event_organizer', 'event_organizer*event organizer', 'trim|required');
		$this->form_validation->set_rules('location_id', 'location_id*Location', 'trim|required');
		$this->form_validation->set_rules('price_type', 'price_type*Price type', 'trim|required');

		$this->form_validation->set_rules('event_logo[0]', 'event_logo[0]*event logo', 'trim|required');
		$this->form_validation->set_rules('event_logo_link', 'event_logo_link*event logo link', 'trim');
		$this->form_validation->set_rules('event_associate_logo[0]', 'event_associate_logo[0]*event associate logo', 'trim|required');
		$this->form_validation->set_rules('associate_logo_link', 'associate_logo_link*associate logo link', 'trim');
		$this->form_validation->set_rules('event_manager_logo[0]', 'event_manager_logo[0]*event manager logo', 'trim|required');
		$this->form_validation->set_rules('manager_logo_link', 'manager_logo_link*manager logo link', 'trim');
		$this->form_validation->set_rules('event_background[0]', 'event_background[0]*event background', 'trim');
		$this->form_validation->set_rules('event_color', 'event_color*event color', 'trim');

		$this->form_validation->set_rules('location_halls[]', 'location_halls[]*location halls', 'trim|required');
		$this->form_validation->set_rules('opening_closing[]', 'opening_closing[]*opening closing date', 'trim|required');
		$this->form_validation->set_rules('stall_builder_contractors[]', 'stall_builder_contractors[]*stall builder contractors', 'trim|required');
		$this->form_validation->set_rules('event_freight_forwarders[]', 'event_freight_forwarders[]*freight forwarders', 'trim|required');


		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}
		else {
			foreach ($this->input->post('opening_closing') as $key => $value) {
				if (trim($value['date']) == '') {
					return $this->common->doError(func_num_args(), "date field is required");
				}
				else if (trim($value['open_time']) == '') {
					return $this->common->doError(func_num_args(), "open time field is required");
				}
				else if (trim($value['closing_time']) == '') {
					return $this->common->doError(func_num_args(), "closing time field is required");
				}
			}


			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function crd_edit_submit() {
		if ($this->crd_edit_validate() !== true)
			show_404();

		//echo '<pre>';
		//print_r($this->input->post());
		//die();

		$exhibition_id = $this->formdata->id;
		$expire_date = $this->input->post('opening_closing');
		$expire_date = end($expire_date)['date'];

		$exhibition_data = array(
			'location_id' => $this->input->post('location_id'),
            'event_organizer' => $this->input->post('event_organizer'),
			'exhibition_title' => $this->input->post('exhibition_title'),
			'booking_expire_date' => date('Y-m-d', strtotime($expire_date)),
			'price_type' => $this->input->post('price_type'),
			'event_color' => $this->input->post('event_color'),
			'event_background' => $this->funcs->make_image_string($this->input->post('event_background')),
			'event_logo' => $this->funcs->make_image_string($this->input->post('event_logo')),
			'event_logo_link' => $this->input->post('event_logo_link'),
			'associate_logo' => $this->funcs->make_image_string($this->input->post('event_associate_logo')),
			'associate_logo_link' => $this->input->post('associate_logo_link'),
			'manager_logo' => $this->funcs->make_image_string($this->input->post('event_manager_logo')),
			'manager_logo_link' => $this->input->post('manager_logo_link'),
			'stall_builder_contractors' => implode(',', $this->input->post('stall_builder_contractors')),
			'event_freight_forwarders' => implode(',', $this->input->post('event_freight_forwarders')),
		);

		$this->db
			->set($exhibition_data)
			->where('id', $exhibition_id)
			->update('es_exhibitions');


		// halls
		$this->db
			->where('exhibition_id', $exhibition_id)
			->delete('es_exhibition_halls');
		$halls_data = array();
		foreach ($this->input->post('location_halls') as $key => $value) {
			$halls_data[] = array(
				'exhibition_id' => $exhibition_id,
				'location_id' => $this->input->post('location_id'),
				'hall_id' => $value,
			);
		}
		if (count($halls_data) > 0) {
			$this->db->insert_batch('es_exhibition_halls', $halls_data);
		}


		$this->db
			->where('exhibition_id', $exhibition_id)
			->delete('es_exhibition_date');
		// insert dates
		$exhibition_dates_data = array();
		foreach ($this->input->post('opening_closing') as $key => $value) {
			$exhibition_dates_data[] = array(
				'exhibition_id' => $exhibition_id,
				'date' => date('Y-m-d', strtotime($value['date'])),
				'open_time' => date('H:i:s', strtotime($value['open_time'])),
				'closing_time' => date('H:i:s', strtotime($value['closing_time'])),
			);
		}
		if (count($exhibition_dates_data) > 0) {
			$this->db->insert_batch('es_exhibition_date', $exhibition_dates_data);
		}




		$this->session->set_flashdata('message', 'Exhibition has been updated successfully');
		redirect($this->myparent);
	}


	function crd_delete() {
		$this->checkEditId();
		if (!ALLOW_DELETION)
			show_404();

		$stall_check = $this->db
			->where('exhibition_id', $this->formdata->id)
			->where('is_deleted', 0)
			->count_all_results('es_exhibition_stalls');

		if ($stall_check > 0) {
			$this->session->set_flashdata('error', 'Event cannot be deleted because it has some stalls available!');
			redirect($this->myparent);
			die();
		}

		$item_check = $this->db
			->where('exhibition_id', $this->formdata->id)
			->where('is_active', 1)
			->where('is_deleted', 0)
			->count_all_results('es_inventory_item');

		if ($item_check > 0) {
			$this->session->set_flashdata('error', 'Event cannot be deleted because it has some active items available!');
			redirect($this->myparent);
			die();
		}

		$booking_check = $this->db
			->where('exhibition_id', $this->formdata->id)
			->where('is_canceled', 0)
			->count_all_results('es_exhibition_booking');

		if ($booking_check > 0) {
			$this->session->set_flashdata('error', 'Event cannot be deleted because it has some bookings available!');
			redirect($this->myparent);
			die();
		}

		$this->db
			->where('id', $this->formdata->id)
			->update('es_exhibitions', array(
				'is_deleted' => 1,
				'deleted_by' => $this->userdata->id,
				'deleted_on' => date('Y-m-d H:i:s')
			));

		$this->session->set_flashdata('message', 'Event has been deleted successfully');
		redirect($this->myparent);
	}




	function get_inventory_items() {
		$category_id = $this->input->post('category_id');

		$items = $this->db
			->where('category_id', $category_id)
			->where('is_deleted', 0)
			->get('es_inventory_item_global')
			->result();

		$html = '<option value="">- select -</option>';
		foreach ($items as $item) {
			$html .= '<option value="'.$item->id.'">'.$item->item_title.'</option>';
		}
		echo $html;
		exit;
	}
}