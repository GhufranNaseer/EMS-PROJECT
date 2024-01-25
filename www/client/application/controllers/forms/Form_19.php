<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form_19 extends MY_Controller {

	public $form_id = 19;


	function index() {
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

		$this->load->view('includes/after_login/head');
		$this->load->view('forms/form_19');
	}

	function form_19_validate() {
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


		if ($this->input->post('cnic')) {
			$_POST['cnic'] = str_replace('-', '', $this->input->post('cnic'));
		}
		if ($this->input->post('mobile')) {
			$_POST['mobile'] = str_replace(array('+','-','_'), '', $this->input->post('mobile'));
		}
		// check badge allow limit
		$booking_badges = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('badge_type', 'exhibitor')
			->where('is_active', 1)
			->count_all_results('es_exhibition_badges');
		if ($booking_badges >= $this->booking->badges_total_limit) {
			return $this->common->doError(func_num_args(), 'You do not have enough limit to add more badge!');
		}

		$this->form_validation->set_rules('full_name', 'full_name*person full name', 'trim|required|max_length[19]');
		$this->form_validation->set_rules('designation', 'designation*designation', 'trim|required|max_length[19]');
		$this->form_validation->set_rules('mobile', 'mobile*mobile', 'trim|required|numeric|min_length[12]');
		$this->form_validation->set_rules('nationality', 'nationality*nationality', 'trim|required');
		$this->form_validation->set_rules('email', 'email*email', 'trim|valid_email');
		$this->form_validation->set_rules('user_image[0]', 'user_image[0]*picture', 'trim|required');

		if ($this->input->post('nationality') && $this->input->post('nationality') == 'Pakistani') {
			$this->form_validation->set_rules('cnic', 'cnic*cnic', 'trim|required|numeric|min_length[13]');
			$this->form_validation->set_rules('cnic_image[0]', 'cnic_image[0]*cnic front image', 'trim|required');
			$this->form_validation->set_rules('cnic_back_image[0]', 'cnic_back_image[0]*cnic back image', 'trim|required');

			$this->form_validation->set_rules('passport', 'passport*passport', 'trim');
			$this->form_validation->set_rules('passport_image[0]', 'passport_image[0]*passport first page image', 'trim');
			$this->form_validation->set_rules('passport_back_image[0]', 'passport_back_image[0]*passport visa page image', 'trim');
		} else {
			$this->form_validation->set_rules('cnic', 'cnic*cnic/passport', 'trim|numeric|min_length[13]');
			$this->form_validation->set_rules('cnic_image[0]', 'cnic_image[0]*cnic front image', 'trim');
			$this->form_validation->set_rules('cnic_back_image[0]', 'cnic_back_image[0]*cnic back image', 'trim');

			$this->form_validation->set_rules('passport', 'passport*passport', 'trim|required');
			$this->form_validation->set_rules('passport_image[0]', 'passport_image[0]*passport first page image', 'trim|required');
			$this->form_validation->set_rules('passport_back_image[0]', 'passport_back_image[0]*passport visa page image', 'trim');
		}

		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}
		else {
			if ($this->input->post('nationality') && $this->input->post('nationality') == 'Pakistani') {
				$check_cnic = $this->db
					->where('exhibition_id', $this->event->id)
					->where('is_active', 1)
					->where('cnic', $this->input->post('cnic'))
					->count_all_results('es_exhibition_badges');
				if ($check_cnic > 0) {
					$check_visitor = $this->db
						->where('exhibition_id', $this->event->id)
						->where('is_active', 1)
						->where('cnic', $this->input->post('cnic'))
						->get('es_exhibition_badges')
						->row();
					// if its visitor the disable visitor badge otherwise show error
					if ($check_visitor->badge_type == 'visitor') {
						$this->db
							->where('id', $check_visitor->id)
							->update('es_exhibition_badges', array(
								'is_active' => 0,
								'modified_on' => date('Y-m-d H:i:s'),
							));
					} else {
						return $this->common->doError(func_num_args(), 'cnic*CNIC already exist!');
					}
				}
			}
			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function form_19_submit() {
		if ($this->form_19_validate() !== true)
			show_404();

		$badge_data = array(
			'exhibition_id' => $this->event->id,
			'booking_id' => $this->booking->id,
			'badge_type' => 'exhibitor',
			'full_name' => $this->input->post('full_name'),
			'designation' => $this->input->post('designation'),
			'mobile' => $this->input->post('mobile'),
			'nationality' => $this->input->post('nationality'),
			'cnic' => $this->input->post('cnic'),
			'email' => $this->input->post('email'),
			'cnic_image' => $this->funcs->make_image_string($this->input->post('cnic_image')),
			'cnic_back_image' => $this->funcs->make_image_string($this->input->post('cnic_back_image')),
			'passport' => $this->input->post('passport'),
			'passport_image' => $this->funcs->make_image_string($this->input->post('passport_image')),
			'passport_back_image' => $this->funcs->make_image_string($this->input->post('passport_back_image')),
			'user_image' => $this->funcs->make_image_string($this->input->post('user_image')),
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

		$badge_id = $this->db->insert_id();


		// save form data
		$check = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('form_id', $this->form_id)
			->count_all_results('es_exhibition_booking_forms_data');

		$exhibitors_badges = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('is_active', 1)
			->get('es_exhibition_badges')
			->result();

		if ($check > 0) {
			$old_form = $this->db
				->where('exhibition_id', $this->event->id)
				->where('booking_id', $this->booking->id)
				->where('form_id', $this->form_id)
				->get('es_exhibition_booking_forms_data')
				->row();

			$data = json_decode($old_form->form_data);
			$data->badges = array_map(function ($badge){ return $badge->id; }, $exhibitors_badges);

			$this->db
				->where('exhibition_id', $this->event->id)
				->where('booking_id', $this->booking->id)
				->where('form_id', $this->form_id)
				->update('es_exhibition_booking_forms_data', array(
					'form_data' => json_encode($data),
					'modified_on' => date('Y-m-d H:i:s'),
				));
		} else {
			$data = array('badges' => array_map(function ($badge){ return $badge->id; }, $exhibitors_badges));

			$this->db
				->where('exhibition_id', $this->event->id)
				->where('booking_id', $this->booking->id)
				->where('form_id', $this->form_id)
				->insert('es_exhibition_booking_forms_data', array(
					'exhibition_id' => $this->event->id,
					'booking_id' => $this->booking->id,
					'form_id' => $this->form_id,
					'form_data' => json_encode($data),
					'created_on' => date('Y-m-d H:i:s'),
				));
		}

		$this->session->set_flashdata('message', 'Exhibitor Badge added successfully');
		redirect(base_url('forms/form_19'));
	}

	function update_badge_invitations() {
		$badge_id = $this->input->post('badge_id');
		$invitations = $this->input->post('invitations');

		if (is_null($badge_id) || is_null($invitations)) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'required parameters is missing'
			));
			exit;
		}

		$this->db
			->where('exhibition_id', $this->event->id)
			->where('badge_id', $badge_id)
			->delete('es_exhibition_badges_invitation');


		$data = array();
		foreach ($invitations as $invitation) {
			$limit = $this->db
				->where('exhibition_id', $this->event->id)
				->where('booking_id', $this->booking->id)
				->where('badge_type', 'exhibitor')
				->where('invitation_type', $invitation)
				->get('es_exhibition_badges_limit')
				->row();

			$check = $this->db
				->where('exhibition_id', $this->event->id)
				->where('booking_id', $this->booking->id)
				->where('invitation_type', $invitation)
				->count_all_results('es_exhibition_badges_invitation');

			if (!isset($limit) || $check >= $limit->quantity) {
				echo json_encode(array(
					'error' => 1,
					'message' => 'You do not have enough invitation for ' . str_replace('_', ' ', $invitation)
				));
				exit;
			}

			$data[] = array(
				'exhibition_id' => $this->event->id,
				'booking_id' => $this->booking->id,
				'badge_id' => $badge_id,
				'invitation_type' => $invitation,
				'created_on' => date('Y-m-d H:i:s'),
			);
		}
		if (count($data) > 0) {
			$this->db->insert_batch('es_exhibition_badges_invitation', $data);
		}

		echo json_encode(array(
			'error' => 0,
			'message' => 'Badges and invitation successfully saved'
		));
	}


	function get_badge_invitations() {
		if (!is_null($this->input->post('badge_id'))) {
			$this->db->where('id', $this->input->post('badge_id'));
		}
		$badges = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('badge_type', 'exhibitor')
			->where('is_active', 1)
			->get('es_exhibition_badges')
			->result();

		foreach ($badges as $key => $badge) {
			$invitations = $this->db
				->where('badge_id', $badge->id)
				->get('es_exhibition_badges_invitation')
				->result();

			$badges[$key]->invitations = array_map(function ($invite) { return $invite->invitation_type; }, $invitations);
		}

		echo json_encode(array(
			'error' => 0,
			'data' => $badges
		));
	}

	function get_badge_invitation_limit() {
		$limit_badges = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('badge_type', 'exhibitor')
			->get('es_exhibition_badges_limit')
			->result();

		foreach ($limit_badges as $limit_badge) {
			$limit_badge->total = $this->db
				->where('exhibition_id', $this->event->id)
				->where('booking_id', $this->booking->id)
				->where('invitation_type', $limit_badge->invitation_type)
				->count_all_results('es_exhibition_badges_invitation');
		}

		echo json_encode(array(
			'error' => 0,
			'data' => $limit_badges
		));
	}

	function disable_exhibitor_badge() {
		$badge_id = $this->input->post('badge_id');

		if (is_null($badge_id)) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'required parameters is missing'
			));
			exit;
		}

		$check = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('id', $badge_id)
			->count_all_results('es_exhibition_badges');

		if ($check == 0) {
			echo json_encode(array(
				'error' => 1,
				'message' => 'Something went wrong! Record not found!'
			));
			exit;
		}

		$this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->where('id', $badge_id)
			->update('es_exhibition_badges', array(
				'is_active' => 0,
				'modified_on' => date('Y-m-d H:i:s')
			));

		$this->db
			->where('badge_id', $badge_id)
			->delete('es_exhibition_badges_invitation');


		$this->session->set_flashdata('message', 'Exhibitor Badge successfully deleted!');
		echo json_encode(array(
			'error' => 0,
			'data' => base_url('forms/form_19')
		));
	}


	function edit_exhibitor_validate() {
		if (!$this->input->post('badge_id')) {
			return $this->common->doError(func_num_args(), 'Badge id is required!');
		}
		if ($this->input->post('cnic')) {
			$_POST['cnic'] = str_replace('-', '', $this->input->post('cnic'));
		}
		if ($this->input->post('mobile')) {
			$_POST['mobile'] = str_replace(array('+','-','_'), '', $this->input->post('mobile'));
		}

		$this->form_validation->set_rules('full_name', 'full_name*person full name', 'trim|required|max_length[36]');
		$this->form_validation->set_rules('designation', 'designation*designation', 'trim|required|max_length[36]');
		$this->form_validation->set_rules('mobile', 'mobile*mobile', 'trim|required|numeric|min_length[12]');
		$this->form_validation->set_rules('nationality', 'nationality*nationality', 'trim|required');
		$this->form_validation->set_rules('email', 'email*email', 'trim|valid_email');
		$this->form_validation->set_rules('user_image[0]', 'user_image[0]*picture', 'trim|required');

		if ($this->input->post('nationality') && $this->input->post('nationality') == 'Pakistani') {
			$this->form_validation->set_rules('cnic', 'cnic*cnic', 'trim|required|numeric|min_length[13]');
			$this->form_validation->set_rules('cnic_image[0]', 'cnic_image[0]*cnic front image', 'trim|required');
			$this->form_validation->set_rules('cnic_back_image[0]', 'cnic_back_image[0]*cnic back image', 'trim|required');

			$this->form_validation->set_rules('passport', 'passport*passport', 'trim');
			$this->form_validation->set_rules('passport_image[0]', 'passport_image[0]*passport first page image', 'trim');
			$this->form_validation->set_rules('passport_back_image[0]', 'passport_back_image[0]*passport visa page image', 'trim');
		} else {
			$this->form_validation->set_rules('cnic', 'cnic*cnic/passport', 'trim|numeric|min_length[13]');
			$this->form_validation->set_rules('cnic_image[0]', 'cnic_image[0]*cnic front image', 'trim');
			$this->form_validation->set_rules('cnic_back_image[0]', 'cnic_back_image[0]*cnic back image', 'trim');

			$this->form_validation->set_rules('passport', 'passport*passport', 'trim|required');
			$this->form_validation->set_rules('passport_image[0]', 'passport_image[0]*passport first page image', 'trim|required');
			$this->form_validation->set_rules('passport_back_image[0]', 'passport_back_image[0]*passport visa page image', 'trim|required');
		}


		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		}
		else {
			if ($this->input->post('nationality') && $this->input->post('nationality') == 'Pakistani') {
				$check_cnic = $this->db
					->where('exhibition_id', $this->event->id)
					->where('is_active', 1)
					->where('cnic', $this->input->post('cnic'))
					->where('id !=', $this->input->post('badge_id'))
					->count_all_results('es_exhibition_badges');
				if ($check_cnic > 0) {
					$check_visitor = $this->db
						->where('exhibition_id', $this->event->id)
						->where('is_active', 1)
						->where('cnic', $this->input->post('cnic'))
						->where('id !=', $this->input->post('badge_id'))
						->get('es_exhibition_badges')
						->row();
					// if its visitor the disable visitor badge otherwise show error
					if ($check_visitor->badge_type == 'visitor') {
						$this->db
							->where('id', $check_visitor->id)
							->update('es_exhibition_badges', array(
								'is_active' => 0,
								'modified_on' => date('Y-m-d H:i:s'),
							));
					} else {
						return $this->common->doError(func_num_args(), 'cnic*CNIC already exist!');
					}
				}
			}
			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function edit_exhibitor_submit() {
		if ($this->edit_exhibitor_validate() !== true)
			show_404();

		$badge_data = array(
			'full_name' => $this->input->post('full_name'),
			'designation' => $this->input->post('designation'),
			'mobile' => $this->input->post('mobile'),
			'nationality' => $this->input->post('nationality'),
			'cnic' => $this->input->post('cnic'),
			'email' => $this->input->post('email'),
			'cnic_image' => $this->funcs->make_image_string($this->input->post('cnic_image')),
			'cnic_back_image' => $this->funcs->make_image_string($this->input->post('cnic_back_image')),
			'passport' => $this->input->post('passport'),
			'passport_image' => $this->funcs->make_image_string($this->input->post('passport_image')),
			'passport_back_image' => $this->funcs->make_image_string($this->input->post('passport_back_image')),
			'user_image' => $this->funcs->make_image_string($this->input->post('user_image')),
			'is_active' => 1,
			'created_on' => date('Y-m-d H:i:s'),
		);

		$this->db
			->where('id', $this->input->post('badge_id'))
			->update('es_exhibition_badges', $badge_data);

		$this->session->set_flashdata('message', 'Exhibitor Badge updated successfully');
		redirect(base_url('forms/form_19'));
	}


	function update_collection_person() {

		$this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->delete('es_exhibition_badges_collection');

		if ($this->input->post('collection_person_name') != '' ||
			$this->input->post('collection_person_cnic') != '' ||
			$this->input->post('collection_person_phone') != '') {

			$this->db
				->insert('es_exhibition_badges_collection', array(
					'exhibition_id' => $this->event->id,
					'booking_id' => $this->booking->id,
					'collection_person_name' => $this->input->post('collection_person_name'),
					'collection_person_cnic' => $this->input->post('collection_person_cnic'),
					'collection_person_phone' => $this->input->post('collection_person_phone'),
					'created_on' => date('Y-m-d H:i:s')
				));
		}

		$this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $this->booking->id)
			->update('es_exhibition_badges', array(
				'collection_person_name' => $this->input->post('collection_person_name'),
				'collection_person_cnic' => $this->input->post('collection_person_cnic'),
				'collection_person_phone' => $this->input->post('collection_person_phone'),
			));

		$this->session->set_flashdata('message', 'Collection Person updated successfully');
		redirect(base_url('forms/form_19'));
	}
}