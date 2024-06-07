<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="officer">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
			Government Officials
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
                        <h3 class="box-title">Government Officials Edit</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('officer-edit-government-officials-submit.html') ?>?id=<?= $this->input->get('id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">


                            <p class="help-block"><span class="text-red">*</span> Required fields</p>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Designation <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="officer_designation" id="officer_designation" value="<?= ($this->formdata->officer_designation)?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Country <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="officer_country" id="officer_country" readonly value="<?= ($this->formdata->officer_country) ?>">
                                </div>
                            </div>
							<div class="form-group row">
                                <div class="col-sm-12">
                                    <label>
                                        <input type="checkbox" name="representative" id="representative" value=""
                                        <?= ($this->formdata->is_representative == 1) ? 'checked' : '' ?> > Representative
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Contact Person Name <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="contact_person_name" value="<?= ($this->formdata->contact_person)?>" id="contact_person_name">
                                </div>
								<div class="col-sm-6">
                                    <label>Mobile Number</label>
                                    <input type="number" class="form-control" name="mobile_number" value="<?= ($this->formdata->officer_phone)?>" id="mobile_number">
                                </div>
                            </div>


                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Email<span class="text-red">*</span></label>
                                    <input type="email" class="form-control" name="email" id="email" value="<?= ($this->formdata->officer_email)?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Password<span class="text-red">*</span></label>

                                    <input type="text" class="form-control" readonly name="password" id="password" value="<?= ($this->formdata->login_password) ?>">
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
			'urlValidator': "<?php echo base_url("officer-edit-government-officials-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});
</script>
</body>
</html>
