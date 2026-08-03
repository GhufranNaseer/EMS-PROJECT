<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="promotional_email_campaign">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Promotional Email Campaign (All Past Exhibitors)
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('promotional-email-campaign'); ?>"><i class="fa fa-dashboard"></i>Promotional Email Campaign</a></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Promotional Email Campaign (All Past Exhibitors)</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('promotional-email-campaign-submit') ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">

                            <div class="well well-sm" style="background-color: #f5f5f5; border-left: 4px solid #00a65a; margin-bottom: 20px;">
                                <label style="font-weight: bold; font-size: 14px; color: #333; margin-bottom: 5px; display: block;">
                                    🚀 Target Recipients: <span style="color: #00a65a;">ALL Past Exhibitors Across ALL Events</span>
                                </label>
                                <p style="margin-bottom: 0; color: #666; font-size: 13px;">
                                    This promotional campaign will be queued and automatically sent to 
                                    <span class="label label-success" id="past_exhibitor_count_badge" style="font-size: 12px;">Loading Count...</span>
                                    unique exhibitor companies across all past events.
                                </p>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <?php
                                        $events = $this->db
                                            ->where('is_deleted', 0)
                                            ->order_by('id', 'DESC')
                                            ->get('es_exhibitions')
                                            ->result();
                                        ?>
                                        <label for="event">Select Target Event to Promote <span class="text-red">*</span></label>
                                        <select class="form-control" name="event" id="event">
                                            <option value="">-Select Event-</option>
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
                                        <label for="status">Recipient Type <span class="text-red">*</span></label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="Both">Both (Executive & Contact Person)</option>
                                            <option value="Executive">Executive Only</option>
                                            <option value="Contact Person">Contact Person Only</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info" id="placeholder_guide_box" style="margin-top: 10px; margin-bottom: 20px;">
                                <strong><i class="fa fa-info-circle"></i> Dynamic Placeholders Available:</strong>
                                You can use the following dynamic tags in your Subject & Message:
                                <ul style="margin-bottom: 0; padding-left: 20px;">
                                    <li><code>{COMPANY_NAME}</code> &mdash; Automatically replaced with Exhibitor's Company Name.</li>
                                    <li><code>{EVENT_NAME}</code> &mdash; Automatically replaced with the Selected Target Event Title.</li>
                                    <li><code>{PORTAL_URL}</code> &mdash; Automatically replaced with the B2B Meeting Portal Link.</li>
                                </ul>
                            </div>

                            <div class="form-group">
                                <label>Email Subject <span class="text-red">*</span></label>
								<input type="text" name="email_subject" class="form-control" id="email_subject" placeholder="e.g. Exclusive Invitation: Stall Bookings Now Open for {EVENT_NAME}!">
                            </div>
                            <div class="form-group">
                                <label>Email Message <span class="text-red">*</span></label>
                                <textarea name="email_message" class="form-control" id="email_message" rows="8"></textarea>
                            </div>

                            <p class="bg-danger js-msgbox"></p>

                            <div class="row">
                                <div class="col-xs-9"></div>
                                <div class="col-xs-3">
                                    <a href="javascript:void(0);"
                                       class="btn btn-primary btn-block margin-bottom js-form_btn"><i class="fa fa-paper-plane"></i> Queue & Send Campaign</a>
                                </div>
                            </div>

                        </form>

                    </div><!-- /.box-body -->
                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<?php $this->load->view('includes/after_login/footer'); ?>
<script src="<?= base_url('assets') ?>/ckeditor/ckeditor.js"></script>
<script>
	$(function () {
		var faqadd = {
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("promotional-email-campaign-validate"); ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		};
		doFormValidation(faqadd);

        // Fetch All Past Exhibitors Count via AJAX
        $.ajax({
            type: 'post',
            url: '<?= base_url("sms_notification/get_all_past_exhibitors_count"); ?>',
            dataType: 'json',
            success: function (res) {
                if (res && !res.error) {
                    $('#past_exhibitor_count_badge').text(res.total + ' Unique Companies');
                }
            }
        });
	});

	var editor = CKEDITOR.replace('email_message');
</script>
</body>
</html>
