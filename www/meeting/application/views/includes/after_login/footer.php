</div> <!-- /.wrapper -->


<script src="<?= base_url('../assets') ?>/js/jquery-2.2.3.min.js"></script>
<script src="<?= base_url('../assets') ?>/bootstrap/js/bootstrap.min.js"></script>

<script src="<?= base_url('../assets') ?>/jQueryUI/jquery-ui.js"></script>

<script src="<?= base_url('../assets') ?>/iCheck/icheck.min.js"></script>

<script src="<?= base_url('../assets') ?>/datatable/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url('../assets') ?>/js/my_datatable.js"></script>

<script src="<?= base_url('../assets') ?>/timepicker/bootstrap-timepicker.min.js"></script>

<script src="<?= base_url('../assets') ?>/daterangepicker/moment.min.js"></script>
<script src="<?= base_url('../assets') ?>/daterangepicker/daterangepicker.js"></script>
<script src="<?= base_url('../assets') ?>/datepicker/bootstrap-datepicker.js"></script>

<script src="<?= base_url('../assets') ?>/bootstrap-select/js/bootstrap-select.min.js"></script>

<script src="<?= base_url('../assets') ?>/file-upload-preview/lib/cropper.min.js"></script>
<script src="<?= base_url('../assets') ?>/file-upload-preview/js/file-upload-preview.js"></script>

<script src="<?= base_url('../assets') ?>/sweetalert/sweetalert.min.js"></script>

<script src="<?= base_url('../assets') ?>/input-mask/jquery.inputmask.js"></script>

<!-- AdminLTE App -->
<script src="<?= base_url('../assets') ?>/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<!--<script src="<?/*= base_url('../assets') */?>/js/demo.js"></script>-->


<script src="<?= base_url('../assets') ?>/js/validation.js"></script>
<script src="<?= base_url('../assets') ?>/js/doFormValidation.js"></script>


<script>


	$(document).ready(function () {
		$('.sidebar-menu li').removeClass('active');

		var activePage = $('.content-wrapper').attr('data-page');
		$('.sidebar-menu li[data-page="' + activePage + '"]').addClass('active');
		if ($('.sidebar-menu li[data-page="' + activePage + '"]').parents('.treeview').length > 0) {
			$('.sidebar-menu li[data-page="' + activePage + '"]').parents('.treeview').addClass('active');
		}


		$(document).on('click', '.js-menu_sign_out', function (e) {
			e.stopImmediatePropagation();

			$('.navbar-custom-menu').find('.user-menu').toggleClass('open');
		});

		$('.icheck').iCheck({
			checkboxClass: 'icheckbox_square-blue',
			radioClass:    'iradio_square-blue',
			increaseArea:  '20%' // optional
		});

		<?php
		$message = $this->session->flashdata('message');
		if (is_string($message)) { ?>
		swal({
			title: '',
			text: '<?= $message ?>',
			type: "success"
		});
		<?php } ?>

		<?php
		$error_message = $this->session->flashdata('error');
		if (is_string($error_message)) { ?>
		swal({
			title: '',
			text: '<?= $error_message ?>',
			type: "warning"
		});
		<?php } ?>
	});

	var oTable = null;
	var datatable_settings = {
		aLengthMenu:       [
			[25, 50, 100, 200, 500],
			[25, 50, 100, 200, 500]
		],
		iDisplayLength:    25,
		"bProcessing":     true,
		"bServerSide":     true,
		"sScrollX":        '100%',
		"bJQueryUI":       true,
		"fnRowCallback":   function (nRow, aData, iDisplayIndex) {
			var oSettings = oTable.fnSettings();
			$("td:first", nRow).html(oSettings._iDisplayStart + iDisplayIndex + 1);
			return nRow;
		},
		"aaSorting":       [[0, 'asc']],
		"sPaginationType": "full_numbers",
		"iDisplayStart ":  20,
		"oLanguage":       {
			"sProcessing": "<img src='<?php echo base_url('../assets'); ?>/datatable/images/ajax-loader_dark.gif'>"
		},
		"fnInitComplete":  function () {
			oTable.fnAdjustColumnSizing();
		},
		'fnServerData':    function (sSource, aoData, fnCallback) {
			$.ajax({
				'dataType': 'json',
				'type':     'POST',
				'url':      sSource,
				'data':     aoData,
				'success':  fnCallback
			});
		}
	}

	function confFunc(text,funcYes,funcNo) {
		$('body > #confirm-box').remove();

		$('body').append('<div class="modal fade" id="confirm-box" >' +
			'<div class="modal-dialog modal-sm">' +
			'<div class="modal-content">' +
			'<div class="modal-body">' +
			'<div class="row">' +
			'<div class="col-sm-2">' +
			'<i class="fa fa-exclamation-triangle"></i>' +
			'</div>' +
			'<div class="col-sm-10">'+text +
			'</div>' +
			'</div>' +
			'</div>' +
			'<div class="modal-footer">' +
			'<div class="row">' +
			'<div class="col-sm-6">' +
			'<button type="button" class="btn btn-default btn-block btn-confirm modalNewButtons" data-confirm="no">No</button>' +
			'</div>' +
			'<div class="col-sm-6">' +
			'<button type="button" class="btn btn-success btn-block btn-confirm modalNewButtons" data-confirm="yes">Yes</button>' +
			'</div>' +
			'</div>' +
			'</div>' +
			'</div>' +
			'</div>' +
			'</div>');

		$('#confirm-box').modal({backdrop:'static',keyboard: false,show: true}); // open lightbox

		$('#confirm-box .btn-confirm').click(function (e) {
			e.stopImmediatePropagation();
			var getConf = $(this).attr('data-confirm');
			if(getConf == 'yes') {
				if (funcYes) {
					funcYes();
				}
			} else {
				if (funcNo) {
					funcNo();
				}
			}
			$('#confirm-box').modal('hide');
		});
	}

	function guid() {
		function s4() {
			return Math.floor((1 + Math.random()) * 0x10000)
				.toString(16)
				.substring(1);
		}
		return new Date().getTime() + '_' + s4() + s4();
	}


	$(document).on('click', '.delete_form_cart_btn', function (e) {
		e.stopImmediatePropagation();
		var id = $(this).attr('data-id');

		$.ajax({
			type:'POST',
			url:"<?PHP echo base_url('delete/cart'); ?>",
			data: {rowid:id},
			success: function (data) {
				location.reload();
			},
			error:   function () {
			}
		});
	});
</script>

