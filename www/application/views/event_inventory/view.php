<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>
<style>
    .form-control[readonly] {
        border-color: transparent;
        background-color: transparent;
    }
</style>
<div class="content-wrapper" data-page="event_inventory">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Event
            <small>Inventory</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Items</li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Event Inventory</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('event-inventory-submit.html') ?>?id=<?= $this->input->get('id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">

                            <table class="table table-bordered">
                                <tr>
                                    <th class="text-center">Event Logo</th>
                                    <th>Event</th>
                                    <td><?= $this->formdata->exhibition_title ?></td>
                                </tr>
                                <tr>
                                    <td rowspan="3" class="text-center">
                                        <img src="<?= base_url($this->formdata->event_logo) ?>" alt="" class="img-thumbnail" style="width: 100px">
                                    </td>
                                    <th>Price Type</th>
                                    <td><?= $this->formdata->price_type ?></td>
                                </tr>
                                <tr>
                                    <th>Booking Expire Date</th>
                                    <td><?= $this->formdata->booking_expire_date ?></td>
                                </tr>
                                <tr>
                                    <th>Event Location</th>
                                    <td><?= $this->db->where('id', $this->formdata->location_id)->get('es_locations')->row()->location_title ?></td>
                                </tr>
                            </table>


                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th>Active</th>
                                    <th>Category</th>
                                    <th>Inventory</th>
                                    <th>Total Stock</th>
                                    <th>Remaining Stock</th>
                                    <th>Event Stock</th>
                                    <th>Event Used Stock</th>
                                    <th>Price USD</th>
                                    <th>Price PKR</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody id="iventory_items">
                                <?php
								// $expired_event_ids = array();
								// $expired_events = $this->db
								// 	->select('id')
								// 	->where('is_deleted' , 0)
								// 	->where('booking_expire_date <', date('Y-m-d'))
								// 	->get('es_exhibitions')
								// 	->result();
								// foreach ($expired_events as $key => $value) {
								// 	$expired_event_ids[] = $value->id;
								// }
								
                                $count = 0;
                                foreach ($items as $key => $item) {
                                    // $used_stock = $this->db
                                    //     ->select('SUM(item_stock) as used_stock')
                                    //     ->where('global_item_id', $item->id)
                                    //     ->where_not_in('exhibition_id', $expired_event_ids)
                                    //     ->where('is_active', 1)
                                    //     ->where('is_deleted', 0)
                                    //     ->get('es_inventory_item')
                                    //     ->row();

                                    // $remaining = $item->item_stock - $used_stock->used_stock;
                                    $remaining = $item->item_stock;

                                    $stock = $remaining;
                                    $item_price_usd = $item->item_price_usd;
                                    $item_price_pkr = $item->item_price_pkr;
									$checked = '';
									$display_edit_btn = 'none';
									$is_old_item = 0;
									$is_disabled = 'readonly';
									$event_used_stock = 0;

                                    $old_data = $this->db
                                        ->where('exhibition_id', $this->formdata->id)
                                        ->where('global_item_id', $item->id)
                                        ->get('es_inventory_item')
                                        ->row();

                                    if ($old_data) {
										$is_old_item = 1;
										$checked = '';
										$display_edit_btn = 'none';
										$is_disabled = 'readonly';

										$event_used_stock = $this->db
                                            ->select('SUM(quantity) as total_quantity')
                                            ->where('exhibition_id', $this->formdata->id)
                                            ->where('item_id', $old_data->id)
                                            ->group_by('item_id')
                                            ->get('es_exhibition_booking_items')
                                            ->row();

										$event_used_stock = ($event_used_stock) ? $event_used_stock->total_quantity : 0;

										if ($old_data->is_active == 1) {
											$checked = 'checked';
											$display_edit_btn = 'inline-block';
											$stock = $old_data->item_stock;
											$item_price_usd = $old_data->item_price_usd;
											$item_price_pkr = $old_data->item_price_pkr;
                                        }

                                    }

									if (!$old_data && $remaining == 0) continue;

									$count++;
                                    echo '<tr>
                                    <td>'. $count .'</td>
                                    <td><input type="checkbox" class="inventory_check" name="items['.$key.'][is_active]" '.$checked.'></td>
                                    <td>'. $item->category_title .'</td>
                                    <td>'. $item->item_title .'</td>
                                    <td>'. $item->item_stock .'</td>
                                    <td>'. $remaining .'</td>
                                    <td>
                                        <input type="hidden" name="items['.$key.'][is_old_item]" value="'. $is_old_item .'">
                                        <input type="hidden" name="items['.$key.'][global_item_id]" value="'. $item->id .'">
                                        <input type="number" class="form-control" name="items['.$key.'][stock]" placeholder="Event Stock" value="'. $stock .'" '.$is_disabled.'>
                                    </td>
                                    <td>'. $event_used_stock .'</td>
                                    <td>
                                        <input type="number" class="form-control" name="items['.$key.'][price_usd]" placeholder="Price USD" value="'. $item_price_usd .'" '.$is_disabled.'>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="items['.$key.'][price_pkr]" placeholder="Price PKR" value="'. $item_price_pkr .'" '.$is_disabled.'>
                                    </td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-link edit_stock_btn" style="display: '.$display_edit_btn.'">Edit</button>
                                    </td>
                                    </tr>';
                                }
                                ?>
                                </tbody>
                            </table>

                            <p class="bg-danger js-msgbox"></p>

                            <div class="row">
                                <div class="col-xs-10">

                                </div>
                                <div class="col-xs-2">
                                    <a href="javascript:void(0);"
                                       class="btn btn-primary btn-block margin-bottom js-form_btn">Save</a>
                                </div>
                            </div>

                        </form>

                    </div><!-- /.box-body -->
                </div><!-- /.box -->

                <!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->
    <!-- /.content -->
</div>


<?php $this->load->view('includes/after_login/footer'); ?>

<link rel="stylesheet" href="<?= base_url('assets') ?>/iCheck/square/grey.css">
<script src="<?= base_url('assets') ?>/iCheck/icheck.min.js"></script>

<script>

	$(function () {
		doFormValidation({
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("event-inventory-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		});

	});

	$('.inventory_check').iCheck({
		checkboxClass: 'icheckbox_square-grey',
		radioClass:    'iradio_square-grey',
		increaseArea:  '20%' // optional
	});


	$(document).on('ifChecked', '.inventory_check', function (e) {
		e.stopImmediatePropagation();

		$(this).parents('tr').find('.edit_stock_btn').show();
	});
	$(document).on('ifUnchecked', '.inventory_check', function (e) {
		e.stopImmediatePropagation();

		$(this).parents('tr').find('.edit_stock_btn').hide();
		$(this).parents('tr').find('.form-control').attr('readonly', true);
	});


	$(document).on('click', '.edit_stock_btn', function (e) {
		e.stopImmediatePropagation();

		$(this).parents('tr').find('.form-control').attr('readonly', false);
	});

</script>
</body>
</html>
