<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="users">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            User
            <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('users.html'); ?>"><i class="fa fa-dashboard"></i>User List</a></li>
            <li class="active">Edit</li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">User Edit</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('user-edit-submit.html') ?>?id=<?= $this->input->get('id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="active">
                                            <input name="active"
                                                   id="active" <?= ($this->formdata->is_active == 1) ? 'checked' : '' ?>
                                                   type="checkbox" value="">
                                            Active
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        &nbsp;
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="user_first_name">First Name</label>
                                        <input type="text" class="form-control" name="user_first_name"
                                               id="user_first_name"
                                               value="<?= html_escape(ucwords($this->formdata->user_first_name)) ?>"
                                               placeholder="First Name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="user_last_name">Last Name</label>
                                        <input type="text" class="form-control" name="user_last_name"
                                               id="user_last_name"
                                               value="<?= html_escape(ucwords($this->formdata->user_last_name)) ?>"
                                               placeholder="Last Name">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="user_email">Email</label>
                                        <input type="text" class="form-control" name="user_email"
                                               value="<?= html_escape($this->formdata->user_email) ?>" id="user_email"
                                               placeholder="Email">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="user_phone">Phone</label>
                                        <input type="text" class="form-control" name="user_phone"
                                               value="<?= html_escape(ucwords($this->formdata->user_phone)) ?>"
                                               id="user_phone" placeholder="Phone">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="user_group_id">User Group</label>
                                        <select class="form-control" name="user_group_id" id="user_group_id">
                                            <option value=""> - Select User Group -</option>
											<?php foreach ($this->db->where('id !=', CUSTOMER)->get('usergroup')->result_array() as $row): ?>
												<?php $selected = ($row['id'] == $this->formdata->user_group_id) ? 'selected' : ''; ?>
                                                <option
                                                    value="<?= ($row['id']) ?>" <?= $selected ?>><?= html_escape(ucwords($row['usergroup_title'])) ?></option>
											<?php endforeach; ?>
                                        </select>

                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <?php if ($this->formdata->user_group_id == SALES_PERSON) { ?>
                                    <label>&nbsp;</label>
                                    <div class="checkbox">
                                        <label for="can_edit_orders">
                                            <input name="can_edit_orders"
                                                   id="can_edit_orders" <?= ($this->formdata->can_edit_orders == 1) ? 'checked' : '' ?>
                                                   type="checkbox" value="">
                                            Can Edit Orders
                                        </label>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="passwordchange">
                                            <input name="passwordchange" id="passwordchange" type="checkbox" value="1">
                                            Change user password
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    &nbsp;
                                </div>
                            </div>

                            <div class="row" id="passwordbody">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="user_password">Password</label>
                                        <input type="password" class="form-control" name="user_password"
                                               id="user_password" placeholder="Password">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="cuser_password">Confirm Password</label>
                                        <input type="password" class="form-control" name="cuser_password"
                                               id="cuser_password" placeholder="Confirm Password">
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="has_extra_rights">
                                            <input name="has_extra_rights" id="has_extra_rights" type="checkbox" value="1" <?= (!is_null($this->formdata->user_rights)) ? 'checked' : '' ?>>
                                            Has Extra Rights
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    &nbsp;
                                </div>
                            </div>
                            <div class="row" id="has_extra_rights_body" style="display: <?= (!is_null($this->formdata->user_rights)) ? 'block' : 'none' ?>" >
                                <div class="col-sm-12">

                                    <h2>User Extra Rights</h2>

									<?php $index = 0;
									foreach ($this->usermdl->getAllRights() as $area):
										$index++;
										?>
                                        <div class="form-group">
                                            <label for="rights_<?= $index ?>">
                                                <input name="rights[]"
                                                       id="rights_<?= $index ?>" <?= (strpos($this->formdata->user_rights, ",{$area['filename']},") !== false) ? 'checked' : ''; ?>
                                                       type="checkbox" value="<?= html_escape($area['filename']) ?>">
												<?= $area['cbname'] ?>
                                            </label>
                                        </div>
									<?php endforeach; ?>
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
		$('#passwordchange').click(function (e) {
			if (!$(this).is(':checked')) {
				$('#passwordbody').slideUp(1000);
			}
			else {
				$('#passwordbody').slideDown(1000);
			}
		});
		$('#passwordbody').hide();

		$('#has_extra_rights').click(function (e) {
			if (!$(this).is(':checked')) {
				$('#has_extra_rights_body').hide();
			}
			else {
				$('#has_extra_rights_body').show();
			}
		});

		$("#user_group_id").change();
		var faqadd = {
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("user-edit-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});
</script>
</body>
</html>
