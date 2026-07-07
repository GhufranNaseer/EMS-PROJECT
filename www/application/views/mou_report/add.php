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
                                             <option value="">- select exhibition first -</option>
                                         </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                         <label for="request_to_id">Request To (Exhibitor) *</label>
                                         <select class="form-control" name="request_to_id" id="request_to_id">
                                             <option value="">- select exhibition first -</option>
                                         </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="exhibition_day">Exhibition Day *</label>
                                        <select class="form-control" name="exhibition_day" id="exhibition_day">
                                            <option value="">- select exhibition first -</option>
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
                                         <label for="commercial_amount">Commercial Value *</label>
                                         <div class="row">
                                             <div class="col-xs-8" style="padding-right: 5px;">
                                                 <input type="number" min="0" class="form-control" id="commercial_amount" placeholder="Amount (e.g. 50000)" required>
                                             </div>
                                             <div class="col-xs-4" style="padding-left: 5px;">
                                                 <select class="form-control" id="commercial_currency">
                                                     <option value="USD">USD</option>
                                                     <option value="PKR">PKR</option>
                                                 </select>
                                             </div>
                                         </div>
                                         <input type="hidden" name="commercial_value" id="commercial_value">
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
            var daySelect = $('#exhibition_day');
            var fromCustomer = $('#request_from_id');
            var toCustomer = $('#request_to_id');
            
            // Reset fields
            select.html('<option value="">- loading locations... -</option>').val('');
            daySelect.html('<option value="">- loading days... -</option>').val('');
            fromCustomer.html('<option value="">- loading exhibitors... -</option>').val('');
            toCustomer.html('<option value="">- loading exhibitors... -</option>').val('');
            $('#booking_date').val('').prop('readonly', false);
            $('#custom_location_wrapper').hide();
            $('#mou_sign_location_custom').removeAttr('name').val('');
            select.attr('name', 'mou_sign_location');

            if (!exhibition_id) {
                select.html('<option value="">- select exhibition first -</option>');
                daySelect.html('<option value="">- select day -</option>');
                fromCustomer.html('<option value="">- select exhibition first -</option>');
                toCustomer.html('<option value="">- select exhibition first -</option>');
                return;
            }

            $.ajax({
                url: '<?= base_url("mou_sign-get-locations.html") ?>',
                type: 'POST',
                data: { exhibition_id: exhibition_id },
                dataType: 'json',
                success: function(response) {
                    // Populate locations
                    var options = '<option value="">- select location -</option>';
                    $.each(response.locations, function(i, loc) {
                        options += '<option value="' + htmlEntities(loc.location) + '">' + htmlEntities(loc.location) + '</option>';
                    });
                    options += '<option value="_custom_">** Other (Type Custom Location) **</option>';
                    select.html(options);

                    // Populate days dynamically
                    var dayOptions = '<option value="">- select day -</option>';
                    $.each(response.dates, function(i, date) {
                        var dayVal = 'Day ' + (i + 1);
                        dayOptions += '<option value="' + dayVal + '" data-date="' + date + '">' + dayVal + ' (' + date + ')</option>';
                    });
                    daySelect.html(dayOptions);

                    // Populate exhibitors dynamically
                    var exhibitorOptions = '<option value="">- select customer -</option>';
                    $.each(response.exhibitors, function(i, cust) {
                        exhibitorOptions += '<option value="' + cust.id + '">' + htmlEntities(cust.company) + ' (' + htmlEntities(cust.name) + ')</option>';
                    });
                    fromCustomer.html(exhibitorOptions);
                    toCustomer.html(exhibitorOptions);
                },
                error: function() {
                    select.html('<option value="">- error loading locations -</option>');
                    daySelect.html('<option value="">- error loading days -</option>');
                    fromCustomer.html('<option value="">- error loading exhibitors -</option>');
                    toCustomer.html('<option value="">- error loading exhibitors -</option>');
                }
            });
        });

        // Auto-fill and lock date when Exhibition Day is selected
        $('#exhibition_day').change(function() {
            var selectedOption = $(this).find('option:selected');
            var date = selectedOption.attr('data-date');
            if (date) {
                $('#booking_date').val(date).prop('readonly', true);
            } else {
                $('#booking_date').val('').prop('readonly', false);
            }
        });

        // Handle commercial value amount & currency concatenation
        function updateCommercialValue() {
            var amount = $('#commercial_amount').val().trim();
            var currency = $('#commercial_currency').val();
            if (amount !== '') {
                $('#commercial_value').val(amount + ' ' + currency);
            } else {
                $('#commercial_value').val('');
            }
        }
        $('#commercial_amount, #commercial_currency').on('input change', function() {
            updateCommercialValue();
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
