<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="email_send_to_companies">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Send Emails To companies
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('email-send-to-companies'); ?>"><i class="fa fa-dashboard"></i>Send Emails To Companies</a></li>

        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Send Emails To Companies</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('email-send-to-companies-add-submit') ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">



                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">

                                        <?php
                                        $events=$this->db
                                        ->where('is_deleted' , 0)
                                        ->get('es_exhibitions')
                                        ->result();
                                        //print_r($events[0]->exhibition_title );die();
                                        ?>
                                        <label for="organizer_company">Select Event</label>
                                        <select class="form-control" name="event"
                                                id="event">
                                            <option value="">-Select-</option>
                                            <?php
                                            foreach ($events as $event) {
                                            ?>
                                        <option value="<?=$event->id ?>"><?=$event->exhibition_title ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="exhibit_companies">Exhibit Companies</label>
                                        <select class="form-control" id="exhibit_companies" name="exhibit_companies">
                                            <option>-Select-</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="">Select</option>
                                            <option value="Executive">Executive</option>
                                            <option value="Contact Person">Contact Person</option>
                                            <option value="Both">Both</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="select_all">
                                            <input name="select_all" id="select_all" type="checkbox" value="">
                                            Select All
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        &nbsp;
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Email Subject <span class="text-red">*</span></label>
								<input type="text" name="email_subject" class="form-control" id="email_subject">
                            </div>
                            <div class="form-group">
                                <label>Email Message <span class="text-red">*</span></label>
                                <textarea name="email_message" class="form-control" id="email_message" rows="8"></textarea>
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
</div><!-- /.content-wrapper -->

<?php $this->load->view('includes/after_login/footer'); ?>
<script src="<?= base_url('assets') ?>/ckeditor/ckeditor.js"></script>
<script>
	$(function () {

		var faqadd = {
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("email-send-to-companies-add-validate"); ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});

	var editor = CKEDITOR.replace('email_message');

    $(document).on('change', '#event', function (e) {
        e.stopImmediatePropagation();

        var selectedName = $('#event').val();
        $.ajax({
            type:    'post',
            url:     '<?= base_url("sms_notification/get_main_company_detail"); ?>',
            data:    {"id":selectedName},
            success: function (data) {
                /// data = JSON.parse(data);
                console.log(data);
                $('#exhibit_companies').html(data);
                //$('#exhibit_companies').html('');
            },
            error:   function () {
            }
        });


    });
</script>
</body>
</html>
