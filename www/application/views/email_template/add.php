<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="email_template">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Email Template
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
                        <h3 class="box-title">Email Template Add</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('email_template-submit.html') ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">


                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input type="text" class="form-control" name="email_template_title"
                                               placeholder="Title">
                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                    <div class="form-group">
                                        <label>Subject</label>
                                        <input type="text" class="form-control" name="email_template_subject"
                                               placeholder="Subject">
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="email_template_message">Message</label>
                                        <textarea class="form-control" name="email_template_message"
                                                  id="email_template_message" rows="5"></textarea>
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

            </div>
        </div><!-- /.row -->
    </section><!-- /.content -->
    <!-- /.content -->
</div><!-- /.content-wrapper -->

<?php $this->load->view('includes/after_login/footer'); ?>
<script src="<?= base_url('assets') ?>/ckeditor/ckeditor.js"></script>
<script>
	$(function () {

		doFormValidation({
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("email_template-validate.html"); ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		});


	});

    var editor = CKEDITOR.replace('email_template_message');
</script>
</body>
</html>
