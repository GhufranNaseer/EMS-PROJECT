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
    .btn-primary.btn-flat {
        background: <?= $event->event_color ?>;
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
        height: 150px;
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
                <a style="font-weight: 500; text-transform: uppercase;" href="<?= base_url('login/' . $event->id . '-' . str_replace(' ', '-', $event->exhibition_title)); ?>"><?= $event->exhibition_title ?></a>
            </div>
        </div>
        <div class="row" style="margin-top: 5%">
            <div class="col-md-2">
                <div class="form-group">
                    <a href="<?= (!is_null($event->event_logo_link) && $event->event_logo_link != '') ? $event->event_logo_link : '#' ?>" target="_blank">
                        <img src="<?= base_url('../' . $event->event_logo) ?>" class="img-responsive img-thumbnail img-logo" alt="">
                    </a>
                </div>

                <?php

                $event_organizer = $this->db
                    ->where('id', $event->event_organizer)
                    ->get('es_organizer')
                    ->row();

                if ($event_organizer && !is_null($event_organizer->organizer_image) && $event_organizer->organizer_image != '') {
                    echo '<div class="form-group">
                    <a href="#" target="_blank">
                        <img src="'. base_url('../' . $event_organizer->organizer_image) .'" class="img-responsive img-thumbnail img-logo" alt="">
                    </a>
                    </div>';
                }

                ?>

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
						<?php
						$message = $this->session->flashdata('message');
						if (is_string($message)) {
							echo '<div class="alert alert-success text-center">'.$message.'</div>';
						}
						$error = $this->session->flashdata('error');
						if (is_string($error)) {
							echo '<div class="alert alert-danger text-center">'.$error.'</div>';
						}
						?>
                        <div class="box">
                            <div class="box-header"><h3 style="text-align: center;">Sign in to start your session</h3></div>
                            <div class="box-body">
                                <form action="<?= base_url('login-submit') ?>?id=<?= myid($event->id) ?>" method="post" id="login-form">

                                    <div class="form-group has-feedback">
                                        <input type="text" class="form-control" placeholder="Login ID" name="username"
                                               autocomplete="off">
                                        <span class="fa fa-envelope form-control-feedback"></span>
                                    </div>

                                    <div class="form-group has-feedback">
                                        <input type="password" class="form-control" placeholder="Password"
                                               name="password">
                                        <span class="fa fa-lock form-control-feedback"></span>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12 text-center" id="msg-box" style="color:#F00;">&nbsp;</div>
                                    </div>
                                    <br>

                                    <div class="row">
                                        <div class="col-xs-8">
                                            <a href="<?= base_url ('forget-password/' . $event->id . '-' . str_replace(' ', '-', $event->exhibition_title))?>">Forgot password</a>
                                        </div>

                                        <div class="col-xs-4">
                                            <button type="button" id="btn-submit"
                                                    class="btn btn-primary btn-block btn-flat">Login
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <div class="box" style="height: 406px; overflow: hidden;">
                            <div class="box-header"><h3 style="text-align: center;">Event Update</h3></div>
                            <div class="box-body" style="">
                                <marquee behavior="scroll" height="406px" scrollamount="1"  direction="up" onmouseover="this.stop();" onmouseout="this.start();">
                                <ul class="products-list product-list-in-box">
									<?php
									$event_updates = $this->db
										->where('exhibition_id', $event->id)
										->where('type', 'update')
										->where('is_deleted', 0)
                                        ->limit(5)
										->order_by('id', 'DESC')
										->get('es_exhibition_notification')
										->result();

									if (count($event_updates) > 0) {
										foreach ($event_updates as $event_update) {
											$img_link = (!is_null($event_update->attachments) && $event_update->attachments != '') ? $event_update->attachments : 'uploads/profile/default.png';
											echo '<li class="item">
                                        <div class="product-img">
                                            <img src="'. base_url('../' . $img_link) .'" alt="">
                                        </div>
                                        <div class="product-info">
                                            <a href="javascript:void(0)" class="product-title open_event_updates_modal" data-id="'.myid($event_update->id).'">'. $event_update->title .'</a>
                                            <span class="product-description">'. substr(strip_tags($event_update->description), 0, 40) .'...</span>
                                            <span class="product-description small">'. date('d/m/Y', strtotime($event_update->created_on)) .'</span>
                                        </div>
                                    </li>';
										}
									} else {
										echo '<li class="item">
                                    <div class="text-center">No event updates available!</div>
                                    </li>';
									}
									?>
                                </ul>
                                </marquee>
                            </div>
                        </div>
                    </div>
                </div>

                <!--<div class="box">
                    <div class="box-header">Disclaimer</div>
                    <div class="box-body">
                        <p class="text-center">Company Address: _______________________________________</p>
                    </div>
                </div>-->
            </div>
            <div class="col-md-2 text-center">
                <?php

                $advertisment = $this->db
                    ->where('exhibition_id' , $event->id)
                    ->get('es_exhibition_advertisment')
                    ->row();
                
                if ($advertisment && !empty($advertisment->attachment)): ?>
                    <img src="<?= base_url('../' . $advertisment->attachment) ?>"  alt=""  height="640px" width="100%">
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>

<!-- Lightbox for event updates -->
<div class="modal fade" id="event_update_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> <!-- /.modal -->

<!-- Go To Top
============================================= -->
<div id="gotoTop" class="icon-angle-up"></div>

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
			'urlValidator': "<?php echo base_url("login-validate"); ?>?id=<?= myid($event->id) ?>",
			'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>",
			'onFormError':  function () {
				$('input').addClass('validation-fail');
			}
		};
		doFormValidation(obj);
	});


	$(document).on('click', '.open_event_updates_modal', function (e) {
		e.stopImmediatePropagation();

		var id = $(this).attr('data-id');
		$.ajax({
			type:    'post',
			url:     '<?php echo base_url("event_updates"); ?>',
			data:    {notification_id: id},
			success: function (data) {
				data = JSON.parse(data);
				console.log(data);
				if (data.error) {
					swal({
						title: '',
						text: data.message,
						type: "warning"
					});
					return;
				}

				$('#event_update_modal .modal-title').html(data.data.title);
				$('#event_update_modal .modal-body').html('');

				if (data.data.attachments != null && data.data.attachments != '') {
					$('#event_update_modal .modal-body').append('<img src="<?= base_url('../') ?>'+data.data.attachments+'" class="img-thumbnail pull-right" width="150px">');
				}

				$('#event_update_modal .modal-body').append(data.data.description);
				$('#event_update_modal .modal-body').append('<div class="clearfix"></div>');
				$('#event_update_modal').modal({backdrop: 'static', keyboard: false, show: true}); // open lightbox
			},
			error:   function () {
			}
		});
	});
</script>

</body>
</html>