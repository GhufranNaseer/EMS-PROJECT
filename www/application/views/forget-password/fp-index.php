
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
            </div>
            <br>
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        &nbsp;
                    </div>
                </div><!-- /.col -->
                <div class="col-xs-4">
                    <button type="button" id="btn-submit" class="btn btn-primary btn-block btn-flat">Send Email</button>
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
	$(function () {
		var obj = {
			'form':         '#login-form',
			'msgbox':       '#msg-box',
			'urlValidator': "<?php echo base_url("forget-password-validate"); ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>",
			'onFormError':  function () {
				$('input').addClass('validation-fail');
			}
		};
		doFormValidation(obj);
	});
</script>
</body>
</html>