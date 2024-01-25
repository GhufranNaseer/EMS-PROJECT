<?php $this->load->view('includes/head'); ?>

<link rel="stylesheet" href="<?= base_url('assets') ?>/iCheck/square/green.css">
<style>
    body, #wrapper {
        background-color: #f3f3f3;
    }
    .checkbox label {
        padding-left: 0;
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

            <div class="login-logo">
                <a href="<?= base_url (); ?>"><?= (PROJECT_LOGO != '') ? '<img src="'.base_url(PROJECT_LOGO) . '" />' : PROJECT_NAME ?></a>
            </div><!-- /.login-logo -->

            <div class="login-box-body">

                <p class="login-box-msg">Login</p>

                <form action="<?= base_url ('login-submit')?>" method="post" id="login-form">

                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" placeholder="Email" name="username" autocomplete="off">
                        <span class="fa fa-envelope form-control-feedback"></span>
                    </div>

                    <div class="form-group has-feedback">
                        <input type="password" class="form-control"  placeholder="Password" name="password">
                        <span class="fa fa-lock form-control-feedback"></span>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 text-center" id="msg-box" style="color:#F00;">&nbsp;</div>
                    </div>
                    <br>


                    <div class="row">
                        <div class="col-xs-8">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="rememberme" id="remember">  Remember me
                                </label>
                            </div>
                        </div>

                        <div class="col-xs-4">
                            <button type="button" id="btn-submit" class="btn btn-primary btn-block btn-flat">Login</button>
                        </div>
                    </div>
                </form>

                <a href="<?= base_url ('forget-password')?>">Forgot password</a><br>
            </div>
        </div><!-- /.login-box -->

        <!--<div class="text-center">
            <p>Powered By
                <strong>
                    <a href="http://www.minimaxsolution.com/" target="_blank">
                        <img src="http://www.minimaxsolution.com/wp-content/uploads/2017/02/logo2-1024x307.png" alt="miniMAX Solution" style="width: 80px;vertical-align: text-top;">
                    </a>
                </strong>
            </p>
        </div>-->

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
<script src="<?= base_url ("assets/js/doFormValidation.js"); ?>"></script>
<script>
	$('#remember').iCheck({
		checkboxClass: 'icheckbox_square-green',
		radioClass:    'iradio_square-green',
		increaseArea:  '20%' // optional
	});

	$(function () {
		var obj = {
			'form':'#login-form',
            'msgbox':'#msg-box',
            'urlValidator': "<?php echo base_url("login-validate"); ?>",
            'loadingImg':"<?php echo base_url("assets/img/load-indicator.gif"); ?>" ,
            'onFormError':function (){
            	$('input').addClass('validation-fail');
            }
		};
		doFormValidation (obj);
	});
</script>

</body>
</html>