<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Portal extends MY_Controller {

	function index() {

		$organizer = $this->db
			->where('id', $this->event->event_organizer)
			->get('es_organizer')
			->row();

		$event_date = $this->db
			->where('exhibition_id', $this->event->id)
			->order_by('date', 'ASC')
			->get('es_exhibition_date')
			->result();

		$stalls = $this->db
			->where('booking_id', $this->booking->id)
			->get('es_exhibition_stalls')
			->result();

		$items = $this->db
			->select('SUM(quantity) as total_quantity')
			->where('booking_id', $this->booking->id)
			->where('is_package_item', 1)
			->get('es_exhibition_booking_items')
			->row();

		$additional_items = $this->db
			->select('SUM(quantity) as total_quantity')
			->where('booking_id', $this->booking->id)
			->where('is_package_item', 0)
			->get('es_exhibition_booking_items')
			->row();

		/*$hall_data = $this->db
			->where('id', $stalls[0]->hall_id)
			->get('es_location_halls')
			->row();*/

		$booking_package_items = $this->db
			->select('I.*, P.package_title, P.package_price_usd, P.package_price_pkr, IT.item_title')
			->where('I.booking_id', $this->booking->id)
			->where('I.is_package_item', 1)
			->join('es_inventory_item as IT', 'IT.id = I.item_id', 'LEFT')
			->join('es_packages as P', 'P.id = I.package_id', 'LEFT')
			->get('es_exhibition_booking_items as I')
			->result();

		$booking_extra_items = $this->db
			->select('I.*, P.package_title, P.package_price_usd, P.package_price_pkr, IT.item_title')
			->where('I.booking_id', $this->booking->id)
			->where('I.is_package_item', 0)
			->join('es_inventory_item as IT', 'IT.id = I.item_id', 'LEFT')
			->join('es_packages as P', 'P.id = I.package_id', 'LEFT')
			->get('es_exhibition_booking_items as I')
			->result();

		$this->load->view('includes/after_login/head');
		$this->load->view('dashboard', array(
			'organizer' => $organizer,
			'event_date' => $event_date,
			'stalls' => $stalls,
			//'hall_data' => $hall_data,
			'items' => $items->total_quantity,
			'additional_items' => $additional_items->total_quantity,
			'booking_package_items' => $booking_package_items,
			'booking_extra_items' => $booking_extra_items,
		));
	}

	### Start my_profile
	function my_profile() {
		$this->load->view('includes/after_login/head');
		$this->load->view('profile');
	}

	function my_profile_validate() {
		$this->load->library('validator');

		//$this->form_validation->set_rules('company', 'company*company', 'trim|required|set_edit_mood[id.' . $this->userdata->id . ']|is_unique[es_customers.company]');
		//$this->form_validation->set_rules('name', 'name*name', 'trim|required');
		$this->form_validation->set_rules('designation', 'designation*designation', 'trim');
		//$this->form_validation->set_rules('email', 'email*email', 'trim|required|valid_email|set_edit_mood[id.' . $this->userdata->id . ']|is_unique[es_customers.email]');
		$this->form_validation->set_rules('url', 'url*url', 'trim');
		$this->form_validation->set_rules('phone', 'phone*telephone', 'trim|numeric|min_length[8]');
		$this->form_validation->set_rules('fax', 'fax*fax', 'trim|numeric');
		$this->form_validation->set_rules('country', 'country*country', 'trim');
		$this->form_validation->set_rules('city', 'city*city', 'trim');
		$this->form_validation->set_rules('zip_code', 'zip_code*zip_code', 'trim|numeric');
		$this->form_validation->set_rules('address', 'address*address', 'trim');

		$this->form_validation->set_rules('contact_person', 'contact_person*contact person', 'trim|required');
		$this->form_validation->set_rules('contact_person_designation', 'contact_person_designation*contact person designation', 'trim');
		$this->form_validation->set_rules('contact_person_email', 'contact_person_email*contact person email', 'trim|required|valid_email');
		$this->form_validation->set_rules('contact_person_phone', 'contact_person_phone*contact person phone', 'trim|numeric|min_length[8]');

		if ($this->form_validation->run() == false) {
			return $this->common->doError(func_num_args(), $this->common->getFVError());
		} else {
			return $this->common->doError(func_num_args(), "done", true);
		}
	}

	function my_profile_submit() {
		if ($this->my_profile_validate() !== true) {
			show_404();
		}


		$data = array(
			//'company' => $this->input->post('company'),
			//'name' => $this->input->post('name'),
			'designation' => $this->input->post('designation'),
			//'email' => $this->input->post('email'),
			'url' => $this->input->post('url'),
			'phone' => $this->input->post('phone'),
			'fax' => $this->input->post('fax'),
			'city' => $this->input->post('city'),
			'country' => $this->input->post('country'),
			'zip_code' => $this->input->post('zip_code'),
			'address' => $this->input->post('address'),
			'contact_person' => $this->input->post('contact_person'),
			'contact_person_designation' => $this->input->post('contact_person_designation'),
			'contact_person_email' => $this->input->post('contact_person_email'),
			'contact_person_phone' => $this->input->post('contact_person_phone'),
		);

		if ($this->userdata->contact_person_email != $this->input->post('contact_person_email')) {
			//TODO send email to exhibitor to inform that information has changed
		}

		$this->db->trans_start();
		$this->db
			->set($data)
			->where('id', $this->userdata->id)
			->update('es_customers');
		$this->db->trans_complete();

		$this->db
			->where('customer_id', $this->userdata->id)
			->update('es_customer_contact_persons', array(
				'person_name' => $this->input->post('contact_person'),
				'designation' => $this->input->post('contact_person_designation'),
				'primary_email' => $this->input->post('contact_person_email'),
				'primary_phone' => $this->input->post('contact_person_phone'),
			));

		$this->session->set_flashdata('message', 'Profile successfully updated.');
		redirect(base_url('my-profile'));
	}


	### End my_profile


    function my_profile_password_validate() {

            $this->form_validation->set_rules('opassword', 'opassword*Password', 'trim|required');
            $this->form_validation->set_rules('password', 'password*Password', 'trim|required|min_length[8]|password_check');
            $this->form_validation->set_rules('cpassword', 'cpassword*Confirm Password', 'trim|required|matches[password]');


        if ($this->form_validation->run() == false) {
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        } else {

            $check = $this->db
                ->where('id', $this->booking->id)
                ->where('login_password', md5($this->input->post('opassword')))
                ->count_all_results('es_exhibition_booking');

            if ($check == 0) {
                return $this->common->doError(func_num_args(), 'Current password is not correct!');
            }

            if ($this->input->post('password') != $this->input->post('cpassword')) {
                return $this->common->doError(func_num_args(), 'Confirm password does not match with new password');
            }

            return $this->common->doError(func_num_args(), "done", true);
        }
    }

    function my_profile_password(){
        if ($this->my_profile_password_validate() !== true) {
            show_404();
        }

        $new_password  = $this->input->post('password');
        $data = array(
            'login_password' =>md5($new_password),
        );

        $this->db->trans_start();
        $this->db
            ->set($data)
            ->where('id', $this->booking->id)
            ->update('es_exhibition_booking');
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'Password update successfully.');
        redirect(base_url('my-profile'));

    }
}