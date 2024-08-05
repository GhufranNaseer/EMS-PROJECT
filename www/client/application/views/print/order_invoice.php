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

$bank_details = $this->db
->where('exhibition_id', $this->event->id)
->get('bank_details')
->row(); 

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
			</td>
			<td style="width: 85%;">
				<p style="font-size: 20px;"><?= $this->event->exhibition_title ?></p>
				<p style="font-size: 14px;"><?= $location->location_title ?></p>
				<p class="text-right text-muted"><?= date('d/m/Y') ?></p>
			</td>
		</tr>
	</table>

	<p style="font-size: 20px;" class="text-center">ORDER INVOICE</p>

	<table class="table-bordered" style="width: 100%; margin-top: 20px;">
		<tr>
			<td style="width: 15%">Company Name:</td>
			<td style="width: 20%"><?= $this->userdata->company ?></td>
			<td style="width: 15%">Company Address:</td>
			<td style="width: 30%"><?= $this->userdata->address ?></td>
			<td style="width: 10%">Country:</td>
			<td style="width: 10%"><?= $this->userdata->country ?></td>
		</tr>
		<tr>
			<td style="width: 15%">Email Address:</td>
			<td style="width: 20%"><?= $this->userdata->email ?></td>
			<td style="width: 15%">Telephone:</td>
			<td style="width: 30%"><?= $this->userdata->phone ?></td>
			<td style="width: 10%">Contact Person:</td>
			<td style="width: 10%"><?= $contact_person_data->person_name ?></td>
		</tr>
		<tr>
			<td style="width: 15%">Hall Number:</td>
			<td style="width: 20%"><?= implode(', ', array_map(function ($h){ return $h->hall_title; }, $halls)) ?></td>
			<td style="width: 15%">Stall Number:</td>
			<td style="width: 50%" colspan="3"><?= implode(', ', array_map(function ($s){ return $s->stall_name; }, $stalls)) ?></td>
		</tr>
	</table>

	<table class="table table-bordered table-striped" style="width: 100%; margin-top: 40px;">
		<thead>
		<tr class="dark-bg">
			<td style="width: 10%">#</td>
			<td style="width: 40%">Product</td>
			<td style="width: 20%">Quantity</td>
			<td style="width: 15%">Price</td>
			<td style="width: 15%">Sub Total</td>
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
        <td colspan="4" class="text-right">Grand Total</td>
        <td>'. number_format($order_data->total_amount) .' '.$this->booking->booking_price_type.'</td>
        </tr>';

		?>
		</tbody>
	</table>

	<table class="table table-striped" style="width: 100%; margin-top: 40px;">
		<tbody>
			<tr class="dark-bg">
				<td colspan="4"><h4>Mode of Payment:</h4></td>
			</tr>
			<tr>
				<td colspan="4">All payments shall be made in favor of "BADAR EXPO SOLUTIONS" through Payorder / Demand Draft / Cross Cheque / IBFT Only.</td>
			</tr>
			<tr>
				<td colspan="4"><h4>For online payment:</h4></td>
			</tr>
			<tr>
				<th><h5>Bank Name:</h5></th>
				<td><?= $bank_details->bank_name ?></td>
				<th><h5>Branch Name:</h5></th>
				<td><?= $bank_details->branch_name ?></td>
			</tr>
			<tr>
				<th><h5>Branch Code:</h5></th>
				<td><?= $bank_details->branch_code ?></td>
				<th><h5>Swift Code:</h5></th>
				<td><?= $bank_details->swift_code ?></td>
			</tr>
			<tr>
				<th><h5>Title of Account:</h5></th>
				<td><?= $bank_details->title ?></td>
				<td><h5>Account No.:</h5></td>
				<td><?= $bank_details->account_no ?></td>
			</tr>
			<tr>
				<th colspan="2"><h5>IBAN No:</h5></th>
				<td colspan="2"><?= $bank_details->iban_no ?></td>
			</tr>
			<tr>
				<td colspan="4">Minimum 50% advance payment of booking amount is mandatory for confirmed allocation of stall.</td>
			</tr>
			<tr>
				<td colspan="4">100% payment has to be cleared before 45 Days of the Event.</td>
			</tr>
			<tr>
				<td colspan="4">The booking of stall without payment will be considered as "TENTATIVE" and stall can be allocated to any other Exhibitor on the basis of first come first serve.</td>
			</tr>
			<tr>
				<td colspan="4">Any valid tax will be applied in addition to the net amount.</td>
			</tr>
			<tr>
				<td colspan="4"><h4>Rules & Regulations / Participation & Cancellation Policy:</h4></td>
			</tr>
			<tr>
				<td colspan="4">Please read all the terms and conditions and policies carefully at https://fleepfair.com/rules-regulations-of-participation/</td>
			</tr>
		</tbody>
	</table>		
			


</page>