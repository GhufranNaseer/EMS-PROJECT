<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<style>
    #add_badges_list .disable_package_item td:first-child {
        text-decoration: line-through;
    }
</style>

<div class="content-wrapper" data-page="packages">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Packages
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">List</li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Packages Add</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('packages-submit.html') ?>?id=<?= $this->input->get('id') ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">

                            <div class="table-responsive">
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
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Package Title</label>
                                        <input type="text" class="form-control" name="package_title"
                                               placeholder="Package Title">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Package Type</label>
                                        <select class="form-control package_type" name="package_type">
                                            <option value="">- select -</option>
                                            <option value="bare">Bare</option>
                                            <option value="shell">Shell</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Package Badges</label>
                                        <input type="number" class="form-control" name="package_badges"
                                               placeholder="Package Badges">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Visitor Badges</label>
                                        <input type="number" class="form-control" name="visitor_badges"
                                               placeholder="Visitor Badges">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row package_cost_row" style="display: none">
                                <div class="col-sm-6">
                                    <label>Package Shell Cost - USD</label>
                                    <input type="number" class="form-control" name="package_price_usd"
                                           placeholder="Package Shell Cost - USD">
                                </div>
                                <div class="col-sm-6">
                                    <label>Package Shell Cost - PKR</label>
                                    <input type="number" class="form-control" name="package_price_pkr"
                                           placeholder="Package Shell Cost - PKR">
                                </div>
                            </div>

                            <h4>Package Items - <button type="button" class="btn btn-sm btn-primary add_inventory_btn">Add item</button></h4>
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Item Name</th>
                                    <th>Initial Allowed</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody id="package_inventories">

                                </tbody>
                            </table>


                            <h4 style="margin-top: 60px">Package Badges Invitations</h4>
                            <div class="row">
                                <div class="col-sm-6">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Badge Invitation</th>
                                            <th style="min-width: 80px">Qty</th>
                                        </tr>
                                        </thead>
                                        <tbody id="add_badges_list">
										<?php
										$invitations = array('inauguration', 'seminar', 'sideline_conference', 'governor_reception', 'gala_dinner', 'closing_ceremony', 'karachi_air_show', 'cm_reception');

										foreach ($invitations as $invitation) {
											echo '<tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="has_badge_item" name="badges['.$invitation.'][has_item]" checked>
                                            </td>
                                            <td>'. ucfirst(str_replace('_', ' ', $invitation)) .'</td>
                                            <td>
                                                <input type="hidden" name="badges['.$invitation.'][badge_type]" value="exhibitor">
                                                <input type="number" class="form-control input-sm badge_qty" name="badges['.$invitation.'][quantity]" value="10">
                                            </td>
                                            </tr>';
										}

										?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>


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

            </div>
        </div><!-- /.row -->
    </section><!-- /.content -->
    <!-- /.content -->
</div><!-- /.content-wrapper -->



<?php $this->load->view('includes/after_login/footer'); ?>

<script>
	$(function () {
		doFormValidation({
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("packages-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		});
	});

	$(document).on('change', '.package_type', function (e) {
		e.stopImmediatePropagation();

		$('.package_cost_row').hide();
		if ($(this).val() == 'shell') {
			$('.package_cost_row').show();
        }
	});

	function get_categories(callback) {
		$.ajax({
			type:    'post',
			url:     '<?= base_url("exhibitions-inventory-category.html"); ?>',
			data:    {},
			success: function (data) {
				if (data && data != 'null') {
					callback(data);
				}
			},
			error:   function () {
			}
		});
	}

	function get_category_item(category_id, callback) {
		$.ajax({
			type:    'post',
			url:     '<?= base_url("inventory/packages/ajax_get_inventory_items"); ?>',
			data:    {category: category_id, exhibition_id: <?= $this->formdata->id ?>},
			success: function (data) {
				data = JSON.parse(data);

				if (data.error == 1) {
					alert(data.message);
					return;
                }

				if (callback) callback(data.data);
			},
			error:   function () {
			}
		});
	}

	$(document).on('click', '.add_inventory_btn', function (e) {
		e.stopImmediatePropagation();

		var uid = guid();

		get_categories(function (category_data) {
			var html = '<tr>\
            <td><select name="inventory['+uid+'][category]" class="form-control inventory_category">'+category_data+'</select></td>\
            <td><select name="inventory['+uid+'][item]" class="form-control inventory_item"></select></td>\
            <td><input type="number" name="inventory['+uid+'][initial_allow]" class="form-control"></td>\
            <td class="text-right">\
            <button type="button" class="btn btn-danger remove_inventory_item"><i class="fa fa-trash"></i></button>\
            </td>\
            </tr>';
			$('#package_inventories').append(html);
		});

	});

	$(document).on('change', '.inventory_category', function (e) {
		e.stopImmediatePropagation();

		var id = $(this).val();
		var $inventory_field = $(this).parents('tr').find('.inventory_item');
		$inventory_field.html('');
		get_category_item(id, function (items) {
            for (var i=0; i<items.length; i++) {
				$inventory_field.append('<option value="'+items[i].id+'">'+items[i].item_title+'</option>');
            }
		})
	});

	$(document).on('click', '.remove_inventory_item', function (e) {
		e.stopImmediatePropagation();

		$(this).parents('tr').remove();
	});

	/* START Badges */
	$(document).on('change', '.has_badge_item', function (e) {
		e.stopImmediatePropagation();

		if ($(this).is(':checked')) {
			$(this).parents('tr').removeClass('disable_package_item');
			$(this).parents('tr').find('.badge_qty').attr('readonly', false);
		} else {
			$(this).parents('tr').addClass('disable_package_item');
			$(this).parents('tr').find('.badge_qty').attr('readonly', true);
		}
	});
	/* END Badges */
</script>
</body>
</html>
