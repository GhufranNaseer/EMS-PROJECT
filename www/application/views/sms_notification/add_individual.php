<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="sms_individual_send">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Individual Send SMS
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('sms-individual-send-add-submit'); ?>"><i class="fa fa-dashboard"></i>Individual Send SMS</a></li>

        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Individual Send SMS</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('sms-individual-send-add-submit') ?>" method="post" id="crd_form"
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
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="exhibit_companies">Mobile Number</label>
                                        <input type="number" class="form-control" id="phone_number" name="phone_number">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Text Message <span class="text-red">*</span></label>
                                <textarea name="text_message" class="form-control" id="text_message" rows="3"></textarea>
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

<script>
	$(function () {

		var faqadd = {
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("sms-individual-send-add-validate"); ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);
	});

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
