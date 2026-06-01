
<?php $this->load->view('includes/head'); ?>

<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="<?= base_url(); ?>">
            <a href="<?= base_url (); ?>"><?= (PROJECT_LOGO != '') ? '<img src="'.base_url(PROJECT_LOGO) . '" />' : PROJECT_NAME ?></a>
        </a>
    </div><!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Recover Your Account with a link that will be sent to your email address</p>
        <form action="<?= base_url('forget-password-submit') ?>" method="post" id="login-form">
            <div class="form-group has-feedback">
                <input type="email" class="form-control" placeholder="Email" name="username" autocomplete="off">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-sm-12 text-center" id="msg-box" style="color:#F00;">&nbsp;</div>
				<?php
				$error = $this->session->flashdata('error');
				if (is_string($error)) {
					echo '<div class="alert alert-danger text-center">'.$error.'</div>';
				}
				?>
            </div>
            <br>
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        &nbsp;
                    </div>
                </div><!-- /.col -->
                <div class="col-xs-4">
					<button class="btn btn-primary btn-block btn-flat" type="submit" id="btn-submit">Send Email</button>
                </div><!-- /.col -->
            </div>
        </form>
        <a href="<?= base_url('') ?>">Login now</a><br>
    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->
<!-- jQuery 2.1.4 -->
<script type="text/javascript" src="<?= base_url('assets'); ?>/js/jquery-2.2.3.min.js"></script>

<script src="<?= base_url ("assets/js/doFormValidation.js"); ?>"></script>

<script>

	$('#login-form').on('submit', function (e) {
		e.preventDefault();
		var form = this;
		$.ajax({
			url: "<?php echo base_url("forget-password-validate"); ?>",
			type: 'POST',
			data: $(form).serialize()
		}).done(function (data) {
			$("#login-form .validation-failed").removeClass("validation-failed");
			$("#login-form .do-error-msg").remove();

			if (data == "done") {
				$('#msg-box').html('');
				$('#msg-box').html('<img src="<?php echo base_url("assets/img/load-indicator.gif"); ?>" />');
				form.submit();
			} else {
				$('#msg-box').html(data);
			}
		});
	});
</script>
</body>
</html>