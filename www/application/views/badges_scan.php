<?php $this->load->view('includes/head'); ?>

<link rel="stylesheet" href="<?= base_url('assets') ?>/iCheck/square/green.css">
<style>
    body, #wrapper {
        background-color: #f3f3f3;
    }
    .checkbox label {
        padding-left: 0;
    }
    table {
        margin: 0 !important;
    }
    table h2 {
        margin: 10px 0;
    }
    table td {
        font-size: 18px;
    }
</style>
<body class="stretched">

<!-- Document Wrapper
============================================= -->
<div id="wrapper" class="clearfix">


    <!-- Sign Up
	============================================= -->
    <div class="container">

        <div class="login-box">
            <h2 class="login-box-msg">Event Management System</h2>

            <div class="login-box-body">
                <input type="text" class="form-control badges_id" placeholder="Badge Number" name="badges_id"></span>
            </div>

        </div><!-- /.login-box -->

        <div class="login-box-body" style="width: 60%; margin: 0 auto;">
            <table class="table table_border" width="100%"></table>
        </div>

    </div>


</div>



<!-- Go To Top
============================================= -->
<div id="gotoTop" class="icon-angle-up"></div>

<!-- External JavaScripts
============================================= -->
<script type="text/javascript" src="<?= base_url('assets'); ?>/js/jquery-2.2.3.min.js"></script>
<script type="text/javascript" src="<?= base_url('assets'); ?>/js/jquery-ui.js"></script>
<script src="<?= base_url('assets') ?>/iCheck/icheck.min.js"></script>
<script>

	$('.badges_id').focus();


	$(document).on('keyup','.badges_id', function (e) {
		e.stopImmediatePropagation();

		var id = $(this).val();

		if (e.keyCode == 13) {
			$.ajax({
				type:'POST',
				data: {
					id:id
				},
				url:'<?php echo base_url('badge-scan_id.html');?>',
				beforeSend: function () {
					$('.table_border').html("<tr class='text-center'><td><img src='<?php echo base_url('assets'); ?>/datatable/images/ajax-loader_dark.gif'></td></tr>")
				},
                success: function (response) {
					$('.table_border').html(response);
				}
			});
		}

	})

</script>

</body>
</html>