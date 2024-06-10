<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exhibitor_team extends MY_Controller
{
    function crd_contact_person_list() {
		$this->load->view('includes/after_login/head');
        $this->load->view('exhibitor_team/contact_person_list');
    }
    function crd_contact_person_list_datatable()
    {

		$this->load->library('datatables');
		$this->datatables
			->select('E.id, 
				  E.person_name,
				  C.company,
				  E.designation, 
				  E.primary_email, 
				  E.primary_phone,
				  E.is_active', false)
			->unset_column('E.is_active')
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<button type="button" class="btn btn-info btn-detail" data-id="' . $id . '">Detail</button>';
				return "<center>{$html}</center>";
			}, NULL)
            ->join('es_customers as C', 'C.id = E.customer_id', 'LEFT')
			->from('es_customer_contact_persons as E')
			->where('E.is_deleted', 0);

		print ($this->datatables->generate());
    }
	function get_contact_person_details (){
		$id = $this->input->post('id');
		$this->db->select('E.*, C.company');
		$this->db->from('es_customer_contact_persons as E');
		$this->db->join('es_customers as C', 'C.id = E.customer_id', 'LEFT');
		$this->db->where('E.id', $id);
		$query = $this->db->get();
		$data = $query->row_array();

		// Return the details as a formatted HTML
		echo "<strong>Person Name:</strong> " . $data['person_name'] . "<br>";
		echo "<strong>Company:</strong> " . $data['company'] . "<br>";
		echo "<strong>Designation:</strong> " . $data['designation'] . "<br>";
		echo "<strong>Primary Email:</strong> " . $data['primary_email'] . "<br>";
		echo "<strong>Secondary Email:</strong> " . $data['secondary_email'] . "<br>";
		echo "<strong>Primary Phone:</strong> " . $data['primary_phone'] . "<br>";
		echo "<strong>Secondary Phone:</strong> " . $data['secondary_phone'] . "<br>";
		echo "<strong>Office Phone:</strong> " . $data['office_phone'] . "<br>";
		echo "<strong>Active:</strong> " . ($data['is_active'] ? 'Yes' : 'No') . "<br>";
		
	}

	function crd_agent_list() {
		$this->load->view('includes/after_login/head');
        $this->load->view('exhibitor_team/agent_list');
    }
    function crd_agent_list_datatable()
    {
		$this->load->library('datatables');
		$this->datatables
			->select('es_agent.id, 
				  es_agent.agent_company, 
				  es_agent.agent_name, 
				  es_agent.agent_country, 
				  es_agent.agent_phone, 
				  es_agent.agent_email')
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<button type="button" class="btn btn-info btn-detail" data-id="' . $id . '">Detail</button>';
				return "<center>{$html}</center>";
			}, NULL)
			->from('es_agent')
            ->where('is_deleted', 0);


        if ($this->input->get('filter_event')){
            $this->datatables->join('es_exhibition_booking', 'es_exhibition_booking.agent_id = es_agent.id', 'LEFT');
            $this->datatables->where('es_exhibition_booking.exhibition_id', $this->input->get('filter_event'));
        }

		print ($this->datatables->generate());
    }
	function get_agent_details (){
		$id = $this->input->post('id');
		$this->db->select('E.*');
		$this->db->from('es_agent as E');
		$this->db->where('E.id', $id);
		$query = $this->db->get();
		$data = $query->row_array();

		// Return the details as a formatted HTML
		echo "<strong>Company:</strong> " . $data['agent_company'] . "<br>";
		echo "<strong>Name:</strong> " . $data['agent_name'] . "<br>";
		echo "<strong>Email:</strong> " . $data['agent_email'] . "<br>";
		echo "<strong>Designation:</strong> " . $data['agent_designation'] . "<br>";
		echo "<strong>Country:</strong> " . $data['agent_country'] . "<br>";
		echo "<strong>City:</strong> " . $data['agent_city'] . "<br>";
		echo "<strong>Zip Code Phone:</strong> " . $data['agent_zip_code'] . "<br>";
		echo "<strong>Address:</strong> " . $data['agent_address'] . "<br>";
		echo "<strong>Phone:</strong> " . $data['agent_phone'] . "<br>";
		echo "<strong>Fax:</strong> " . $data['agent_fax'] . "<br>";
		
	}

	function crd_organizer_list() {
		$this->load->view('includes/after_login/head');
        $this->load->view('exhibitor_team/organizer_list');
    }
    function crd_organizer_list_datatable()
    {
		$this->load->library('datatables');
		$this->datatables
			->select('es_organizer.id, 
				  es_organizer.organizer_name, 
				  es_organizer.organizer_company, 
				  es_organizer.organizer_email, 
				  es_organizer.organizer_phone, 
				  es_organizer.organizer_country,
				  es_organizer.organizer_city', false)

			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<button type="button" class="btn btn-info btn-detail" data-id="' . $id . '">Detail</button>';
				return "<center>{$html}</center>";
			}, NULL)
			->from('es_organizer')
			->where('es_organizer.is_deleted', 0);

        if ($this->input->get('filter_event')){
            $this->datatables->join('es_exhibitions', 'es_exhibitions.event_organizer = es_organizer.id', 'LEFT');
            $this->datatables->where('es_exhibitions.id', $this->input->get('filter_event'));
        }

		print ($this->datatables->generate());
    }
	function get_organizer_details (){
		$id = $this->input->post('id');
		$this->db->select('E.*');
		$this->db->from('es_organizer as E');
		$this->db->where('E.id', $id);
		$query = $this->db->get();
		$data = $query->row_array();

		// Return the details as a formatted HTML
		echo "<strong>Organizer Company:</strong> " . $data['organizer_company'] . "<br>";
		echo "<strong>Organizer Name:</strong> " . $data['organizer_name'] . "<br>";
		echo "<strong>Organizer Email:</strong> " . $data['organizer_email'] . "<br>";
		echo "<strong>Organizer Country:</strong> " . $data['organizer_country'] . "<br>";
		echo "<strong>Organizer City:</strong> " . $data['organizer_city'] . "<br>";
		echo "<strong>Organizer Phone:</strong> " . $data['organizer_phone'] . "<br>";
		echo "<strong>Organizer Image:</strong> <img src='../../" . $data['organizer_image'] . "'<br>";
	}

	function crd_stall_builder_list() {
		$this->load->view('includes/after_login/head');
        $this->load->view('exhibitor_team/stall_builder_list');
    }
    function crd_stall_builder_list_datatable()
    {
		$this->load->library('datatables');
		$this->datatables
			->select('id, 
				  company_name, 
				  person_name, 
				  phone, 
				  mobile, 
				  company_email,
				  is_active', false)
			->unset_column('is_active')
			
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<button type="button" class="btn btn-info btn-detail" data-id="' . $id . '">Detail</button>';
				return "<center>{$html}</center>";
			}, NULL)
			->from('es_stall_builders')
			->where('is_deleted', 0);
		print ($this->datatables->generate());

    }
	function get_stall_builder_details (){
		$id = $this->input->post('id');
		$this->db->select('E.*');
		$this->db->from('es_stall_builders as E');
		$this->db->where('E.id', $id);
		$query = $this->db->get();
		$data = $query->row_array();

		// Return the details as a formatted HTML
		echo "<strong>Company Name:</strong> " . $data['company_name'] . "<br>";
		echo "<strong>Company Logo:</strong> <img src='../../" . $data['company_logo'] . "'<br>";
		echo "<strong>Name Of Person:</strong> " . $data['person_name'] . "<br>";
		echo "<strong>Phone:</strong> " . $data['phone'] . "<br>";
		echo "<strong>Designation:</strong> " . $data['designation'] . "<br>";
		echo "<strong>Fax:</strong> " . $data['fax'] . "<br>";
		echo "<strong>Mobile:</strong> " . $data['mobile'] . "<br>";
		echo "<strong>Company Email:</strong> " . $data['company_email'] . "<br>";
		echo "<strong>Url:</strong> " . $data['url'] . "<br>";
		echo "<strong>Company Address:</strong> " . $data['company_address'] . "<br>";
		echo "<strong>Active:</strong> " . ($data['is_active'] ? 'Yes' : 'No') . "<br>";
	}
}