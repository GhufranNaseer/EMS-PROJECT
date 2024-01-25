<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="exhibitions">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Event
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
                        <h3 class="box-title">Event Add</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('exhibitions-submit.html') ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">

							<?php $this->load->view('exhibitions/_form'); ?>

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
			'urlValidator': "<?php echo base_url("exhibitions-validate.html"); ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		});
	});

	var file_event_logo = new file_upload_preview({
		selector: '#event_logo_container',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: 'jpg|jpeg|png|PNG',
		base_url: 'uploads/exhibition/',
		post_file_name: 'event_logo',
		has_rotation: false,
		max_upload: 1
	});
	var file_event_associate_logo = new file_upload_preview({
		selector: '#event_associate_logo_container',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: 'jpg|jpeg|png|PNG',
		base_url: 'uploads/exhibition/',
		post_file_name: 'event_associate_logo',
		has_rotation: false,
		max_upload: 1
	});
	var file_event_manager_logo = new file_upload_preview({
		selector: '#event_manager_logo_container',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: 'jpg|jpeg|png|PNG',
		base_url: 'uploads/exhibition/',
		post_file_name: 'event_manager_logo',
		has_rotation: false,
		max_upload: 1
	});
	var file_event_background_container = new file_upload_preview({
		selector: '#event_background_container',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: 'jpg|jpeg|png|PNG',
		base_url: 'uploads/exhibition/',
		post_file_name: 'event_background',
		has_rotation: false,
		max_upload: 1
	});
</script>
<?php $this->load->view('exhibitions/_form_script'); ?>

</body>
</html>
