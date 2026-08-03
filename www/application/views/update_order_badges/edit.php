<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<style>
    #add_badges_list .disable_package_item td:first-child {
        text-decoration: line-through;
    }
</style>

<div class="content-wrapper" data-page="update_order_badges">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Update Order Badges
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Update Order Badges </li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Update Order Badges </h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('update-order-badges-edit-submit.html') ?>?id=<?= $this->input->get('id') ?>&order_id=<?= $this->input->get('order_id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Badges total limit</label>
                                        <input type="text" class="form-control" name="badges_total_limit"
                                               value="<?= $this->order_data->badges_total_limit ?>"
                                               placeholder="Badges total limit">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Visitor badges limit</label>
                                        <input type="text" class="form-control" name="visitor_badges_limit"
                                               value="<?= $this->order_data->visitor_badges_limit ?>"
                                               placeholder="Visitor badges limit">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Form 19 Edit Permission</label>
                                        <div class="checkbox">
                                            <label style="font-weight: bold; color: #3c8dbc;">
                                                <input type="checkbox" name="allow_invitation_edit" value="1" <?= (isset($this->order_data->allow_invitation_edit) && $this->order_data->allow_invitation_edit == 1) ? 'checked' : '' ?>>
                                                Enable Edit Button in Form 19
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <h4 style="margin-top: 60px">Badges Invitations</h4>
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
											->where('booking_id', $this->order_data->id)
											->get('es_exhibition_badges_limit')
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
			'urlValidator': "<?php echo base_url("update-order-badges-edit-validate.html"); ?>?id=<?= $this->input->get('id') ?>&order_id=<?= $this->input->get('order_id') ?>",
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
