<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form_10 extends MY_Controller {
	public $form_id = 10;

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
		$this->load->view('forms/form_10');
	}

	function form_10_validate() {
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

		$visitor_badges = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->get('es_exhibition_booking')
			->row();
		if ($visitor_badges->visitor_badges_limit < 1) {
			return $this->common->doError(func_num_args(), 'You do not have enough limit to add more badge!');
		}


		$this->form_validation->set_rules('is_agree', 'is_agree*is_agree', 'trim|required');

		$this->form_validation->set_rules('visitor_name', 'visitor_name*visitor_name', 'trim|required|max_length[19]');
		$this->form_validation->set_rules('visitor_father_name', 'visitor_father_name*visitor_father_name', 'trim');
		$this->form_validation->set_rules('visitor_job_title', 'visitor_job_title*visitor_job_title', 'trim|required|max_length[19]');
		$this->form_validation->set_rules('visitor_phone', 'visitor_phone*visitor_phone', 'trim|numeric');
		$this->form_validation->set_rules('visitor_mobile', 'visitor_mobile*visitor_mobile', 'trim|required|numeric');
		$this->form_validation->set_rules('visitor_email', 'visitor_email*visitor_email', 'trim|valid_email');
		$this->form_validation->set_rules('visitor_address', 'visitor_address*visitor_address', 'trim');
		$this->form_validation->set_rules('visitor_image[0]', 'visitor_image[0]*visitor_image', 'trim');

		if ($this->input->post('visitor_passport') && $this->input->post('visitor_passport') != '') {
			$this->form_validation->set_rules('visitor_passport_date_expire', 'visitor_passport_date_expire*visitor_passport_date_expire', 'trim|required');
		} else {
			$this->form_validation->set_rules('visitor_cnic', 'visitor_cnic*CNIC or Passport', 'trim|required|numeric');
			$this->form_validation->set_rules('visitor_cnic_date_issue', 'visitor_cnic_date_issue*visitor_cnic_date_issue', 'trim|required');
			if ($this->input->post('visitor_cnic_has_lifetime')) {
				$this->form_validation->set_rules('visitor_cnic_date_expire', 'visitor_cnic_date_expire*visitor_cnic_date_expire', 'trim');
			} else {
				$this->form_validation->set_rules('visitor_cnic_date_expire', 'visitor_cnic_date_expire*visitor_cnic_date_expire', 'trim|required');
			}
		}

		$this->form_validation->set_rules('visitor_gender', 'visitor_gender*visitor_gender', 'trim|required');
		$this->form_validation->set_rules('visitor_country', 'visitor_country*visitor_country', 'trim|required');
		$this->form_validation->set_rules('visitor_dob', 'visitor_dob*visitor_dob', 'trim|required');

		$this->form_validation->set_rules('company_name', 'company_name*company_name', 'trim|required');
		$this->form_validation->set_rules('company_business_sector', 'company_business_sector*company_business_sector', 'trim|required');
		$this->form_validation->set_rules('company_country', 'company_country*company_country', 'trim|required');
		$this->form_validation->set_rules('company_city', 'company_city*company_city', 'trim');
		$this->form_validation->set_rules('company_phone', 'company_phone*company_phone', 'trim|required|numeric');
		$this->form_validation->set_rules('company_fax', 'company_fax*company_fax', 'trim|numeric');
		$this->form_validation->set_rules('company_email', 'company_email*company_email', 'trim|required|valid_email');
		$this->form_validation->set_rules('company_address', 'company_address*company_address', 'trim');


		if ($this->form_validation->run() == false){
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}
		else {
			$age = 0;
			if ($this->input->post('visitor_dob') && $this->input->post('visitor_dob') != '') {
				$age = (date('Y') - date('Y',strtotime($this->input->post('visitor_dob'))));

				if ($age < 18) {
					return $this->common->doError(func_num_args(), 'visitor_dob*Visitor must be atleast 18 years old!');
				}
			}

			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function form_10_submit() {
		if ($this->form_10_validate() !== true)
			show_404();


		$data = $this->input->post();
		$data['id'] = uniqid(time().rand(1, 1000));


		/* START INSERT BADGE */
		$badge_data = array(
			'exhibition_id' => $this->event->id,
			'booking_id' => $this->booking->id,
			'badge_type' => 'visitor',
			'full_name' => $this->input->post('visitor_name'),
			'designation' => $this->input->post('visitor_job_title'),
			'mobile' => $this->input->post('visitor_mobile'),
			'nationality' => (($this->input->post('visitor_country') == 'Pakistan') ? 'Pakistani' : $this->input->post('visitor_country')),
			'cnic' => $this->input->post('visitor_cnic'),
			'email' => $this->input->post('visitor_email'),
			'cnic_image' => null,
			'cnic_back_image' => null,
			'passport' => $this->input->post('visitor_passport'),
			'passport_image' => null,
			'passport_back_image' => null,
			'user_image' => (($this->input->post('visitor_image')) ? $this->funcs->make_image_string($this->input->post('visitor_image')) : null),
			'is_active' => 1,
			'created_on' => date('Y-m-d H:i:s'),
		);

		$has_collection = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->count_all_results('es_exhibition_badges_collection');
		if ($has_collection > 0) {
			$collection_data = $this->db
				->where('exhibition_id', $this->event->id)
				->where('booking_id', $this->booking->id)
				->get('es_exhibition_badges_collection')
				->row();

			$badge_data['collection_person_name'] = $collection_data->collection_person_name;
			$badge_data['collection_person_cnic'] = $collection_data->collection_person_cnic;
			$badge_data['collection_person_phone'] = $collection_data->collection_person_phone;
		}

		$this->db->insert('es_exhibition_badges', $badge_data);
		$this_badge_id = $this->db->insert_id();
		$data['badge_id'] = $this_badge_id;
		/* END INSERT BADGE */


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
			$new_data->badge[] = $data;

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
					'form_data' => json_encode(array('badge' => array($data))),
					'created_on' => date('Y-m-d H:i:s'),
				));
		}

		$this->session->set_flashdata('message', 'Form has been submitted successfully');
		redirect(base_url('forms/form_10'));
	}

	function delete_badge() {
		$id = $this->input->get('id');

		$old_form = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->get('es_exhibition_booking_forms_data')
			->row();

		$all_data = json_decode($old_form->form_data);
		$new_data = array();
		foreach ($all_data->badge as $data) {
			if ($data->id != $id) {
				$new_data[] = $data;
			} else {
				// remove badge
				if ($data->badge_id && $data->badge_id != '') {
					$this->db
						->where('id', $data->badge_id)
						->update('es_exhibition_badges', array(
							'is_active' => 0,
							'modified_on' => date('Y-m-d H:i:s'),
						));
				}
			}
		}
		$all_data->badge = $new_data;

		$this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->update('es_exhibition_booking_forms_data', array(
				'form_data' => json_encode($all_data),
				'modified_on' => date('Y-m-d H:i:s'),
			));

		$this->session->set_flashdata('message', 'Record has been successfully deleted!');
		redirect(base_url('forms/form_10'));
	}

	function temp_badge_insert() {
		die('die!');
		$old_forms = $this->db
			->where('form_id', $this->form_id)
			->get('es_exhibition_booking_forms_data')
			->result();

		foreach ($old_forms as $old_form) {
			$all_data = json_decode($old_form->form_data);
			$new_data = array();

			foreach ($all_data->badge as $data) {

				$badge_data = array(
					'exhibition_id' => $old_form->exhibition_id,
					'booking_id' => $old_form->booking_id,
					'badge_type' => 'visitor',
					'full_name' => $data->visitor_name,
					'designation' => $data->visitor_job_title,
					'mobile' => $data->visitor_mobile,
					'nationality' => (($data->visitor_country == 'Pakistan') ? 'Pakistani' : $data->visitor_country),
					'cnic' => $data->visitor_cnic,
					'email' => $data->visitor_email,
					'cnic_image' => null,
					'cnic_back_image' => null,
					'passport' => $data->visitor_passport,
					'passport_image' => null,
					'passport_back_image' => null,
					'user_image' => ((isset($data->visitor_image)) ? $this->funcs->make_image_string($data->visitor_image) : null),
					'is_active' => 1,
					'created_on' => date('Y-m-d H:i:s'),
				);

				$this->db->insert('es_exhibition_badges', $badge_data);
				$this_badge_id = $this->db->insert_id();

				$data->badge_id = $this_badge_id;
				$new_data[] = $data;
			}

			$all_data->badge = $new_data;

			$this->db
				->where('id', $old_form->id)
				->update('es_exhibition_booking_forms_data', array(
					'form_data' => json_encode($all_data),
					'modified_on' => date('Y-m-d H:i:s'),
				));
		}


		echo 'data updated!';
		die();
	}
}