<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="forms">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Form
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
                        <h3 class="box-title">Form Edit</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('forms-edit-submit.html') ?>?id=<?= $this->input->get('id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">


                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="form_number">Form No</label>
                                        <input type="text" class="form-control" name="form_number"
                                               id="form_number"
                                               value="<?= html_escape(ucwords($this->formdata->form_number)) ?>"
                                               placeholder="Form No">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="form_name">Form Name</label>
                                        <input type="text" class="form-control" name="form_name"
                                               id="form_name"
                                               value="<?= html_escape(ucwords($this->formdata->form_name)) ?>"
                                               placeholder="Form Name">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!--<div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="form_view">Form View</label>
                                        <input type="text" class="form-control" name="form_view"
                                               value="<?/*= html_escape($this->formdata->form_view) */?>" id="form_view"
                                               placeholder="Form View" disabled>
                                    </div>
                                </div>-->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Form Type</label>
                                        <input type="text" class="form-control"
                                               value="<?= ($this->formdata->is_essential == 1) ? 'Essential Form' : 'Optional Form' ?>" disabled>
                                    </div>
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
		var faqadd = {
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("forms-edit-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});
</script>
</body>
</html>
