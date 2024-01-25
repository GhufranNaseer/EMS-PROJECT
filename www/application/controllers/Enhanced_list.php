<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Enhanced_list extends MY_Controller
{
	protected function rule()
	{
		$this->activateRightsSystem();
		$crd = array(
			'crd_list,
			enhanced_order_list,
			order_detail,
			print_order_invoice,
			confirm_order,
			cancel_order,
			approve_order,
			edit_order_submit,
			order_extended_form_submit,
			send_order_invitation_view,
			send_order_invitation_submit,
			enhanced_order_list_view,
			enhanced_order_approve,
			enhanced_order_cancel,
			' => array(
				'rule' => '@'
			),
			'crd_list_datatable,
			enhanced_order_list_datatable,
			send_order_invitation_validate,
			' => array(
				'rule' => '@',
				'ajaxOnly' => true
			)
		);
		$this->load->model('usermdl');
		$this->myparent = base_url('stalls.html');
		return array_merge($crd);
	}

	protected function checkEditId()
	{
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



	protected function checkOrderId()
	{
		$id = $this->input->get('id');
		if (is_null($id))
			show_404();
		$id = $this->db->escape($id);
		$key = config_item('encryption_key');
		$key = $this->db->escape($key);
		$r = $this->db
			->where("MD5(CONCAT($key,`id`)) = $id")
			->get('es_exhibition_order');
		if ($r->num_rows() == 0)
			show_404();
		$this->formdata = $r->row();
	}


	protected function getOrderData()
	{
		$id = $this->input->get('order_id');
		if (is_null($id))
			show_404();
		$id = $this->db->escape($id);
		$key = config_item('encryption_key');
		$key = $this->db->escape($key);
		if ($this->userdata->user_group_id == SALES_PERSON) {
			$this->db->where('booked_by', $this->userdata->id);
		}
		$r = $this->db
			->where("MD5(CONCAT($key,`id`)) = $id")
			->get('es_exhibition_booking');
		if ($r->num_rows() == 0)
			show_404();
		$this->orderdata = $r->row();
	}


	function crd_list()
	{
		$this->load->view('includes/after_login/head');
		$this->load->view('enhanced_list/list');
	}

	/* Exhibition List Table */

	function crd_list_datatable()
	{
		$this->load->library('datatables');
		$this->datatables
			->select('E.id,
				  E.exhibition_title,
				  L.location_title				  
				  ', false)
			->add_column('total_orders', function ($row) {
				if ($this->userdata->user_group_id == SALES_PERSON) {
					$this->db->where('customer_id', $this->userdata->id);
				}
				return $this->db
					->where('exhibition_id', $row['id'])
					->count_all_results('es_exhibition_order');
			}, NULL)
			->add_column('col_action', function ($row) {
				$id = $row['id'];
				$html = '<a href="' . base_url() . 'enhanced-order-list.html?id=' . urlencode(myid($id)) . '">View Orders</a>';

				return "<div class='text-center'>{$html}</div>";
			}, NULL)
			->where('E.is_deleted', 0)
			->join('es_locations as L', 'E.location_id = L.id', 'LEFT')
			->from('es_exhibitions as E');

		print ($this->datatables->generate());
	}


	function enhanced_order_list() {

		$this->checkEditId();

		$this->load->view('includes/after_login/head');
		$this->load->view('enhanced_list/enhanced_order_list');
	}


	/* Order List table From Particular Exhibition */

	function enhanced_order_list_datatable() {

		$this->checkEditId();

		$this->load->library('datatables');

		$this->datatables
			->select('es_exhibition_order.id,
				  es_customers.company,
				  es_exhibition_order.total_items,
				  es_exhibition_order.total_amount,
				  es_exhibition_order.is_canceled,
				  es_exhibition_order.is_approved,
				  es_exhibition_order.created_on
				  ', false)

			->unset_column('es_exhibition_order.is_canceled')
			->unset_column('es_exhibition_order.is_approved')
			->add_column('status', function ($row)
			{
				$approved = $row['is_approved'];
				$cancel = $row['is_canceled'];

				if($approved==1){
					return '<center style="font-size:11px; background-color:#72b508; padding:3px; border-radius:5px; color:#fff;">Approved</center>';
				}else{
					return ($cancel==1)?'<center style="font-size:11px; background-color:#cc0000; padding:3px; border-radius:5px; color:#fff;">Canceled</center>':'<center style="font-size:11px; background-color:#dcc415; border-radius:5px; padding:3px; color:#fff;">Pending</center>';
				}
			},NULL)


			->add_column('col_action', function ($row) {

				$id = $row['id'];

				$html = '<a href="' . base_url() . 'enhanced-order-list-view.html?id=' . urlencode(myid($id)) . '">View Details</a>';

				return "<div class='text-center'>{$html}</div>";
			}, NULL)

			->where('exhibition_id', $this->formdata->id)
			->join('es_customers', 'es_exhibition_order.customer_id = es_customers.id')
			->from('es_exhibition_order');

		if ($this->input->get('filter_status') && $this->input->get('filter_status') != '') {
			if ($this->input->get('filter_status') == 'approved') {
				$this->datatables->where('es_exhibition_order.is_approved', 1);
			} else if ($this->input->get('filter_status') == 'canceled') {
				$this->datatables->where('es_exhibition_order.is_canceled', 1);
			} else if ($this->input->get('filter_status') == 'pending') {
				$this->datatables->where('es_exhibition_order.is_approved', 0);
				$this->datatables->where('es_exhibition_order.is_canceled', 0);
			}
		}

		print ($this->datatables->generate());
	}


	/* Order View Details */

	function enhanced_order_list_view (){

		$this->checkOrderId();

		$booking = $this->db
			->where('id', $this->formdata->booking_id)
			->get('es_exhibition_booking')
			->row();

		$order_views = $this->db
			->select('
			es_exhibition_order_items.*,
			es_customers.name,
			es_customers.email,
			es_customers.phone,
			es_customers.company,
			es_customers.city,
			es_customers.address,
			es_customers.url,
			es_inventory_item.item_title,
			es_inventory_item.item_image,
			es_inventory_item.item_price_usd,
			es_inventory_item.item_price_pkr,
			es_inventory_item.global_item_id,
			es_exhibition_order.total_items,
			es_exhibition_order.total_amount,
			')
			->where('order_id',$this->formdata->id)
			->join('es_exhibition_order', 'es_exhibition_order_items.order_id = es_exhibition_order.id')
			->join('es_customers', 'es_exhibition_order_items.customer_id = es_customers.id')
			->join('es_inventory_item', 'es_exhibition_order_items.item_id = es_inventory_item.id')
			->get('es_exhibition_order_items')
			->result_array();

		$this->load->view('includes/after_login/head');
		$this->load->view('enhanced_list/order-list-view',['order_views'=> $order_views, 'booking' => $booking]);

	}


	/* Order Approved */

	function enhanced_order_approve(){

		$approve_orders = json_decode(htmlspecialchars_decode($this->input->post('order_details')),true);

		foreach($approve_orders as $approve_order){

			if ($approve_order['global_item_id'] == BADGE_GLOBAL_INVENTORY_ITEM) {
				// add badge quantity
				$booking_data = $this->db
					->where('id', $approve_order['booking_id'])
					->get('es_exhibition_booking')
					->row();

				$this->db
					->where('id', $approve_order['booking_id'])
					->update('es_exhibition_booking', array(
						'badges_total_limit' => $booking_data->badges_total_limit + $approve_order['item_quantity']
					));
			} else {
				$insert_approve_order = array(
					'exhibition_id' => $approve_order['exhibition_id'],
					'booking_id' =>  $approve_order['booking_id'],
					'item_id' =>  $approve_order['item_id'],
					'quantity' =>  $approve_order['item_quantity'],
					'item_price_usd' =>  $approve_order['item_price_usd'],
					'item_price_pkr' =>  $approve_order['item_price_pkr'],
					'added_by' => $this->userdata->id,
					'created_on' => date('Y-m-d H:i:s')
				);

				$this->db
					->insert('es_exhibition_booking_items',$insert_approve_order);
			}
		}


		/* Update status in order table */

		$order_status =	array(
			'is_approved'=> 1,
			'is_canceled'=> 0,
			'canceled_by'=> NULL,
			'approved_by'=> $this->userdata->id
		);

		$this->db
			->where('id',$approve_order['order_id'])
			->set($order_status)
			->update('es_exhibition_order');

		$this->db
			->where('order_id', $approve_order['order_id'])
			->update('es_exhibition_order_items', array(
				'is_approved'=> 1,
				'approved_by'=> $this->userdata->id
			));

		$this->session->set_flashdata('message', 'Order has been Approved Successfully');
		redirect(''.base_url().'enhanced-order-list.html?id=' . urlencode(myid($approve_order['exhibition_id'])) . '');
	}


	/* Enhanced Order Cancel */

	function enhanced_order_cancel(){

		$order_no = $this->input->post('order_no');
		$exhibition_id = $this->input->post('exhibition_id');

		$cancel_order =	array(
			'is_canceled'=> 1,
			'is_approved'=> 0,
			'approved_by'=> NULL,
			'canceled_by'=> $this->userdata->id
		);

		$this->db
			->where(mycolumn('id'),$order_no)
			->set($cancel_order)
			->update('es_exhibition_order');

		$this->session->set_flashdata('message', 'Order has been Canceled Successfully');
		redirect(''.base_url().'enhanced-order-list.html?id=' . $exhibition_id . '');

	}



}