<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ecommerce extends MY_Controller {

	public $form_id = 26;

	function index() {

		$this->load->view('includes/after_login/head');
		$this->load->view('ecommerce/index', array());
	}

	// Product Category page

	function category_products() {

		$category_id =  $_GET['cat'];

		$get_products = $this->db
			->where(mycolumn('category_id'),$category_id)
			->where('exhibition_id',$this->event->id)
			->get('es_inventory_item')
			->result_array();

		$this->load->view('includes/after_login/head');
		$this->load->view('ecommerce/category-products',['get_products'=> $get_products]);

	}

	// Product details page

	function product_details() {

		$product_id =  $_GET['pid'];

		$product_details = $this->db
			->select('es_inventory_item.*, G.item_description, C.category_title')
			->where(mycolumn('es_inventory_item.id'),$product_id)
			->where('es_inventory_item.exhibition_id',$this->event->id)
			->join('es_inventory_category as C', 'es_inventory_item.category_id = C.id', 'LEFT')
			->join('es_inventory_item_global as G', 'G.id = es_inventory_item.global_item_id', 'LEFT')
			->get('es_inventory_item')
			->result_array();

		$this->load->view('includes/after_login/head');
		$this->load->view('ecommerce/product-detail',['product_details'=> $product_details]);

	}


	// Product Add to Cart

	function add_cart(){
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
			$this->session->set_flashdata('error', 'Additional items submission date is already expired!');
			redirect($_SERVER['HTTP_REFERER']);
			die;
		}


		$qty = ($this->input->post('qty') && $this->input->post('qty') != '') ? $this->input->post('qty') : 1;

		$cart_data = array(
			'id'      => $this->input->post('item_id'),
			'qty'     => $qty,
			'price'   => $this->input->post('item_price'),
			'name'    => preg_replace('/[^A-Za-z0-9\- ]/', '', $this->input->post('item_title')),
			'image'   => $this->input->post('item_image')
		);

		$this->cart->insert($cart_data);

		$this->session->set_flashdata('message', 'Product add successfully.');
		redirect($_SERVER['HTTP_REFERER']);

	}


	// Product Delete From Cart List

	function delete_cart(){

		$delete_cart = array(
			'rowid' => $this->input->post('rowid'),
			'qty'   => 0
		);

		$this->cart->update($delete_cart);

		$this->session->set_flashdata('message', 'Product Delete successfully.');
		echo 'done';
		die();
	}


	// View Cart List
	function view_cart(){

		$this->load->view('includes/after_login/head');
		$this->load->view('ecommerce/view-cart', array());

	}

	// Update Cart list
	function update_cart(){

		$update_cart = array(
			'rowid' => $this->input->post('rowid'),
			'qty'   => $this->input->post('qty_value')
		);

		$this->cart->update($update_cart);

		$this->session->set_flashdata('message', 'Product updated successfully.');

		echo 'done';
		die();
	}

	// checkout page

	function checkout(){

		$this->load->view('includes/after_login/head');
		$this->load->view('ecommerce/checkout', array());

	}

// Product Order 

	function order_place(){

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
			$this->session->set_flashdata('error', 'Additional items submission date is already expired!');
			redirect($_SERVER['HTTP_REFERER']);
			die;
		}

		$order_place = array(
			'exhibition_id' => $this->event->id,
			'booking_id'  =>   $this->booking->id,
			'customer_id' =>   $this->userdata->id,
			'total_amount' =>  $this->cart->total(),
			'total_items' =>   count($this->cart->contents())
		);

		$this->db
			->set($order_place)
			->insert('es_exhibition_order');

		$last_insert_id = $this->db->insert_id();

		foreach($this->cart->contents() as $items){

			$items_insert = array(
				'order_id' =>  $last_insert_id,
				'exhibition_id' => $this->event->id,
				'booking_id' =>  $this->booking->id,
				'customer_id' => $this->userdata->id,
				'item_id' => $items['id'],
				'item_quantity' => $items['qty'],
				'item_price' => $items['subtotal']
			);

			$this->db
				->insert('es_exhibition_order_items',$items_insert);
		}

		$this->cart->destroy();

		$this->session->set_flashdata('message', 'Your order has been placed');
		$this->session->set_flashdata('order_invoice', base_url('print/order/invoice?id=' . $last_insert_id));
		redirect(base_url('dashboard'));
	}

	function order_invoice() {
		$order_id = $this->input->get('id');

		$order_data = $this->db
			->where('id', $order_id)
			->get('es_exhibition_order')
			->row();

		$order_items = $this->db
			->select('O.*, I.item_title, I.item_price_usd, I.item_price_pkr')
			->where('O.order_id', $order_id)
			->join('es_inventory_item as I', 'I.id = O.item_id', 'LEFT')
			->get('es_exhibition_order_items as O')
			->result();

		$html = $this->load->view('print/order_invoice', array(
			'order_data' => $order_data,
			'order_items' => $order_items,
		), true);


		//echo $html; die();
		set_time_limit(0);
		$this->load->helper ("pdf-loader");
		try {
			$html2pdf = new HTML2PDF('P', 'A4', 'en');
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->writeHTML($html);
			$html2pdf->Output('print-invoice-'.$order_id.'.pdf');
		}
		catch(HTML2PDF_exception $e) {
			$msg = $e->getMessage ();
			//$msg = base64_encode($msg);
			//$msg = trim ($msg);
			print_r($msg);
		}
	}
}