<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="event_notification">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Notification
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
                        <h3 class="box-title">Notification Edit</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('notification-edit-submit.html') ?>?id=<?= $this->input->get('id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="title">Notification Title</label>
                                        <input type="text" class="form-control" name="title"
                                               id="title"
                                               value="<?= html_escape(ucwords($this->formdata->title)) ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Notification Type</label>
                                        <select name="type" class="form-control notification_type">
                                            <option value="update" <?= ($this->formdata->type == 'update') ? 'selected' : '' ?>>Event Updates</option>
                                            <option value="downloads" <?= ($this->formdata->type == 'downloads') ? 'selected' : '' ?>>Downloads</option>
                                            <option value="notification" <?= ($this->formdata->type == 'notification') ? 'selected' : '' ?>>Notification Alert</option>
                                            <option value="end_user_note" <?= ($this->formdata->type == 'end_user_note') ? 'selected' : '' ?>>Note for End User Certificate</option>
                                            <option value="trade_visitor_badge_terms" <?= ($this->formdata->type == 'trade_visitor_badge_terms') ? 'selected' : '' ?>>Terms for Trade Visitor Badge</option>
                                            <option value="hotel_reservation_terms" <?= ($this->formdata->type == 'hotel_reservation_terms') ? 'selected' : '' ?>>Terms for Hotel Reservation</option>
                                            <option value="display_mobility_terms" <?= ($this->formdata->type == 'display_mobility_terms') ? 'selected' : '' ?>>Terms for Display Mobility</option>
                                            <option value="vehicle_rental_terms" <?= ($this->formdata->type == 'vehicle_rental_terms') ? 'selected' : '' ?>>Terms for Vehicle Rental</option>
                                            <option value="additional_items_terms" <?= ($this->formdata->type == 'additional_items_terms') ? 'selected' : '' ?>>Terms for Additional Items</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row notification_type_container" style="display: <?= ($this->formdata->type == 'update') ? 'block' : 'none' ?>">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" name="description" rows="5"
                                                  id="description"><?= html_escape($this->formdata->description) ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
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
			'urlValidator': "<?php echo base_url("notification-edit-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		});
	});

	var editor = CKEDITOR.replace('description');

	setInterval(function () {
		if ($('.notification_type').val() == 'update') {
			$('#description').val(editor.getData());
		}
	}, 600)

	var file_event_logo = new file_upload_preview({
		selector: '#event_logo_container',
		ajax_src: '<?= base_url('welcome/file_upload') ?>',
		extensions: [
			'jpg',
			'jpeg',
			'png',
			'PNG',
			{
				type: 'pdf',
				icon: '<i class="fa fa-file-pdf-o"></i>'
			},
			{
				type: 'doc',
				icon: '<i class="fa fa-file-word-o"></i>'
			},
			{
				type: 'docx',
				icon: '<i class="fa fa-file-word-o"></i>'
			},
			{
				type: 'xls',
				icon: '<i class="fa fa-file-excel-o"></i>'
			},
			{
				type: 'xlsx',
				icon: '<i class="fa fa-file-excel-o"></i>'
			},
			{
				type: 'pptx',
				icon: '<i class="fa fa-file-powerpoint-o"></i>'
			}
		],
		base_url: 'uploads/notifications/',
		post_file_name: 'attachment',
		has_rotation: false,
		max_upload: 1,
		predefined_images: [<?= json_encode($this->formdata->attachments) ?>]
	});

	$(document).on('change', '.notification_type', function (e) {
		e.stopImmediatePropagation();

		$('.attachment_area').show();
		if ($(this).val() == 'update') {
			$('.notification_type_container').show();
		} else if ($(this).val() == 'end_user_note' ||
			$(this).val() == 'additional_items_terms' ||
			$(this).val() == 'trade_visitor_badge_terms' ||
			$(this).val() == 'hotel_reservation_terms' ||
			$(this).val() == 'display_mobility_terms' ||
			$(this).val() == 'vehicle_rental_terms') {
			$('.attachment_area').hide();
		} else {
			$('.notification_type_container').hide();
			$('#description').val('');
		}
	});

</script>
</body>
</html>
