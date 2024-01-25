<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>
<style>
    .radio label {
        padding-left: 0;
    }
</style>

<div class="content-wrapper" data-page="order_list">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Order
            <small>Details</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Details</li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <ul class="nav nav-tabs h4" role="tablist">
                            <li class="<?= (!$this->input->get('tab') || $this->input->get('tab') == 'order_detail') ? 'active' : '' ?>">
                                <a href="<?= base_url('order_detail.html?id='.$this->input->get('id').'&order_id='.$this->input->get('order_id').'&tab=order_detail') ?>">Order Details</a>
                            </li>
                            <li class="<?= ($this->input->get('tab') && $this->input->get('tab') == 'edit_order') ? 'active' : '' ?>">
                                <a href="<?= base_url('order_detail.html?id='.$this->input->get('id').'&order_id='.$this->input->get('order_id').'&tab=edit_order') ?>">Edit Order</a>
                            </li>
							<?php if ($this->userdata->user_group_id != SALES_PERSON) { ?>
                                <li class="<?= ($this->input->get('tab') && $this->input->get('tab') == 'order_logs') ? 'active' : '' ?>">
                                    <a href="<?= base_url('order_detail.html?id='.$this->input->get('id').'&order_id='.$this->input->get('order_id').'&tab=order_logs') ?>">Logs</a>
                                </li>
                                <li class="<?= ($this->input->get('tab') && $this->input->get('tab') == 'expired_forms') ? 'active' : '' ?>">
                                    <a href="<?= base_url('order_detail.html?id='.$this->input->get('id').'&order_id='.$this->input->get('order_id').'&tab=expired_forms') ?>">Expired forms</a>
                                </li>
							<?php } ?>
                        </ul>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <table class="table table-bordered table-striped">
                                    <tr class="h4">
                                        <th>Order #</th>
                                        <td colspan="3"><?= $this->orderdata->id ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
											<?php
											if ($this->orderdata->is_approved == 1) {
												echo '<span class="label label-success">APPROVED</span>';
											} else if ($this->orderdata->booking_type == 'tentative') {
												echo '<span class="label label-warning">'. strtoupper($this->orderdata->booking_type) .'</span>';
											} else if ($this->orderdata->booking_type == 'confirmed') {
												echo '<span class="label label-success">'. strtoupper($this->orderdata->booking_type) .'</span>';
											}
											?>
                                        </td>
                                        <th>Price Type</th>
                                        <td><?= strtoupper($this->orderdata->booking_price_type) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Booked on</th>
                                        <td><?= $this->orderdata->booking_date ?></td>
                                        <th>Booked By</th>
                                        <td>
                                            <?php
                                            $sales_person = $this->db->where('id', $this->orderdata->booked_by)->get('users')->row();
											echo $sales_person->user_first_name . ' ' . $sales_person->user_last_name
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-center">Customer Information</th>
                                    </tr>
                                    <tr>
                                        <th>Exhibitor Company</th>
                                        <td colspan="3">
                                            <a href="<?= base_url('customer-edit.html?id=' . myid($customer_data->id)) ?>" target="_blank">
                                                <?= $customer_data->company ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 25%">Executive Name</th>
                                        <td style="width: 25%"><?= $customer_data->name ?></td>
                                        <th style="width: 25%">Designation</th>
                                        <td style="width: 25%"><?= $customer_data->designation ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><?= $customer_data->email ?></td>
                                        <th>URL</th>
                                        <td><?= $customer_data->url ?></td>
                                    </tr>
                                    <tr>
                                        <th>Telephone</th>
                                        <td><?= $customer_data->phone ?></td>
                                        <th>Fax</th>
                                        <td><?= $customer_data->fax ?></td>
                                    </tr>
                                    <tr>
                                        <th>Country</th>
                                        <td><?= $customer_data->country ?></td>
                                        <th>City</th>
                                        <td><?= $customer_data->city ?></td>
                                    </tr>
                                    <tr>
                                        <th>Zip Code</th>
                                        <td><?= $customer_data->zip_code ?></td>
                                        <th>Address</th>
                                        <td><?= $customer_data->address ?></td>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-center">Contact Person Information</th>
                                    </tr>
                                    <?php
									$contact_person_data = $this->db
										->where('id', $this->orderdata->contact_person_id)
										->get('es_customer_contact_persons')
										->row();
                                    ?>
                                    <tr>
                                        <th>Person Name</th>
                                        <td><?= $contact_person_data->person_name ?></td>
                                        <th>Designation</th>
                                        <td><?= $contact_person_data->designation ?></td>
                                    </tr>
                                    <tr>
                                        <th>Person Email</th>
                                        <td><?= $contact_person_data->primary_email ?></td>
                                        <th>Person Cell</th>
                                        <td><?= $contact_person_data->primary_phone ?></td>
                                    </tr>

                                    <?php
                                    if ($this->orderdata->has_agent == 1) {
                                        $agent_data = $this->db
                                            ->where('id', $this->orderdata->agent_id)
                                            ->get('es_agent')
                                            ->row();
                                        ?>
                                    <tr>
                                        <th colspan="4" class="text-center">Agent Information</th>
                                    </tr>
                                    <tr>
                                        <th>Agent Company</th>
                                        <td>
                                            <a href="<?= base_url('agent-edit.html?id=' . myid($agent_data->id)) ?>" target="_blank">
                                                <?= $agent_data->agent_company ?>
                                            </a>
                                        </td>
                                        <th>Email</th>
                                        <td><?= $agent_data->agent_email ?></td>
                                    </tr>
                                    <tr>
                                        <th>Contact Person</th>
                                        <td><?= $agent_data->agent_name ?></td>
                                        <th>Designation</th>
                                        <td><?= $agent_data->agent_designation ?></td>
                                    </tr>
                                    <tr>
                                        <th>Country</th>
                                        <td><?= $agent_data->agent_country ?></td>
                                        <th>City</th>
                                        <td><?= $agent_data->agent_city ?></td>
                                    </tr>
                                    <tr>
                                        <th>Zip Code</th>
                                        <td><?= $agent_data->agent_zip_code ?></td>
                                        <th>Address</th>
                                        <td><?= $agent_data->agent_address ?></td>
                                    </tr>
                                    <tr>
                                        <th>Telephone</th>
                                        <td><?= $agent_data->agent_phone ?></td>
                                        <th>Fax</th>
                                        <td><?= $agent_data->agent_fax ?></td>
                                    </tr>
									<?php } ?>
                                </table>
                            </div>
                            <div class="col-sm-6">
                                <?php

                                $booking_items = $this->db
                                    ->where('booking_id', $this->orderdata->id)
                                    ->get('es_exhibition_booking_items')
                                    ->result();

                                $booking_packages = $this->db
                                    ->select('S.*, P.package_title')
									->where('booking_id', $this->orderdata->id)
                                    ->group_by('S.package_id')
                                    ->join('es_packages as P', 'P.id = S.package_id', 'LEFT')
                                    ->get('es_exhibition_booking_stalls as S')
                                    ->result();

                                $booking_package_items = $this->db
                                    ->select('I.*, SUM(I.quantity) as quantity, P.package_title, P.package_price_usd, P.package_price_pkr, IT.item_title')
                                    ->where('I.booking_id', $this->orderdata->id)
                                    ->where('I.is_package_item', 1)
                                    ->group_by('I.item_id')
                                    ->join('es_inventory_item as IT', 'IT.id = I.item_id', 'LEFT')
                                    ->join('es_packages as P', 'P.id = I.package_id', 'LEFT')
                                    ->get('es_exhibition_booking_items as I')
                                    ->result();

                                $booking_extra_items = $this->db
									->select('I.*, P.package_title, P.package_price_usd, P.package_price_pkr, IT.item_title')
									->where('I.booking_id', $this->orderdata->id)
									->where('I.is_package_item', 0)
									->join('es_inventory_item as IT', 'IT.id = I.item_id', 'LEFT')
									->join('es_packages as P', 'P.id = I.package_id', 'LEFT')
									->get('es_exhibition_booking_items as I')
									->result();

                                $stall_cost = 0;
                                foreach ($booking_stalls as $booking_stall) {
                                    $stall_cost += ($this->orderdata->booking_price_type == 'PKR') ? $booking_stall->stall_price_pkr : $booking_stall->stall_price_usd;
                                }
                                ?>

                                <table class="table table-bordered table-striped">
                                    <?php
                                    $stall_types = array();
                                    foreach ($booking_stalls as $stall_type) {
                                        $stall_types[$stall_type->booking_stall_type] = 1;
                                    }
                                    ?>
                                    <tr>
                                        <th>Stall Type</th>
                                        <td colspan="3"><?= implode(', ', array_map(function ($stall){ return $stall; }, array_keys($stall_types))) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Stalls</th>
                                        <td colspan="3">
                                            <?= implode(', ', array_map(function ($stall){ return $stall->stall_name; }, $booking_stalls)) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Package</th>
                                        <td colspan="3"><?= implode(', ', array_map(function ($p){ return $p->package_title; }, $booking_packages)) ?></td>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-center">Summary</th>
                                    </tr>
                                    <tr>
                                        <th>Stalls x <?= count($booking_stalls) ?></th>
                                        <td colspan="3" class="text-right"><?= $stall_cost ?></td>
                                    </tr>

                                    <tr>
                                        <th>Item Type</th>
                                        <th>Item Name</th>
                                        <th>Item Quantity</th>
                                        <th></th>
                                    </tr>
                                    <?php
                                    foreach ($booking_package_items as $booking_package_item) {
                                        echo '<tr>
                                        <td>Package Item</td>
                                        <td>'. $booking_package_item->item_title .'</td>
                                        <td>'. $booking_package_item->quantity .'</td>
                                        <td class="text-right">-</td>
                                        </tr>';
                                    }


                                    foreach ($booking_extra_items as $booking_extra_item) {
										$i_p = ($this->orderdata->booking_price_type == 'PKR') ? $booking_extra_item->item_price_pkr : $booking_extra_item->item_price_usd;
										$i_p = $i_p * $booking_extra_item->quantity; // multiply per item price to quantity
                                        $item_type = ($booking_extra_item->is_initial_item == 1) ? 'Extra Item' : 'Additional Purchase';
                                        echo '<tr>
                                        <td>'.$item_type.'</td>
                                        <td>'. $booking_extra_item->item_title .'</td>
                                        <td>'. $booking_extra_item->quantity .'</td>
                                        <td class="text-right">'. $i_p .'</td>
                                        </tr>';
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="3" class="text-right">Total</td>
                                        <td class="text-right"><?= $this->orderdata->booking_amount ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right">Discount</td>
                                        <td class="text-right">(<?= ($this->orderdata->offer_discount_amount + $this->orderdata->additional_discount_amount) ?>)</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right">Tax</td>
                                        <td class="text-right">+<?= $this->orderdata->booking_tax_amount ?></td>
                                    </tr>
                                    <tr class="h4">
                                        <th colspan="3" class="text-right">Grand Total</th>
                                        <th class="text-right"><?= $this->orderdata->booking_price_type ?> <?= $this->orderdata->booking_total ?></th>
                                    </tr>
                                </table>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <a href="<?= base_url('print_invoice.html?id=' . myid($this->orderdata->exhibition_id) . '&order_id=' . myid($this->orderdata->id)) ?>" class="btn btn-primary btn-lg" target="_blank">Print Invoice</a>
                                    </div>
                                    <div class="col-sm-6 text-right">
										<?php
										if ($this->orderdata->is_canceled == 0) {
											if ($this->orderdata->booking_type == 'tentative') {
												echo '<button type="button" class="btn btn-success btn-lg btn_mark_confirm">Mark Confirmed</button> ';
											}

											if ($this->orderdata->booking_type == 'confirmed' && $this->orderdata->is_approved == 0) {
												echo '<button type="button" class="btn btn-success btn-lg btn_mark_approved">Approve Order</button> ';
											}

											if ($this->orderdata->is_approved == 0 || $this->userdata->user_group_id != SALES_PERSON) {
												echo '<button type="button" class="btn btn-danger btn-lg btn_cancel_order">Cancel Order</button>';
											}
										}
										?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!-- /.box-body -->
                </div><!-- /.box -->

                <!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->
    <!-- /.content -->
</div>


<form action="<?= base_url('order_list/cancel_order') ?>?id=<?= $this->input->get('id') ?>&order_id=<?= $this->input->get('order_id') ?>" method="post"
      id="cancel_form" enctype="multipart/form-data">

    <input type="hidden" name="cancel_reason" class="cancel_reason">

</form>


<?php $this->load->view('includes/after_login/footer'); ?>

<link rel="stylesheet" href="<?= base_url('assets') ?>/iCheck/square/grey.css">
<script src="<?= base_url('assets') ?>/iCheck/icheck.min.js"></script>

<script>

	$(document).on('click', '.btn_mark_confirm', function (e) {
		e.stopImmediatePropagation();

		swal({
				title: "Are you sure?",
				text: "You want to confirm this order?",
				type: "warning",
				showCancelButton: true,
				confirmButtonClass: "btn-success",
				confirmButtonText: "Confirmed",
				closeOnConfirm: false,
				showLoaderOnConfirm: true
			},
			function(){
                window.location.href = "<?= base_url('order_list/confirm_order') ?>?id=<?= $this->input->get('id') ?>&order_id=<?= $this->input->get('order_id') ?>";

				setTimeout(function () {
					return true;
				}, 40000);
			});
	});


	$(document).on('click', '.btn_cancel_order', function (e) {
		e.stopImmediatePropagation();


		swal({
				title: "Are you sure?",
				text: "This action cannot be undo, The order will be removed and all stalls will be available again for booking!",
				type: "warning",
				showCancelButton: true,
				confirmButtonClass: "btn-danger",
				confirmButtonText: "Yes, Cancel it!",
				closeOnConfirm: false
			},
			function(){
			    // get cancel reason
				swal({
					title: "",
					text: "Please write the reason of cancel!",
					type: "input",
					showCancelButton: true,
					closeOnConfirm: false,
					inputPlaceholder: "Reason",
					showLoaderOnConfirm: true
				}, function (inputValue) {
					if (inputValue === false) return false;
					if (inputValue === "") {
						swal.showInputError("Reason field is required!");
						return false
					}
					$('.cancel_reason').val(inputValue);
					$('#cancel_form').submit();

					setTimeout(function () {
                        return true;
					}, 40000)
				});
			});
	});


    $(document).on('click', '.btn_mark_approved', function (e) {
		e.stopImmediatePropagation();
		swal({
				title: "Are you sure?",
				text: "You want to approve this order?",
				type: "warning",
				showCancelButton: true,
				confirmButtonClass: "btn-success",
				confirmButtonText: "Approve",
				closeOnConfirm: false,
				showLoaderOnConfirm: true
			},
			function(){
				window.location.href = "<?= base_url('order_list/approve_order') ?>?id=<?= $this->input->get('id') ?>&order_id=<?= $this->input->get('order_id') ?>";

				setTimeout(function () {
					return true;
				}, 40000);
			});
	});
</script>
</body>
</html>
