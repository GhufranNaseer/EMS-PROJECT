<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_profile extends MY_Controller {

	public function view_profile() {
		$booking = $this->db
			->select('B.*')
			->where(mycolumn('B.id'), $_GET['id'])
			->get('es_exhibition_booking as B')
			->row();

		$event = $this->db
			->select('E.*')
			->where(mycolumn('B.id'), $_GET['id'])
			->join('es_exhibitions as E', 'E.id = B.exhibition_id')
			->get('es_exhibition_booking as B')
			->row();
		
		$organizer = $this->db
			->where('id', $event->event_organizer)
			->get('es_organizer')
			->row();

		$rows = $this->db
			->select('F.*, C.company')
			->where(mycolumn('F.booking_id'), $_GET['id'])
			->where('F.form_id', 3)
			->join('es_exhibition_booking as B', 'F.booking_id = B.id', 'LEFT')
			->join('es_customers as C', 'B.customer_id = C.id', 'LEFT')
			->get('es_exhibition_booking_forms_data as F')
			->row();
	
		$data = (isset($rows->form_data)) ? json_decode($rows->form_data,false) : null;

		$booking_stalls = $this->db
			->where('booking_id', $booking->id)
			->get('es_exhibition_stalls')
			->result();
			
		$hall_data = $this->db
			->where('id', $booking_stalls[0]->hall_id)
			->get('es_location_halls')
			->row();

		// echo '<pre>'; print_r($data); die;
		
		$this->load->view('includes/after_login/head');
		$this->load->view('product-profile/view', array(
			'booking' => $booking,
			'event' => $event,
			'organizer' => $organizer,
			'rows' => $rows,
			'data' => $data,
			'booking_stalls' => $booking_stalls,
			'hall_data' => $hall_data,
		));
	}
}