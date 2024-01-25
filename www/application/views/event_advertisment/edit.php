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
                        <h3 class="box-title">Advertisement Edit</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('advertisment-edit-submit.html') ?>?id=<?= $this->input->get('id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="title">Advertisement Title</label>
                                        <input type="text" class="form-control" name="title"
                                               id="title"
                                               value="<?= html_escape(ucwords($this->formdata->title)) ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="attachment_link">Attachment link</label>
                                        <input type="text" class="form-control" name="attachment_link"
                                               id="attachment_link"
                                               value="<?= html_escape(ucwords($this->formdata->attachment_link)) ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="expire_on">Expire on</label>
                                        <input type="date" class="form-control" name="expire_on"
                                               id="expire_on"
                                               value="<?= html_escape(ucwords($this->formdata->expire_on)) ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6" style="padding-left: 32px;">
                                    <label>Attachments</label>
                                    <div id="event_logo_container"></div>
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

<script src="<?= base_url('assets') ?>/ckeditor/ckeditor.js"></script>

<script>
	$(function () {
		doFormValidation({
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("advertisment-edit-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
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
		max_upload: 1,
		predefined_images: [<?= json_encode($this->formdata->attachment) ?>]
	});


</script>
</body>
</html>
