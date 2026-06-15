<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<div class="content-wrapper" data-page="email_logs">
    <section class="content-header">
        <h1>
            Email Logs
            <small>History</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li>Settings</li>
            <li class="active">Email Logs</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Email Logs List</h3>
                        <a href="<?= base_url('email-configuration'); ?>" class="btn btn-default pull-right"><i class="fa fa-envelope"></i> Email Configuration</a>
                    </div>
                    
                    <div class="box-body">
                        <table width="100%" id="email-logs-table" class="table table-striped table-hover table-bordered">
                            <thead>
                            <tr>
                                <th class="text-center" width="5%">#</th>
                                <th width="20%">Recipient</th>
                                <th width="25%">Subject</th>
                                <th width="15%">Email Type</th>
                                <th width="8%">Driver</th>
                                <th class="text-center" width="10%">Status</th>
                                <th class="text-center" width="7%">Attempts</th>
                                <th width="15%">Date & Time</th>
                                <th class="text-center" width="10%">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal for details -->
<div class="modal fade" id="email-log-modal" tabindex="-1" role="dialog" aria-labelledby="emailLogModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="emailLogModalLabel">Email Log Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="20%">Recipient</th>
                        <td id="modal-recipient"></td>
                        <th width="20%">Date & Time</th>
                        <td id="modal-created"></td>
                    </tr>
                    <tr>
                        <th>Subject</th>
                        <td id="modal-subject" colspan="3"></td>
                    </tr>
                    <tr>
                        <th>Email Type</th>
                        <td id="modal-type"></td>
                        <th>Driver</th>
                        <td id="modal-driver"></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td id="modal-status"></td>
                        <th>Attempts</th>
                        <td id="modal-attempts"></td>
                    </tr>
                </table>
                
                <div id="modal-error-section" style="display:none; margin-top: 15px;">
                    <h5><strong>Error Message</strong></h5>
                    <pre class="bg-danger text-danger" id="modal-error" style="white-space: pre-wrap; word-wrap: break-word; max-height: 150px; overflow-y: auto; padding: 10px; border: 1px solid #ebccd1; border-radius: 4px;"></pre>
                </div>
                
                <div id="modal-debug-section" style="display:none; margin-top: 15px;">
                    <h5><strong>Debug / SMTP Log</strong></h5>
                    <pre class="bg-warning text-warning" id="modal-debug" style="white-space: pre-wrap; word-wrap: break-word; max-height: 250px; overflow-y: auto; padding: 10px; border: 1px solid #faebcc; border-radius: 4px; font-family: monospace;"></pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/after_login/footer'); ?>

<script type="text/javascript">
    $(document).ready(function () {
        var oTable = $('#email-logs-table').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sScrollX": '100%',
            "sAjaxSource": '<?php echo base_url('email-logs-datatable.html'); ?>',
            "bJQueryUI": true,
            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                $("td:first", nRow).html(oSettings._iDisplayStart + iDisplayIndex + 1);
                return nRow;
            },
            "aaSorting": [[0, 'desc']],
            "sPaginationType": "full_numbers",
            "iDisplayLength": 20,
            "oLanguage": {
                "sProcessing": "<img src='<?php echo base_url(); ?>assets/images/ajax-loader_dark.gif'>"
            },
            "fnInitComplete": function () {
                oTable.fnAdjustColumnSizing();
            },
            'fnServerData': function (sSource, aoData, fnCallback) {
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            },
            "aoColumns": [
                { "mData": "id", "sClass": "text-center" },
                { "mData": "recipient_email" },
                { "mData": "subject" },
                { "mData": "email_type" },
                { "mData": "driver" },
                { "mData": "col_status", "sClass": "text-center", "bSortable": false, "bSearchable": false },
                { "mData": "attempts", "sClass": "text-center" },
                { "mData": "created_on" },
                { "mData": "col_action", "sClass": "text-center", "bSortable": false, "bSearchable": false }
            ]
        });

        // View Details handler
        $(document).on('click', '.js-view-log-details', function () {
            var btn = $(this);
            
            // Set text fields
            $('#modal-recipient').text(btn.data('recipient'));
            $('#modal-subject').text(btn.data('subject'));
            $('#modal-type').text(btn.data('type') || 'N/A');
            $('#modal-driver').text(btn.data('driver').toUpperCase());
            $('#modal-attempts').text(btn.data('attempts'));
            $('#modal-created').text(btn.data('created'));
            
            // Set status label
            var status = btn.data('status');
            var badge = 'default';
            if (status === 'sent') badge = 'success';
            else if (status === 'failed') badge = 'danger';
            else if (status === 'queued') badge = 'warning';
            else if (status === 'retry') badge = 'primary';
            else if (status === 'test') badge = 'info';
            
            $('#modal-status').html('<span class="label label-' + badge + '">' + status.toUpperCase() + '</span>');
            
            // Decode messages
            var errorMsg = '';
            var debugMsg = '';
            
            try {
                var rawError = btn.attr('data-error');
                if (rawError) {
                    errorMsg = atob(rawError);
                }
            } catch (e) {
                console.error("Error decoding error msg:", e);
                errorMsg = "Error decoding message";
            }
            
            try {
                var rawDebug = btn.attr('data-debug');
                if (rawDebug) {
                    debugMsg = atob(rawDebug);
                }
            } catch (e) {
                console.error("Error decoding debug msg:", e);
                debugMsg = "Error decoding message";
            }
            
            // Error block
            if (errorMsg) {
                $('#modal-error').text(errorMsg);
                $('#modal-error-section').show();
            } else {
                $('#modal-error-section').hide();
            }
            
            // Debug block
            if (debugMsg) {
                $('#modal-debug').text(debugMsg);
                $('#modal-debug-section').show();
            } else {
                $('#modal-debug-section').hide();
            }
            
            // Show modal
            $('#email-log-modal').modal('show');
        });
    });
</script>
</body>
</html>
