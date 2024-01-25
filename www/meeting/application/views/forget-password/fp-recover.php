<?php $this->load->view('includes/head'); ?>

<link rel="stylesheet" href="<?= base_url('../assets') ?>/iCheck/square/green.css">
<style>
    body, #wrapper {
        background-color: #f3f3f3;
    }

    <?php if (!is_null($event->event_color) && $event->event_color != '') { ?>
    body, #wrapper {
        background-color: <?= $event->event_color ?>;
    }
    .login-logo a {
        color: #fff;
    }
    <?php } ?>


    <?php if (!is_null($event->event_background) && $event->event_background != '') { ?>
    body, #wrapper {
        background-image: url("<?= base_url('../' . $event->event_background) ?>");
        background-repeat: no-repeat;
        background-position: center center;
        background-attachment: fixed;
        -webkit-background-size: cover;
    }
    .login-logo a {
        color: #fff;
    }
    <?php } ?>

    .checkbox label {
        padding-left: 0;
    }

    .img-logo {
        width: 100%;
        height: auto;
    }
</style>
<body class="stretched">

<!-- Document Wrapper
============================================= -->
<div id="wrapper" class="clearfix">

    <!-- Sign Up
	============================================= -->
    <div class="container">
        <div class="login-box" style="margin: 5% auto; line-height: 35px; width: auto;">
            <div class="login-logo">
                <a href="<?= base_url('login/' . $event->id . '-' . str_replace(' ', '-', $event->exhibition_title)); ?>"><?= $event->exhibition_title ?></a>
            </div>
        </div>

        <div class="row" style="margin-top: 5%">
            <div class="col-md-2">
                <div class="form-group">
                    <a href="<?= (!is_null($event->event_logo_link) && $event->event_logo_link != '') ? $event->event_logo_link : '#' ?>" target="_blank">
                        <img src="<?= base_url('../' . $event->event_logo) ?>" class="img-responsive img-thumbnail img-logo" alt="">
                    </a>
                </div>

                <div class="form-group">
                    <a href="<?= (!is_null($event->associate_logo_link) && $event->associate_logo_link != '') ? $event->associate_logo_link : '#' ?>" target="_blank">
                        <img src="<?= base_url('../' . $event->associate_logo) ?>" class="img-responsive img-thumbnail img-logo" alt="">
                    </a>
                </div>

                <div class="form-group">
                    <a href="<?= (!is_null($event->manager_logo_link) && $event->manager_logo_link != '') ? $event->manager_logo_link : '#' ?>" target="_blank">
                        <img src="<?= base_url('../' . $event->manager_logo) ?>" class="img-responsive img-thumbnail img-logo" alt="">
                    </a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <div class="box" style="background-color: #fff9c4 !important;">
                            <div class="box-header">Recover Your Account Now</div>
                            <div class="box-body">
                                <form action="<?= base_url('recover-account-submit/' . myid($recover['forget_booking']->id)) ?>" method="post" id="login-form">

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
                                        <div class="col-xs-8"></div>
                                        <div class="col-xs-4">
                                            <button type="button" id="btn-submit"
                                                    class="btn btn-primary btn-block btn-flat">Change
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="col-md-2 text-center">

            </div>
        </div>

    </div>

</div>



<!-- External JavaScripts
============================================= -->
<script type="text/javascript" src="<?= base_url('../assets'); ?>/js/jquery-2.2.3.min.js"></script>
<script src="<?= base_url('../assets') ?>/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?= base_url('../assets'); ?>/js/jquery-ui.js"></script>
<script src="<?= base_url('../assets') ?>/iCheck/icheck.min.js"></script>
<script src="<?= base_url("../assets/js/doFormValidation.js"); ?>"></script>
<script>
	$(function () {
		var obj = {
			'form':         '#login-form',
			'msgbox':       '#msg-box',
			'urlValidator': "<?php echo base_url("recover-account-validate/" . myid($recover['forget_booking']->id)); ?>",
			'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>",
			'onFormError':  function () {
				$('input').addClass('validation-fail');
			}
		};
		doFormValidation(obj);
	});

</script>

</body>
</html>