<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form_22 extends MY_Controller {
	public $form_id = 22;

	function index() {
		$this->formdata = null;
		$this->editdata = null;

		$check = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->get('es_exhibition_booking_forms_data')
			->row();

		if (isset($check)) {
			$this->formdata = json_decode($check->form_data);
		}

		$this->load->view('includes/after_login/head');
		$this->load->view('forms/form_22');
	}

	function form_22_validate() {
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

		$this->form_validation->set_rules('product_name', 'product_name*product name', 'trim|required');
		$this->form_validation->set_rules('product_quantity', 'product_quantity*product quantity', 'trim|required|numeric');
		$this->form_validation->set_rules('product_category', 'product_category*product category', 'trim|required');
		$this->form_validation->set_rules('product_description', 'product_description*description', 'trim|required');
		//$this->form_validation->set_rules('product_note', 'product_note*note', 'trim|required');
		$this->form_validation->set_rules('package_quantity', 'package_quantity*package quantity', 'trim|required|numeric');
		$this->form_validation->set_rules('package_mode', 'package_mode*mode of shipping', 'trim|required');
		$this->form_validation->set_rules('package_mode_type', 'package_mode_type*mode of shipping', 'trim|required');
		$this->form_validation->set_rules('package_length', 'package_length*length', 'trim|required|numeric');
		$this->form_validation->set_rules('package_breth', 'package_breth*breth', 'trim|required|numeric');
		$this->form_validation->set_rules('package_height', 'package_height*height', 'trim|required|numeric');
		$this->form_validation->set_rules('package_size_units', 'package_size_units*units', 'trim|required');
		$this->form_validation->set_rules('package_weight', 'package_weight*weight', 'trim|required|numeric');
		$this->form_validation->set_rules('package_weight_units', 'package_weight_units*weight units', 'trim|required');
		$this->form_validation->set_rules('package_remarks', 'package_remarks*remarks', 'trim');
		$this->form_validation->set_rules('is_agree', 'is_agree*read and agreed', 'trim|required');

		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		} else {
			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function form_22_submit() {
		if ($this->form_22_validate() !== true)
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
			$new_data->products[] = $data;

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
					'form_data' => json_encode(array('products' => array($data))),
					'created_on' => date('Y-m-d H:i:s'),
				));
		}

		$this->session->set_flashdata('message', 'Form has been submitted successfully');
		redirect(base_url('forms/form_22'));
	}

	function edit_certificate() {
		$id = $this->input->get('id');

		$this->formdata = null;
		$this->editdata = null;

		$check = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->get('es_exhibition_booking_forms_data')
			->row();

		if (isset($check)) {
			$this->formdata = json_decode($check->form_data);
		}

		$all_data = json_decode($check->form_data);
		$all_data = $all_data->products;
		$edit_data = null;
		foreach ($all_data as $data) {
			if ($data->id == $id) {
				$edit_data = $data;
			}
		}

		$this->editdata = $edit_data;

		$this->load->view('includes/after_login/head');
		$this->load->view('forms/form_22');
	}

	function edit_submit() {
		if ($this->form_22_validate() !== true)
			show_404();

		$id = $this->input->get('id');

		$data = $this->input->post();
		$data['id'] = $id;

		$check = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->get('es_exhibition_booking_forms_data')
			->row();

		$all_data = json_decode($check->form_data);
		$new_data = array();
		foreach ($all_data->products as $old_data) {
			if ($old_data->id == $id) {
				$new_data[] = $data;
			} else {
				$new_data[] = $old_data;
			}
		}
		$all_data->products = $new_data;

		$this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->update('es_exhibition_booking_forms_data', array(
				'form_data' => json_encode($all_data),
				'modified_on' => date('Y-m-d H:i:s'),
			));

		$this->session->set_flashdata('message', 'Record has been successfully updated!');
		redirect(base_url('forms/form_22'));
	}

	function delete_certificate() {
		$id = $this->input->get('id');

		$old_form = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->get('es_exhibition_booking_forms_data')
			->row();

		$all_data = json_decode($old_form->form_data);
		$new_data = array();
		foreach ($all_data->products as $data) {
			if ($data->id != $id) {
				$new_data[] = $data;
			}
		}
		$all_data->products = $new_data;

		$this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->update('es_exhibition_booking_forms_data', array(
				'form_data' => json_encode($all_data),
				'modified_on' => date('Y-m-d H:i:s'),
			));

		$this->session->set_flashdata('message', 'Record has been successfully deleted!');
		redirect(base_url('forms/form_22'));
	}

	function print_certificate() {
		$this->formdata = null;

		$check = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->get('es_exhibition_booking_forms_data')
			->row();

		if (isset($check)) {
			$this->formdata = json_decode($check->form_data);
		}

		if (is_null($this->formdata)) {
			show_404();
			exit;
		}

		$html = $this->load->view('print/end_user_certificate', array(), true);


		//echo $html; die();
		set_time_limit(0);
		$this->load->helper ("pdf-loader");
		try {
			$html2pdf = new HTML2PDF('P', 'A4', 'en');
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->writeHTML($html);
			$html2pdf->Output('end-user-certificate-'.myid($this->userdata->id).'.pdf');
		}
		catch(HTML2PDF_exception $e) {
			$msg = $e->getMessage ();
			//$msg = base64_encode($msg);
			//$msg = trim ($msg);
			print_r($msg);
		}
	}

	function upload_certificate() {
		$check = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->count_all_results('es_exhibition_booking_forms_data');

		if ($check == 0) {
			$this->session->set_flashdata('error', 'Please add products first');
			redirect(base_url('forms/form_22'));
			exit;
		}

		if(isset($_FILES['attested_certificate'])){
			$result = null;
			$tmpFilePath = $_FILES['attested_certificate']['tmp_name'];
			//Make sure we have a file path
			if ($tmpFilePath != ""){
				// check folder
				if (!file_exists('../uploads/client_form_22/')) {
					mkdir('../uploads/client_form_22/');
				}


				//Setup our new file path
				$ext = pathinfo($_FILES['attested_certificate']['name']);
				$newFileName = uniqid(time().rand(1, 1000)) . '.' .strtolower($ext['extension']);
				$newFilePath = '../uploads/client_form_22/' . $newFileName;

				//Upload the file into the temp dir
				if(move_uploaded_file($tmpFilePath, $newFilePath)) {

					$old_form = $this->db
						->where('exhibition_id', $this->event->id)
						->where('booking_id', $this->booking->id)
						->where('form_id', $this->form_id)
						->get('es_exhibition_booking_forms_data')
						->row();

					$new_data = json_decode($old_form->form_data);
					$new_data->certificate = 'uploads/client_form_22/'. $newFileName;

					$this->db
						->where('exhibition_id', $this->event->id)
						->where('booking_id', $this->booking->id)
						->where('form_id', $this->form_id)
						->update('es_exhibition_booking_forms_data', array(
							'form_data' => json_encode($new_data),
							'modified_on' => date('Y-m-d H:i:s'),
						));

					// upload successfully
					$this->session->set_flashdata('message', 'Certificate successfully uploaded');
					redirect(base_url('forms/form_22'));

				} else {
					$this->session->set_flashdata('error', 'Unable to upload certificate');
					redirect(base_url('forms/form_22'));
				}
			} else {
				$this->session->set_flashdata('error', 'Unable to upload certificate');
				redirect(base_url('forms/form_22'));
			}
		} else {
			$this->session->set_flashdata('error', 'Unable to upload certificate');
			redirect(base_url('forms/form_22'));
		}
	}
}