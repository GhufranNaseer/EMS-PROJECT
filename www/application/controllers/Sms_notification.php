<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms_notification extends MY_Controller {
    protected function rule() {
        $this->activateRightsSystem();
        $crd = array(
            'crd_add,crd_add_submit,crd_add_email,crd_add_email_submit,get_main_company_detail,crd_add_individual,crd_add_submit_individual' => array(
                'rule' => '@'
            ),
            'crd_add_validate,crd_add_email_validate,crd_add_validate_individual' => array(
                'rule' => '@',
                'ajaxOnly' => true
            )
        );
        $this->load->model('usermdl');
        $this->myparent = base_url('organizer.html');
        return array_merge($crd);
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
            ->get('es_organizer');
        if ($r->num_rows() == 0)
            show_404();
        $this->formdata = $r->row();
        //if ($this->formdata->id == $this->userdata->id)
        //	show_404();

    }


    function crd_add() {
        $this->load->view('includes/after_login/head');
        $this->load->view('sms_notification/add');
    }

    function crd_add_submit() {
        if ($this->crd_add_validate() !== true)
            show_404();

        $is_select_all = (is_null($this->input->post('select_all')) ? 0 : 1);

        if ($is_select_all == 1) {
            $companies = $this->db
                ->select('C.*')
                ->where('B.exhibition_id',$this->input->post('event'))
                ->join('es_customers as C', 'C.id = B.customer_id')
                ->get('es_exhibition_booking as B')
                ->result();
        } else {
            $companies = $this->db
                ->where('id',$this->input->post('exhibit_companies'))
                ->get('es_customers')
                ->result();
        }

        foreach ($companies as $number) {
            if ($this->input->post('status') == 'Executive') {
                if ($number->phone && $number->phone != '' && $number->phone != null) {
                    $this->db->insert('es_sms_notification', array(
                        'customer_id' => $number->id,
                        'exhibition_id' => $this->input->post('event'),
                        'phone_number' => $number->phone,
                        'text_message' => $this->input->post('text_message'),
                        'created_on' => date('Y-m-d H:i:s'),
                    ));
                }
            } else if ($this->input->post('status') == 'Contact Person') {
                if ($number->contact_person_phone && $number->contact_person_phone != '' && $number->contact_person_phone != null) {
                    $this->db->insert('es_sms_notification', array(
                        'customer_id' => $number->id,
                        'exhibition_id' => $this->input->post('event'),
                        'phone_number' => $number->contact_person_phone,
                        'text_message' => $this->input->post('text_message'),
                        'created_on' => date('Y-m-d H:i:s'),
                    ));
                }
            }else {

                if ($number->phone && $number->phone != '' && $number->phone != null) {
                    $this->db->insert('es_sms_notification', array(
                        'customer_id' => $number->id,
                        'exhibition_id' => $this->input->post('event'),
                        'phone_number' => $number->phone,
                        'text_message' => $this->input->post('text_message'),
                        'created_on' => date('Y-m-d H:i:s'),
                    ));
                }
                if ($number->contact_person_phone && $number->contact_person_phone != '' && $number->contact_person_phone != null) {
                    $this->db->insert('es_sms_notification', array(
                        'customer_id' => $number->id,
                        'exhibition_id' => $this->input->post('event'),
                        'phone_number' => $number->contact_person_phone,
                        'text_message' => $this->input->post('text_message'),
                        'created_on' => date('Y-m-d H:i:s'),
                    ));
                }

            }
        }


        $this->session->set_flashdata('message', 'Sms Notification has been created successfully');
        redirect(base_url('sms-send-to-companies'));
    }

    function crd_add_validate() {

        $this->form_validation->set_rules('event', 'event*event', 'trim|required');
        $this->form_validation->set_rules('exhibit_companies', 'exhibit_companies*Exhibit companies', 'trim|required');
        $this->form_validation->set_rules('status', 'status*Status', 'trim|required');
        $this->form_validation->set_rules('text_message', 'text_message*Text Message', 'trim|required');

        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else
            return $this->common->doError(func_num_args(), "done", true);
    }

    function crd_add_individual() {
        $this->load->view('includes/after_login/head');
        $this->load->view('sms_notification/add_individual');
    }

    function crd_add_submit_individual() {
        if ($this->crd_add_validate_individual() !== true)
            show_404();
        //print_r($number);die();

        $data = array(
            //'customer_id' => $number->id,
            'exhibition_id' => $this->input->post('event'),
            'phone_number' => $this->input->post('phone_number'),
            'text_message' => $this->input->post('text_message'),
            'created_on' => date('Y-m-d H:i:s'),
        );

        //$this->db->trans_start();

        //$id = $this->db->insert_id();
        $this->db
            ->set($data)
            ->insert('es_sms_notification');
        //$this->db->trans_complete();

        $this->session->set_flashdata('message', 'Sms Notification has been created successfully');
        redirect(base_url('sms-individual-send'));
    }

    function crd_add_validate_individual() {

        $this->form_validation->set_rules('event', 'event*event', 'trim|required');
        $this->form_validation->set_rules('phone_number', 'phone_number*phone number', 'trim|required');
        $this->form_validation->set_rules('text_message', 'text_message*Text message', 'trim|required');

        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else
            return $this->common->doError(func_num_args(), "done", true);
    }

	function crd_add_email() {
        $this->load->view('includes/after_login/head');
        $this->load->view('sms_notification/add_email');
    }

    function crd_add_email_submit() {
        if ($this->crd_add_email_validate() !== true)
            show_404();

		$exhibition = $this->db->select('exhibition_title')->where('id', $this->input->post('event'))->get('es_exhibitions')->row();

        $is_select_all = (is_null($this->input->post('select_all')) ? 0 : 1);

        if ($is_select_all == 1) {
            $companies = $this->db
                ->select('C.*')
                ->where('B.exhibition_id',$this->input->post('event'))
                ->join('es_customers as C', 'C.id = B.customer_id')
                ->get('es_exhibition_booking as B')
                ->result();
        } else {
            $companies = $this->db
                ->where('id',$this->input->post('exhibit_companies'))
                ->get('es_customers')
                ->result();
        }

        foreach ($companies as $contact) {
            if ($this->input->post('status') == 'Executive') {
                if ($contact->email && $contact->email != '' && $contact->email != null) {
					$this->db->insert('es_emails_cron', array(
						'type' => 'CUSTOM_BULK_EMAILS',
						'data' => json_encode(array(
							'customer_id' => $contact->id,
							'exhibition_id' => $this->input->post('event'),
						)),
						'from_name' => (isset($exhibition)) ? $exhibition->exhibition_title : null,
						'email' => $contact->email,
						'subject' => $this->input->post('email_subject'),
						'message' => $this->input->post('email_message'),
						'created_on' => date('Y-m-d H:i:s'),
					));
                }
            } else if ($this->input->post('status') == 'Contact Person') {
                if ($contact->contact_person_email && $contact->contact_person_email != '' && $contact->contact_person_email != null) {
                    $this->db->insert('es_emails_cron', array(
						'type' => 'CUSTOM_BULK_EMAILS',
						'data' => json_encode(array(
							'customer_id' => $contact->id,
							'exhibition_id' => $this->input->post('event'),
						)),
						'from_name' => (isset($exhibition)) ? $exhibition->exhibition_title : null,
						'email' => $contact->contact_person_email,
						'subject' => $this->input->post('email_subject'),
						'message' => $this->input->post('email_message'),
						'created_on' => date('Y-m-d H:i:s'),
					));
                }
            }else {

                if ($contact->email && $contact->email != '' && $contact->email != null) {
					$this->db->insert('es_emails_cron', array(
						'type' => 'CUSTOM_BULK_EMAILS',
						'data' => json_encode(array(
							'customer_id' => $contact->id,
							'exhibition_id' => $this->input->post('event'),
						)),
						'from_name' => (isset($exhibition)) ? $exhibition->exhibition_title : null,
						'email' => $contact->email,
						'subject' => $this->input->post('email_subject'),
						'message' => $this->input->post('email_message'),
						'created_on' => date('Y-m-d H:i:s'),
					));
                }
                if ($contact->contact_person_email && $contact->contact_person_email != '' && $contact->contact_person_email != null) {
                    $this->db->insert('es_emails_cron', array(
						'type' => 'CUSTOM_BULK_EMAILS',
						'data' => json_encode(array(
							'customer_id' => $contact->id,
							'exhibition_id' => $this->input->post('event'),
						)),
						'from_name' => (isset($exhibition)) ? $exhibition->exhibition_title : null,
						'email' => $contact->contact_person_email,
						'subject' => $this->input->post('email_subject'),
						'message' => $this->input->post('email_message'),
						'created_on' => date('Y-m-d H:i:s'),
					));
                }

            }
        }


        $this->session->set_flashdata('message', 'Email Notification has been created successfully');
        redirect(base_url('email-send-to-companies'));
    }

    function crd_add_email_validate() {

        $this->form_validation->set_rules('event', 'event*event', 'trim|required');
        $this->form_validation->set_rules('exhibit_companies', 'exhibit_companies*Exhibit companies', 'trim|required');
        $this->form_validation->set_rules('status', 'status*Status', 'trim|required');
        $this->form_validation->set_rules('email_subject', 'email_subject*Email Subject', 'trim|required');
        // $this->form_validation->set_rules('email_message', 'email_message*Email Message', 'trim|required');

        if ($this->form_validation->run() == false)
            return $this->common->doError(func_num_args(), $this->common->getFVError());
        else
            return $this->common->doError(func_num_args(), "done", true);
    }


    function get_main_company_detail() {

        $main_category = $this->db
            ->select('C.*')
            ->where('B.exhibition_id', $this->input->post('id'))
            ->join('es_customers as C', 'C.id = B.customer_id', 'LEFT')
            ->get('es_exhibition_booking as B')
            ->result();
        //print_r($main_category );die();

        $html = '';
        $html .= '<option>-Select-</option>';
        foreach($main_category as $main){
            $html .= '<option value=" '.$main->id.' ">' . $main->company . '</option>';
        }

        echo $html;
    }

}