<?php $this->load->view('includes/head'); ?>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="<?= base_url(); ?>">
            <a href="<?= base_url(); ?>"><?= (PROJECT_LOGO != '') ? '<img src="' . base_url(PROJECT_LOGO) . '" />' : PROJECT_NAME ?></a>
        </a>
    </div><!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Recover Your Account Now</p>
        <form action="<?= base_url("recover-account-submit/{$recover}"); ?>" method="post" id="fp-form">
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Password" name="password">
                <span class="fa fa-lock form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Confirm Password" name="confirmPassword">
                <span class="fa fa-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-sm-12 text-center" id="msg-box" style="color:#F00;">&nbsp;</div>
            </div>
            <br>
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        &nbsp;
                    </div>
                </div><!-- /.col -->
                <div class="col-xs-4">
                    <button type="button" id="btn-submit"
                            class="btn btn-primary btn-block btn-flat">Change</button>
                </div><!-- /.col -->
            </div>
        </form>
        <a href="<?= base_url('') ?>">Login now</a><br>
    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->

<script type="text/javascript" src="<?= base_url('assets'); ?>/js/jquery-2.2.3.min.js"></script>

<script src="<?= base_url ("assets/js/doFormValidation.js"); ?>"></script>
<script>
	$(function () {
		var obj = {
			'form':         '#fp-form',
			'msgbox':       '#msg-box',
			'urlValidator': "<?= base_url("recover-account-validate/{$recover}"); ?>",
			'loadingImg':   "<?= base_url("assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(obj);
	});
</script>
</body>
</html>