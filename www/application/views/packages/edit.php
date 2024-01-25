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
            <li class="active">Packages</li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Packages Edit</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('packages-edit-submit.html') ?>?id=<?= $this->input->get('id') ?>&package_id=<?= $this->input->get('package_id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">

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


                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <label>
                                        <input name="active"
                                               class="icheck"
                                               id="active" <?= ($this->packagedata->is_active == 1) ? 'checked' : '' ?>
                                               type="checkbox" value="">
                                        Active
                                    </label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Package Title</label>
                                        <input type="text" class="form-control" name="package_title"
                                               value="<?= html_escape($this->packagedata->package_title) ?>"
                                               placeholder="Package Title">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Package Type</label>
                                        <select class="form-control package_type" name="package_type">
                                            <option value="">- select -</option>
                                            <option value="bare" <?= ($this->packagedata->package_type == 'bare') ? 'selected' : '' ?>>Bare</option>
                                            <option value="shell" <?= ($this->packagedata->package_type == 'shell') ? 'selected' : '' ?>>Shell</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Package Badges</label>
                                        <input type="number" class="form-control" name="package_badges"
                                               value="<?= html_escape($this->packagedata->package_badges) ?>"
                                               placeholder="Package Badges">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row package_cost_row" style="display: <?= ($this->packagedata->package_type == 'shell') ? 'block' : 'none' ?>">
                                <div class="col-sm-6">
                                    <label>Package Shell Cost - USD</label>
                                    <input type="number" class="form-control" name="package_price_usd"
                                           value="<?= html_escape($this->packagedata->package_price_usd) ?>"
                                           placeholder="Package Shell Cost - USD">
                                </div>
                                <div class="col-sm-6">
                                    <label>Package Shell Cost - PKR</label>
                                    <input type="number" class="form-control" name="package_price_pkr"
                                           value="<?= html_escape($this->packagedata->package_price_pkr) ?>"
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
                                <?php
                                $package_inventories = $this->db
                                    ->select('P.*, I.category_id, I.item_title')
                                    ->where('P.package_id', $this->packagedata->id)
                                    ->join('es_inventory_item as I', 'I.id = P.item_id', 'LEFT')
                                    ->get('es_package_items as P')
                                    ->result();

								$categories = $this->db
									->where('is_deleted', 0)
									->get('es_inventory_category')
									->result();


                                foreach ($package_inventories as $inventory) {
                                    $uid = time() . '_' . rand();

									$categories_html = '';
									if (isset($categories) && count($categories) > 0) {
										$categories_html = '<option value="">- select -</option>';
										foreach ($categories as $category) {
										    $selected = ($category->id == $inventory->category_id) ? 'selected' : '';
											$categories_html .= '<option value="'.$category->id.'" '.$selected.'>'.$category->category_title.'</option>';
										}
									}

									$items = $this->db
										->where('exhibition_id', $this->formdata->id)
										->where('category_id', $inventory->category_id)
										->where('is_active', 1)
										->where('is_deleted', 0)
										->get('es_inventory_item')
										->result();
									$inventory_html = '';
									if (isset($items) && count($items) > 0) {
										foreach ($items as $item) {
											$selected = ($item->id == $inventory->item_id) ? 'selected' : '';
											$inventory_html .= '<option value="'.$item->id.'" '.$selected.'>'.$item->item_title.'</option>';
										}
									}

                                    echo '<tr>
                                    <td><select name="inventory['. $uid .'][category]" class="form-control inventory_category">'. $categories_html .'</select></td>
                                    <td><select name="inventory['. $uid .'][item]" class="form-control inventory_item">'. $inventory_html .'</select></td>
                                    <td><input type="number" name="inventory['. $uid .'][initial_allow]" class="form-control" value="'. $inventory->initial_allow .'"></td>
                                    <td class="text-right">
                                    <button type="button" class="btn btn-danger remove_inventory_item"><i class="fa fa-trash"></i></button>
                                    </td>
                                    </tr>';
                                }
                                ?>
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

										$old_badges = $this->db
											->where('package_id', $this->packagedata->id)
											->get('es_package_badges')
                                            ->result();

										$invitations = array('inauguration', 'seminar', 'sideline_conference', 'governor_reception', 'gala_dinner', 'closing_ceremony', 'karachi_air_show', 'cm_reception');
                                        $print_invitations = array();
										foreach ($old_badges as $badge) {
										    $is_active = ($badge->is_active == 1) ? 'checked' : '';
											echo '<tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="has_badge_item" name="badges['.$badge->invitation_type.'][has_item]" '.$is_active.'>
                                            </td>
                                            <td>'. ucfirst(str_replace('_', ' ', $badge->invitation_type)) .'</td>
                                            <td>
                                                <input type="hidden" name="badges['.$badge->invitation_type.'][badge_type]" value="exhibitor">
                                                <input type="number" class="form-control input-sm badge_qty" name="badges['.$badge->invitation_type.'][quantity]" value="'.$badge->quantity.'">
                                            </td>
                                            </tr>';
											$print_invitations[] = $badge->invitation_type;
										}

										foreach ($invitations as $invitation) {
										    if (!in_array($invitation, $print_invitations)) {
												echo '<tr>
                                                <td class="text-center">
                                                    <input type="checkbox" class="has_badge_item" name="badges['.$invitation.'][has_item]">
                                                </td>
                                                <td>'. ucfirst(str_replace('_', ' ', $invitation)) .'</td>
                                                <td>
                                                    <input type="hidden" name="badges['.$invitation.'][badge_type]" value="exhibitor">
                                                    <input type="number" class="form-control input-sm badge_qty" name="badges['.$invitation.'][quantity]" value="0">
                                                </td>
                                                </tr>';
                                            }
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

                <!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->
    <!-- /.content -->
</div>



<?php $this->load->view('includes/after_login/footer'); ?>

<script>
	$(function () {
		doFormValidation({
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("packages-edit-validate.html"); ?>?id=<?= $this->input->get('id') ?>&package_id=<?= $this->input->get('package_id') ?>",
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
