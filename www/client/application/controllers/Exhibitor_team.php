<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exhibitor_team extends MY_Controller
{

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

		$html = '<table class="table table-striped">';
		$html .= '<tr><th>Company:</th><td>'. $data['agent_company'] .'</td></tr>';
		$html .= '<tr><th>Name:</th><td>'. $data['agent_name'] .'</td></tr>';
		$html .= '<tr><th>Email:</th><td>'. $data['agent_email'] .'</td></tr>';
		$html .= '<tr><th>Designation:</th><td>'. $data['agent_designation'] .'</td></tr>';
		$html .= '<tr><th>Country:</th><td>'. $data['agent_country'] .'</td></tr>';
		$html .= '<tr><th>City:</th><td>'. $data['agent_city'] .'</td></tr>';
		$html .= '<tr><th>Zip Code Phone:</th><td>'. $data['agent_zip_code'] .'</td></tr>';
		$html .= '<tr><th>Address:</th><td>'. $data['agent_address'] .'</td></tr>';
		$html .= '<tr><th>Phone:</th><td>'. $data['agent_phone'] .'</td></tr>';
		$html .= '<tr><th>Fax:</th><td>'. $data['agent_fax'] .'</td></tr>';
		$html .= '</table>';

		echo $html;
		
	}

	function get_badges (){
		$id = $this->input->post('id');

		$exhibitors_badges = $this->db
			->where('exhibition_id', $this->event->id)
			->where('booking_id', $id)
			->where('badge_type', 'exhibitor')
			->where('is_active', 1)
			->get('es_exhibition_badges')
			->result();
	
		if (count($exhibitors_badges) > 0) {
			foreach ($exhibitors_badges as $badge) {
				$html = '<tr>
				<td>'. $badge->full_name .'</td>
				<td>'. $badge->designation .'</td>
				<td>
				</td>
				</tr>';

				echo $html;
			}
		} else {
			echo '<tr><td colspan="8">No record found!</td></tr>';
		}
		
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
		$html = '<table class="table table-striped">';
		$html .= '<tr>';
		$html .= '<td rowspan="7" class="text-center"><img src="'.base_url('../' . $data['organizer_image']).'" class="img-responsive" style="max-width: 150px"></td>';
		$html .= '</tr>';
		$html .= '<tr><th>Company:</th><td>'. $data['organizer_company'] .'</td></tr>';
		$html .= '<tr><th>Name:</th><td>'. $data['organizer_name'] .'</td></tr>';
		$html .= '<tr><th>Email:</th><td>'. $data['organizer_email'] .'</td></tr>';
		$html .= '<tr><th>Country:</th><td>'. $data['organizer_country'] .'</td></tr>';
		$html .= '<tr><th>City:</th><td>'. $data['organizer_city'] .'</td></tr>';
		$html .= '<tr><th>Phone:</th><td>'. $data['organizer_phone'] .'</td></tr>';
		$html .= '</table>';

		echo $html;
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
		$this->db->where('E.is_active', 1);
		$query = $this->db->get();
		$data = $query->row_array();

		// Return the details as a formatted HTML
		$html = '<table class="table table-striped">';
		$html .= '<tr>';
		$html .= '<td rowspan="10" class="text-center"><img src="'.base_url('../' . $data['company_logo']).'" class="img-responsive" style="max-width: 150px"></td>';
		$html .= '</tr>';
		$html .= '<tr><th>Stall Builder Company:</th><td>'. $data['company_name'] .'</td></tr>';
		$html .= '<tr><th>Person Name:</th><td>'. $data['person_name'] .'</td></tr>';
		$html .= '<tr><th>Company Phone:</th><td>'. $data['phone'] .'</td></tr>';
		$html .= '<tr><th>Person Designations:</th><td>'. $data['designation'] .'</td></tr>';
		$html .= '<tr><th>Company Fax:</th><td>'. $data['fax'] .'</td></tr>';
		$html .= '<tr><th>Mobile:</th><td>'. $data['mobile'] .'</td></tr>';
		$html .= '<tr><th>Company Email:</th><td>'. $data['company_email'] .'</td></tr>';
		$html .= '<tr><th>Company Website:</th><td>'. $data['url'] .'</td></tr>';
		$html .= '<tr><th>Company Address:</th><td>'. $data['company_address'] .'</td></tr>';
		$html .= '</table>';

		echo $html;
	}
}