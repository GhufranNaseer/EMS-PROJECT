<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="agent">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Agent
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
                        <h3 class="box-title">Agent Edit</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('agent-edit-submit.html') ?>?id=<?= $this->input->get('id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">


                            <p class="help-block"><span class="text-red">*</span> Required fields</p>
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Agent Company <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="agent_company" id="agent_company" value="<?= html_escape(ucwords($this->formdata->agent_company)) ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Agent Name <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="agent_name" id="agent_name" value="<?= html_escape(ucwords($this->formdata->agent_name)) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Agent Designation <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="agent_designation" id="agent_designation" value="<?= html_escape(ucwords($this->formdata->agent_designation)) ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Agent Email <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="agent_email" id="agent_email" value="<?= ($this->formdata->agent_email) ?>">
                                </div>
                                <div class="col-sm-6">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Agent Telephone <span class="text-red">*</span></label>
                                    <input type="number" class="form-control" name="agent_phone"  id="agent_phone" value="<?= ($this->formdata->agent_phone) ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label>Agent Fax</label>
                                    <input type="number" class="form-control" name="agent_fax" id="agent_fax" value="<?= ($this->formdata->agent_fax) ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label>Agent Country <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="agent_country" id="agent_country" list="country_list" value="<?= ($this->formdata->agent_country) ?>">
                                    <datalist id="country_list">
										<?= get_instance()->funcs->print_input_data_list('country'); ?>
                                    </datalist>
                                </div>
                                <div class="col-sm-4">
                                    <label>Agent City <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="agent_city" list="cities_list" id="agent_city" value="<?= ($this->formdata->agent_city) ?>">
                                    <datalist id="cities_list">
										<?= get_instance()->funcs->print_input_data_list('city'); ?>
                                    </datalist>
                                </div>
                                <div class="col-sm-4">
                                    <label>Agent Zip Code</label>
                                    <input type="number" class="form-control" name="agent_zip_code" id="agent_zip_code" value="<?= ($this->formdata->agent_zip_code) ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Agent Address <span class="text-red">*</span></label>
                                <textarea name="agent_address" class="form-control" id="agent_address" rows="3"><?= ($this->formdata->agent_address) ?></textarea>
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
			'urlValidator': "<?php echo base_url("agent-edit-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});
</script>
</body>
</html>
