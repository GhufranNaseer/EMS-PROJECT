<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="customers">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Customer
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
                        <h3 class="box-title">Customer Edit</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('customer-edit-submit.html') ?>?id=<?= $this->input->get('id') ?>"
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


                            <p class="help-block"><span class="text-red">*</span> Required fields</p>
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Exhibitor Company <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="company" id="company" value="<?= html_escape(ucwords($this->formdata->company)) ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Executive Name <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name" value="<?= html_escape(ucwords($this->formdata->name)) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Designation<span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="designation" id="designation" value="<?= html_escape(ucwords($this->formdata->designation)) ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Email <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="email" id="email" value="<?= ($this->formdata->email) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>URL</label>
                                    <input type="text" class="form-control" name="url" id="url" value="<?= ($this->formdata->url) ?>"">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Fax</label>
                                    <input type="number" class="form-control" name="fax" id="fax" value="<?= ($this->formdata->fax) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Telephone<span class="text-red">*</span></label>
                                    <input type="number" class="form-control" name="phone"  id="phone" value="<?= ($this->formdata->phone) ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label>Country<span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="country" id="country" list="country_list" value="<?= ($this->formdata->country) ?>">
                                    <datalist id="country_list">
										<?= get_instance()->funcs->print_input_data_list('country'); ?>
                                    </datalist>
                                </div>
                                <div class="col-sm-4">
                                    <label>City<span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="city" list="cities_list" id="city" value="<?= ($this->formdata->city) ?>">
                                    <datalist id="cities_list">
										<?= get_instance()->funcs->print_input_data_list('city'); ?>
                                    </datalist>
                                </div>
                                <div class="col-sm-4">
                                    <label>Zip Code</label>
                                    <input type="number" class="form-control" name="zip_code" id="zip_code" value="<?= ($this->formdata->zip_code) ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Address<span class="text-red">*</span></label>
                                <textarea name="address" class="form-control" id="address" rows="3"><?= ($this->formdata->address) ?></textarea>
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
			'urlValidator': "<?php echo base_url("customer-edit-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});
</script>
</body>
</html>
