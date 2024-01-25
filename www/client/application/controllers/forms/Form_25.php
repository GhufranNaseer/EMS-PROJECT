<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form_25 extends MY_Controller {

	public $form_id = 25;

	function index() {
		$this->formdata = null;

		$check = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->get('es_exhibition_booking_forms_data')
			->row();

		if (isset($check)) {
			$data = json_decode($check->form_data);

			$this->formdata = $data->branding;
		}

		$this->load->view('includes/after_login/head');
		$this->load->view('forms/form_25');
	}

	function form_25_validate() {

		$form_expire_date = $this->db->where('exhibition_id', $this->event->id)->where('form_id', $this->form_id)->get('es_exhibition_forms')->row()->expiry_date . ' 24:00:00';
		$check_extend_date = $this->db
			->where('exhibition_id', $this->event->id)
			->where('form_id', $this->form_id)
			->where('booking_id', $this->booking->id)
			->get('es_exhibition_forms_extended')
			->row();
		if (isset($check_extend_date) && !empty($check_extend_date)) {
			if ($check_extend_date->update_date && !is_null($check_extend_date->update_date) && strtotime($check_extend_date->update_date) >= strtotime($form_expire_date)) {
				$form_expire_date = $check_extend_date->update_date;
			}
			$form_expire_date = strtotime('+ '.(int)$check_extend_date->extended_hours.' hours', strtotime($form_expire_date));
		} else {
			$form_expire_date = strtotime($form_expire_date);
		}
		if (strtotime(date('Y-m-d H:i:s')) > $form_expire_date) {
			return $this->common->doError(func_num_args(), 'Form submission date is already expired!');
		}


		$this->form_validation->set_rules('main_category', 'main_category*Main category', 'trim|required');
		$this->form_validation->set_rules('sub_category', 'sub_category*Sub Category', 'trim|required');
		$this->form_validation->set_rules('item', 'item*Item', 'trim|required');
		$this->form_validation->set_rules('quantity', 'quantity*Quantity', 'trim|required');
		$this->form_validation->set_rules('cost', 'cost*cost', 'trim|required');


		if ($this->form_validation->run() == false)
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		else
			return $this->common->doError(func_num_args(), "done", true);

	}

	function form_25_submit() {
		if ($this->form_25_validate() !== true)
			show_404();


		$data = $this->input->post();
		$data['id'] = uniqid(time().rand(1, 1000));


		$check = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->count_all_results('es_exhibition_booking_forms_data');

		if ($check > 0) {
			$old_form = $this->db
				->where('exhibition_id', $this->event->id)
				->where('booking_id', $this->booking->id)
				->where('form_id', $this->form_id)
				->get('es_exhibition_booking_forms_data')
				->row();

			$new_data = json_decode($old_form->form_data);
			$new_data->branding[] = $data;

			$this->db
				->where('exhibition_id', $this->event->id)
				->where('booking_id', $this->booking->id)
				->where('form_id', $this->form_id)
				->update('es_exhibition_booking_forms_data', array(
					'form_data' => json_encode($new_data),
					'modified_on' => date('Y-m-d H:i:s'),
				));
		} else {
			$this->db
				->where('exhibition_id', $this->event->id)
				->where('booking_id', $this->booking->id)
				->where('form_id', $this->form_id)
				->insert('es_exhibition_booking_forms_data', array(
					'exhibition_id' => $this->event->id,
					'booking_id' => $this->booking->id,
					'form_id' => $this->form_id,
					'form_data' => json_encode(array('branding' => array($data))),
					'created_on' => date('Y-m-d H:i:s'),
				));
		}

		$this->session->set_flashdata('message', 'Form has been submitted successfully');
		redirect(base_url('forms/form_25'));
	}


	function get_main_company_detail() {

		$main_category = $this->db
			->where('parent_id', $this->input->post('category'))
			->get('es_inventory_category')
			->result();
		$html = '';
		$html .= '<option>-Select-</option>';
		foreach($main_category as $main){
			$html .= '<option value=" '.$main->id.' ">' . $main->category_title . '</option>';
		}

		echo $html;
	}

	function get_main_company_form() {
		$items = $this->db
			->where('category_id', $this->input->post('form_data'))
			->get('es_inventory_item_global')
			->result();

		$html = '';
		$html .= '<option>-Select-</option>';
		foreach($items as $item){
			$price = ($this->booking->booking_price_type == 'PKR') ? $item->item_price_pkr : $item->item_price_usd;
			$html .= '<option value=" '.$item->id.' " data-price="'.$price.'">' . $item->item_title . '</option>';
		}
		echo $html;

	}

	function delete_branding() {
		$id = $this->input->get('id');

		$old_form = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->get('es_exhibition_booking_forms_data')
			->row();

		$all_data = json_decode($old_form->form_data);
		$new_data = array();
		foreach ($all_data->branding as $data) {
			if ($data->id != $id) {
				$new_data[] = $data;
			}
		}
		$all_data->branding = $new_data;

		$this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->update('es_exhibition_booking_forms_data', array(
				'form_data' => json_encode($all_data),
				'modified_on' => date('Y-m-d H:i:s'),
			));

		$this->session->set_flashdata('message', 'Record has been successfully deleted!');
		redirect(base_url('forms/form_25'));
	}
}