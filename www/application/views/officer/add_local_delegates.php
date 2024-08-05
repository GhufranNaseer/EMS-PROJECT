<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="officer">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Local Delegates
            <small>Add</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('officer.html'); ?>"><i class="fa fa-dashboard"></i> Officer List</a></li>
            <li class="active">Add Local Delegates</li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Add Local Delegates</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('officer-local-delegates-submit.html') ?>?id=<?= $this->input->get('id') ?>" method="post" id="add_agent_form"
                              enctype="multipart/form-data">

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Designation <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="officer_designation"  id="officer_designation">
                                </div>
                                <div class="col-sm-6">
                                    <label>Country <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="officer_country" value="Pakistan" id="officer_country" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Profile</label>
                                    <input type="text" class="form-control" name="officer_rank"  id="officer_rank">
                                </div>
                                <div class="col-sm-6">
                                    <label>Contact Person Name <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="contact_person_name" id="contact_person_name">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Organization<span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="organization" id="organization">
                                </div>
                                <div class="col-sm-6">
                                    <label>Mobile Number<span class="text-red">*</span></label>
                                    <input type="number" class="form-control" name="mobile_number" id="mobile_number">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Email<span class="text-red">*</span></label>
                                    <input type="email" class="form-control" name="email" id="email">
                                </div>
                                <div class="col-sm-6">
                                    <label>Password<span class="text-red">*</span></label>
									<?php
									$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
									$password = array();
									$alpha_length = strlen($alphabet) - 1;
									for ($i = 0; $i < 8; $i++)
									{
										$n = rand(0, $alpha_length);
										$password[] = $alphabet[$n];
									}
									?>
                                    <input type="text" class="form-control" readonly name="password" id="password" value="<?= implode($password) ?>">
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
</div><!-- /.content-wrapper -->

<?php $this->load->view('includes/after_login/footer'); ?>

<script>
	$(function () {

		var faqadd = {
			'form':         '#add_agent_form',
			'msgbox':       '#add_agent_form .js-msgbox',
			'btnClick':     '#add_agent_form .js-form_btn',
			'urlValidator': "<?php echo base_url("officer-local-delegates-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});
</script>
</body>
</html>
