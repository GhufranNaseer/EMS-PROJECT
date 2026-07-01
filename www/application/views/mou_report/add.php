<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<div class="content-wrapper" data-page="mou_signing_request">
    <section class="content-header">
        <h1>
            MoU Signing
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?= base_url('mou_sign.html'); ?>">MoU List</a></li>
            <li class="active">Add MoU Signing</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Add MoU Signing Request (Manual)</h3>
                    </div>

                    <div class="box-body">
                        <form action="<?= base_url('mou_sign-submit.html') ?>" method="post" id="crd_form">
                            <div class="js-msgbox"></div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="exhibition_id">Exhibition *</label>
                                        <select class="form-control" name="exhibition_id" id="exhibition_id">
                                            <option value="">- select exhibition -</option>
                                            <?php foreach ($exhibitions as $exhibition): ?>
                                                <option value="<?= $exhibition->id; ?>"><?= html_escape($exhibition->exhibition_title); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                 <div class="col-sm-6">
                                     <div class="form-group">
                                         <label for="mou_sign_location_select">MoU Sign Location *</label>
                                         <select class="form-control" id="mou_sign_location_select" name="mou_sign_location">
                                             <option value="">- select exhibition first -</option>
                                         </select>
                                         <div id="custom_location_wrapper" style="display: none; margin-top: 10px;">
                                             <label for="mou_sign_location_custom">Custom MoU Sign Location *</label>
                                             <input type="text" class="form-control" id="mou_sign_location_custom" placeholder="e.g. Meeting Room A">
                                         </div>
                                     </div>
                                 </div>
                             </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="request_from_id">Request From (Exhibitor) *</label>
                                        <select class="form-control" name="request_from_id" id="request_from_id">
                                            <option value="">- select customer -</option>
                                            <?php foreach ($customers as $customer): ?>
                                                <option value="<?= $customer->id; ?>"><?= html_escape($customer->company . ' ('.$customer->name.')'); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="request_to_id">Request To (Exhibitor) *</label>
                                        <select class="form-control" name="request_to_id" id="request_to_id">
                                            <option value="">- select customer -</option>
                                            <?php foreach ($customers as $customer): ?>
                                                <option value="<?= $customer->id; ?>"><?= html_escape($customer->company . ' ('.$customer->name.')'); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="exhibition_day">Exhibition Day *</label>
                                        <select class="form-control" name="exhibition_day" id="exhibition_day">
                                            <option value="">- select day -</option>
                                            <option value="Day 1">Day 1</option>
                                            <option value="Day 2">Day 2</option>
                                            <option value="Day 3">Day 3</option>
                                            <option value="Day 4">Day 4</option>
                                            <option value="Day 5">Day 5</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="booking_date">Sign Date *</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" class="form-control datepicker" name="booking_date" id="booking_date" placeholder="YYYY-MM-DD">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="booking_time">Sign Time *</label>
                                        <div class="input-group bootstrap-timepicker timepicker">
                                            <div class="input-group-addon"><i class="fa fa-clock-o"></i></div>
                                            <input type="text" class="form-control timepicker" name="booking_time" id="booking_time" placeholder="HH:MM">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="commercial_value">Commercial Value *</label>
                                        <input type="text" class="form-control" name="commercial_value" id="commercial_value" placeholder="e.g. 50000 USD">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="description">Description *</label>
                                        <textarea class="form-control" name="description" id="description" rows="5" placeholder="MoU Signing description details..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-xs-12">
                                    <button type="button" class="btn btn-primary margin-bottom js-form_btn">Save MoU Signing</button>
                                    <a href="<?= base_url('mou_sign.html'); ?>" class="btn btn-default margin-bottom">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php $this->load->view('includes/after_login/footer'); ?>

<script src="<?= base_url('assets') ?>/datepicker/bootstrap-datepicker.js"></script>
<script src="<?= base_url('assets') ?>/timepicker/bootstrap-timepicker.min.js"></script>
<script src="<?= base_url('assets') ?>/js/doFormValidation.js"></script>

<script>
    $(function () {
        // Initialize Datepicker
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });

        // Initialize Timepicker
        $('.timepicker').timepicker({
            showMeridian: false,
            defaultTime: '12:00',
            minuteStep: 15
        });

        // Setup AJAX Form Validation
        doFormValidation({
            'form':         '#crd_form',
            'msgbox':       '#crd_form .js-msgbox',
            'btnClick':     '#crd_form .js-form_btn',
            'urlValidator': "<?php echo base_url("mou_sign-validate.html"); ?>",
            'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
        });

        // Dynamic location loading based on selected Exhibition
        $('#exhibition_id').change(function() {
            var exhibition_id = $(this).val();
            var select = $('#mou_sign_location_select');
            
            // Reset fields
            select.html('<option value="">- loading locations... -</option>').val('');
            $('#custom_location_wrapper').hide();
            $('#mou_sign_location_custom').removeAttr('name').val('');
            select.attr('name', 'mou_sign_location');

            if (!exhibition_id) {
                select.html('<option value="">- select exhibition first -</option>');
                return;
            }

            $.ajax({
                url: '<?= base_url("mou_sign-get-locations.html") ?>',
                type: 'POST',
                data: { exhibition_id: exhibition_id },
                dataType: 'json',
                success: function(locations) {
                    var options = '<option value="">- select location -</option>';
                    $.each(locations, function(i, loc) {
                        options += '<option value="' + htmlEntities(loc.location) + '">' + htmlEntities(loc.location) + '</option>';
                    });
                    options += '<option value="_custom_">** Other (Type Custom Location) **</option>';
                    select.html(options);
                },
                error: function() {
                    select.html('<option value="">- error loading locations -</option>');
                }
            });
        });

        // Handle custom location input toggling
        $('#mou_sign_location_select').change(function() {
            var val = $(this).val();
            if (val === '_custom_') {
                $('#custom_location_wrapper').show();
                $('#mou_sign_location_custom').attr('name', 'mou_sign_location').focus();
                $(this).removeAttr('name');
            } else {
                $('#custom_location_wrapper').hide();
                $('#mou_sign_location_custom').removeAttr('name').val('');
                $(this).attr('name', 'mou_sign_location');
            }
        });

        function htmlEntities(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    });
</script>
</body>
</html>
