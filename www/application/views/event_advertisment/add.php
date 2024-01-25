<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="event_advertisment">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Advertisement
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Add</li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Advertisement Add</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('advertisment-submit.html') ?>?id=<?= $this->input->get('id') ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="title">Advertisement Title</label>
                                        <input type="text" class="form-control" name="title"
                                               id="title" placeholder="Title">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="attachment_link">Advertisement  Link</label>
                                    <input type="text" class="form-control" name="attachment_link"
                                           id="attachment_link" placeholder="Advertisment  Link">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6" style="padding-left: 32px;">
                                    <label>Attachments</label>
                                    <div id="event_logo_container"></div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="expire_on">Expire on</label>
                                    <input type="date" class="form-control" name="expire_on"
                                           id="expire_on" placeholder="Expire on">
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
			'urlValidator': "<?php echo base_url("advertisment-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		});
	});

	var file_event_logo = new file_upload_preview({
		selector: '#event_logo_container',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: [
			'jpg',
			'jpeg',
			'png',
			'PNG'
		],
		base_url: 'uploads/notification/',
		post_file_name: 'attachment',
		has_rotation: false,
		max_upload: 1
	});

</script>

</body>
</html>
