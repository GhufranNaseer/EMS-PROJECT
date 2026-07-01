<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>
<style>
    .col-srialno {
        width: 40px !important;
    }
</style>

<div class="content-wrapper" data-page="mou_signing_request">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            My MoU's Signing
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">List</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">My MoU's Signing</h3>
                        <a href="<?= base_url('mou_sign-add.html'); ?>" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add MoU Signing</a>
                        <a href="javascript:void(0);" class="btn btn-success pull-right" style="margin-right: 5px;" data-toggle="modal" data-target="#import-modal"><i class="fa fa-file-excel-o"></i> Import Bulk MoUs</a>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <form action="" method="get">
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label>Status</label>
                                    <select name="filter_status" class="form-control">
                                        <option value="">- select -</option>
                                        <option value="approved" <?= (($this->input->get('filter_status') && $this->input->get('filter_status') == 'approved') ? 'selected' : '') ?>>Approved</option>
                                        <option value="pending" <?= (($this->input->get('filter_status') && $this->input->get('filter_status') == 'pending') ? 'selected' : '') ?>>Pending</option>
                                        <option value="my_requests" <?= (($this->input->get('filter_status') && $this->input->get('filter_status') == 'my_requests') ? 'selected' : '') ?>>My Requests</option>
                                    </select>
                                </div>

                                <div class="col-sm-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                </div>
                            </div>
                        </form>

                        <table class="table table-bordered table-striped" id="crud-table">
                            <thead>
                                <tr class="">
                                    <th class="text-center col-srialno">#</th>
                                    <th>Appointment From</th>
                                    <th>Appointment To</th>
                                    <th>Exhibition</th>
                                    <th>Event Day</th>
                                    <th>Appointment Date</th>
                                    <th>Appointment Time</th>
                                    <th>Description</th>
                                    <th>Commercial Value</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

<?php
$exhibitions = $this->db->select('id, exhibition_title')->where('is_deleted', 0)->get('es_exhibitions')->result();
?>

<!-- Import Modal -->
<div class="modal fade" id="import-modal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="importModalLabel">Import Bulk MoUs via Excel</h4>
            </div>
            <div class="modal-body">
                <form id="bulk-import-form" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="import_exhibition_id">Select Exhibition *</label>
                                <select class="form-control" name="exhibition_id" id="import_exhibition_id" required>
                                    <option value="">- select exhibition -</option>
                                    <?php foreach ($exhibitions as $exhibition): ?>
                                        <option value="<?= $exhibition->id; ?>"><?= html_escape($exhibition->exhibition_title); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="import_file">Excel File (.xlsx) *</label>
                                <input type="file" class="form-control" name="import_file" id="import_file" accept=".xlsx" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="auto_approve" id="import_auto_approve" checked> <strong>Auto-Approve imported records</strong>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="<?= base_url('mou_sign-download-template.html'); ?>" class="btn btn-link"><i class="fa fa-download"></i> Download Sample Excel Template</a>
                        </div>
                    </div>
                    
                    <hr>

                    <div id="import-msgbox"></div>

                    <!-- Verification Preview Area -->
                    <div id="import-preview-area" style="display: none; max-height: 400px; overflow-y: auto;">
                        <h4 style="margin-top: 0;">Validation Preview</h4>
                        <table class="table table-bordered table-striped" id="preview-table">
                            <thead>
                                <tr>
                                    <th>Row</th>
                                    <th>Day</th>
                                    <th>Date/Time</th>
                                    <th>Request From (Sender)</th>
                                    <th>Request To (Receiver)</th>
                                    <th>Location</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-validate-upload">Validate File</button>
                <button type="button" class="btn btn-success" id="btn-confirm-import" style="display: none;">Confirm & Import</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/after_login/footer'); ?>

<script type="text/javascript">
    $(document).ready(function() {
        oTable = $('#crud-table').dataTable($.extend(datatable_settings, {
            "sAjaxSource": '<?php echo base_url('mou_sign-datatable.html'); ?>' + location.search,
        }));
        my_datatable(oTable, {
            exportable: true,
            file_name: 'Mou List',
            export_type: ['excel', 'pdf'],
            event_id: '<?= (isset($this->event) && !is_null($this->event)) ? myid($this->event->id) : "" ?>',
        });

        var validatedRowsData = null;

        // Reset modal on close
        $('#import-modal').on('hidden.bs.modal', function () {
            $('#bulk-import-form')[0].reset();
            $('#import-msgbox').html('');
            $('#import-preview-area').hide();
            $('#preview-table tbody').html('');
            $('#btn-validate-upload').show().prop('disabled', false);
            $('#btn-confirm-import').hide().prop('disabled', false);
            validatedRowsData = null;
        });

        // Click Validate File
        $('#btn-validate-upload').click(function() {
            var exhibition_id = $('#import_exhibition_id').val();
            var file = $('#import_file').val();

            if (!exhibition_id) {
                alert('Please select an exhibition.');
                return;
            }
            if (!file) {
                alert('Please select an Excel file.');
                return;
            }

            var formData = new FormData($('#bulk-import-form')[0]);
            
            $('#import-msgbox').html('<div class="alert alert-info"><i class="fa fa-spin fa-refresh"></i> Validating Excel records, please wait...</div>');
            $('#btn-validate-upload').prop('disabled', true);
            $('#import-preview-area').hide();
            $('#preview-table tbody').html('');
            $('#btn-confirm-import').hide();

            $.ajax({
                url: '<?= base_url("mou_sign-import-validate.html") ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $('#btn-validate-upload').prop('disabled', false);
                    
                    if (response.status === 'success') {
                        $('#import-msgbox').html('');
                        validatedRowsData = response.rows;

                        var tbody = '';
                        var hasErrors = response.has_errors;

                        $.each(response.rows, function(i, row) {
                            var statusCell = '';
                            var rowClass = '';
                            
                            if (row.status === 'valid') {
                                statusCell = '<span class="label label-success"><i class="fa fa-check"></i> Valid</span>';
                            } else {
                                statusCell = '<span class="label label-danger" data-toggle="tooltip" title="' + row.errors.join(' | ') + '"><i class="fa fa-warning"></i> Error</span>';
                                rowClass = 'danger';
                            }

                            tbody += '<tr class="' + rowClass + '">';
                            tbody += '<td>' + row.excel_row_num + '</td>';
                            tbody += '<td>' + htmlEntities(row.exhibition_day) + '</td>';
                            tbody += '<td>' + htmlEntities(row.booking_date) + ' ' + htmlEntities(row.booking_time) + '</td>';
                            tbody += '<td>' + htmlEntities(row.request_from_email) + '<br><small class="text-muted">' + htmlEntities(row.request_from_name) + '</small></td>';
                            tbody += '<td>' + htmlEntities(row.request_to_email) + '<br><small class="text-muted">' + htmlEntities(row.request_to_name) + '</small></td>';
                            tbody += '<td>' + htmlEntities(row.mou_sign_location) + '</td>';
                            tbody += '<td>' + htmlEntities(row.commercial_value) + '</td>';
                            tbody += '<td>' + statusCell + '</td>';
                            tbody += '</tr>';

                            if (row.status !== 'valid') {
                                tbody += '<tr class="danger"><td colspan="8" style="padding-top: 0; padding-bottom: 8px;"><div class="text-danger" style="font-size: 11px; margin-left: 15px;"><strong>Errors:</strong><br>' + row.errors.join('<br>') + '</div></td></tr>';
                            }
                        });

                        $('#preview-table tbody').html(tbody);
                        $('#import-preview-area').show();
                        $('[data-toggle="tooltip"]').tooltip();

                        if (hasErrors) {
                            $('#import-msgbox').html('<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> We found validation errors in the sheet. Please fix the highlighted rows and upload again.</div>');
                        } else {
                            $('#import-msgbox').html('<div class="alert alert-success"><i class="fa fa-check-circle"></i> All rows are valid! You can now import the records.</div>');
                            $('#btn-validate-upload').hide();
                            $('#btn-confirm-import').show();
                        }
                    } else {
                        $('#import-msgbox').html('<div class="alert alert-danger"><i class="fa fa-times-circle"></i> ' + htmlEntities(response.message) + '</div>');
                    }
                },
                error: function() {
                    $('#btn-validate-upload').prop('disabled', false);
                    $('#import-msgbox').html('<div class="alert alert-danger"><i class="fa fa-times-circle"></i> Server communication error. Please try again.</div>');
                }
            });
        });

        // Click Confirm & Import
        $('#btn-confirm-import').click(function() {
            var exhibition_id = $('#import_exhibition_id').val();
            var auto_approve = $('#import_auto_approve').is(':checked') ? 1 : 0;

            if (!validatedRowsData || validatedRowsData.length === 0) {
                alert('No validated data to import.');
                return;
            }

            $('#btn-confirm-import').prop('disabled', true);
            $('#import-msgbox').html('<div class="alert alert-info"><i class="fa fa-spin fa-refresh"></i> Importing records, please wait...</div>');

            $.ajax({
                url: '<?= base_url("mou_sign-import-confirm.html") ?>',
                type: 'POST',
                data: {
                    exhibition_id: exhibition_id,
                    auto_approve: auto_approve,
                    rows: JSON.stringify(validatedRowsData)
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#import-msgbox').html('<div class="alert alert-success"><i class="fa fa-check-circle"></i> Import successful! Reloading page...</div>');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        $('#btn-confirm-import').prop('disabled', false);
                        $('#import-msgbox').html('<div class="alert alert-danger"><i class="fa fa-times-circle"></i> ' + htmlEntities(response.message) + '</div>');
                    }
                },
                error: function() {
                    $('#btn-confirm-import').prop('disabled', false);
                    $('#import-msgbox').html('<div class="alert alert-danger"><i class="fa fa-times-circle"></i> Server communication error during insertion.</div>');
                }
            });
        });

        function htmlEntities(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    });
</script>


</body>

</html>