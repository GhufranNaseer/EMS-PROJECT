<?php

$location = $this->db
	->where('id', $this->event->location_id)
	->get('es_locations')
	->row();

$contact_person_data = $this->db
	->where('id', $this->booking->contact_person_id)
	->get('es_customer_contact_persons')
	->row();

$halls = $this->db
	->select('BS.*, L.hall_title')
	->where('BS.booking_id', $this->booking->id)
	->group_by('BS.hall_id')
	->join('es_location_halls as L', 'L.id = BS.hall_id', 'LEFT')
	->get('es_exhibition_booking_stalls as BS')
	->result();

$stalls = $this->db
	->select('BS.*, S.stall_name')
	->where('BS.booking_id', $this->booking->id)
	->join('es_exhibition_stalls as S', 'S.id = BS.stall_id', 'LEFT')
	->get('es_exhibition_booking_stalls as BS')
	->result();

?>
<page>
	<style>
		* {
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
			margin: 0;
			padding: 0;
		}
		page {
			font-family: Helvetica, Arial, sans-serif;
			font-size: 12px;
		}

		.text-right {
			text-align: right;
		}
		.text-left {
			text-align: left;
		}
		.text-center {
			text-align: center;
		}
		.text-muted {
			color: #777;
		}
        .text-justify {
            text-align: justify;
        }

		table {
			border-spacing: 0;
			border-collapse: collapse;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
		}
		table th,
		table td {
			padding: 5px;
			word-break: break-all;
			white-space: normal;
			vertical-align: top;
		}


		table.table-bordered th,
		table.table-bordered td {
			border: 1px solid #ccc;
		}

		.light-bg {
			background: #f9f9f9;
		}
		.dark-bg {
			background: #eee;
		}

		th.bottom-border,
		td.bottom-border {
			border-bottom: 1px solid #ccc;
		}

		.event_logo {
			width: 100px;
		}

		.company_stamp_area {
			width: 100%;
			height: 200px;
			border: 1px solid #ccc;
			margin-bottom: 5px;
		}
	</style>

	<table style="width: 100%">
		<tr>
			<td style="width: 15%;">
				<img src="<?= base_url('../' . $this->event->event_logo) ?>" alt="" class="event_logo">
			</td>
			<td style="width: 85%;">
				<h2><?= $this->event->exhibition_title ?></h2>
				<h4><?= $location->location_title ?></h4>
				<p class="text-right text-muted"><?= date('d/m/Y') ?></p>
			</td>
		</tr>
	</table>

	<h4 class="text-center">ORDER INVOICE</h4>

	<table class="table-bordered" style="width: 100%; margin-top: 20px;">
		<tr>
			<th style="width: 15%">Company Name:</th>
			<td style="width: 20%"><?= $this->userdata->company ?></td>
			<th style="width: 15%">Company Address:</th>
			<td style="width: 30%"><?= $this->userdata->address ?></td>
			<th style="width: 10%">Country:</th>
			<td style="width: 10%"><?= $this->userdata->country ?></td>
		</tr>
		<tr>
			<th style="width: 15%">Email Address:</th>
			<td style="width: 20%"><?= $this->userdata->email ?></td>
			<th style="width: 15%">Telephone:</th>
			<td style="width: 30%"><?= $this->userdata->phone ?></td>
			<th style="width: 10%">Contact Person:</th>
			<td style="width: 10%"><?= $contact_person_data->person_name ?></td>
		</tr>
		<tr>
			<th style="width: 15%">Hall Number:</th>
			<td style="width: 20%"><?= implode(', ', array_map(function ($h){ return $h->hall_title; }, $halls)) ?></td>
			<th style="width: 15%">Stall Number:</th>
			<td style="width: 50%" colspan="3"><?= implode(', ', array_map(function ($s){ return $s->stall_name; }, $stalls)) ?></td>
		</tr>
	</table>

	<table class="table table-bordered table-striped" style="width: 100%; margin-top: 40px;">
		<thead>
		<tr class="dark-bg">
			<th style="width: 10%">#</th>
			<th style="width: 40%">Product</th>
			<th style="width: 20%">Quantity</th>
			<th style="width: 15%">Price</th>
			<th style="width: 15%">Sub Total</th>
		</tr>
		</thead>
		<tbody>
		<?php

        foreach ($order_items as $count => $order_item) {
            echo '<tr>
            <td style="width: 10%">'.($count+1).'</td>
			<td style="width: 40%">'.$order_item->item_title.'</td>
			<td style="width: 20%">'.$order_item->item_quantity.'</td>
			<td style="width: 15%">'. (($this->booking->booking_price_type == 'PKR') ? number_format($order_item->item_price_pkr) : number_format($order_item->item_price_usd)) .' '.$this->booking->booking_price_type.'</td>
			<td style="width: 15%">'. number_format($order_item->item_price) .' '.$this->booking->booking_price_type.'</td>
			</tr>';
        }

        echo '<tr>
        <th colspan="4" class="text-right">Grand Total</th>
        <th>'. number_format($order_data->total_amount) .' '.$this->booking->booking_price_type.'</th>
        </tr>';

		?>
		</tbody>
	</table>


</page>