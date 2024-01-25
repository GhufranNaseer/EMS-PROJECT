<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="officer">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Officer
            <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
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
                        <h3 class="box-title">Officer Edit</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('officer-edit-submit.html') ?>?id=<?= $this->input->get('id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">


                            <p class="help-block"><span class="text-red">*</span> Required fields</p>
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Officer Category</label>
                                    <select name="officer_type" class="form-control">

                                        <option value="">- None -</option>
                                            <option value="delegations" <?= (isset($this->formdata) && $this->formdata->officer_type == 'delegations') ? 'selected' : '' ?>>Delegations</option>
                                            <option value="government_official" <?= (isset($this->formdata) && $this->formdata->officer_type == 'government_official') ? 'selected' : '' ?>>Government Official</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Name of Officer <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="officer_name" id="officer_name" value="<?= html_escape(ucwords($this->formdata->officer_name)) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Officer Designation <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="officer_designation" id="officer_designation" value="<?= html_escape(ucwords($this->formdata->officer_designation)) ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Officer Email <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="officer_email" id="officer_email" value="<?= ($this->formdata->officer_email) ?>" readonly>
                                </div>
                                <div class="col-sm-6">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Officer Telephone <span class="text-red">*</span></label>
                                    <input type="number" class="form-control" name="officer_phone"  id="officer_phone" value="<?= ($this->formdata->officer_phone) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Officer Fax</label>
                                    <input type="number" class="form-control" name="officer_fax" id="officer_fax" value="<?= ($this->formdata->officer_fax) ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label>Officer Country <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="officer_country" id="officer_country" list="country_list" value="<?= ($this->formdata->officer_country) ?>">
                                    <datalist id="country_list">
										<?= get_instance()->funcs->print_input_data_list('country'); ?>
                                    </datalist>
                                </div>
                                <div class="col-sm-4">
                                    <label>Officer City <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="officer_city" list="cities_list" id="officer_city" value="<?= ($this->formdata->officer_city) ?>">
                                    <datalist id="cities_list">
										<?= get_instance()->funcs->print_input_data_list('city'); ?>
                                    </datalist>
                                </div>
                                <div class="col-sm-4">
                                    <label>Officer Zip Code</label>
                                    <input type="number" class="form-control" name="officer_zip_code" id="officer_zip_code" value="<?= ($this->formdata->officer_zip_code) ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Officer Address <span class="text-red">*</span></label>
                                <textarea name="officer_address" class="form-control" id="officer_address" rows="3"><?= ($this->formdata->officer_address) ?></textarea>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Name for Corresponding Officer <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="user_name_for_corresponding_officer" id="user_name_for_corresponding_officer" value="<?= ($this->formdata->user_name_for_corresponding_officer) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Corresponding Officer Email address <span class="text-red">*</span></label>
                                    <input type="email" class="form-control" name="user_corresponding_email_address" id="user_corresponding_email_address" value="<?= ($this->formdata->user_corresponding_email_address) ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Corresponding Officer Phone No <span class="text-red">*</span></label>
                                    <input type="number" class="form-control" name="user_corresponding_phone_no" id="user_corresponding_phone_no" value="<?= ($this->formdata->user_corresponding_phone_no) ?>">
                                </div>
                                <div class="col-sm-6">
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

		$("#user_group_id").change();
		var faqadd = {
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("officer-edit-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});
</script>
</body>
</html>
