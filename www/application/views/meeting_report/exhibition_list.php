<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>
<style>
    .col-srialno
    {
        width: 40px !important;
    }

</style>

<div class="content-wrapper" data-page="meeting_report">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Meeting List
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
                        <h3 class="box-title">Meeting List</h3>
                        <a href="javascript:void(0);" class="btn btn-success pull-right" data-toggle="modal" data-target="#import-meetings-modal"><i class="fa fa-file-excel-o"></i> Import Bulk Meetings</a>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <!-- Summary Metrics Dashboard -->
                        <div class="row" style="margin-bottom: 20px;">
                            <!-- Total Meetings Card -->
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-aqua">
                                    <span class="info-box-icon"><i class="fa fa-calendar-check-o"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Meetings</span>
                                        <span class="info-box-number" style="font-size: 24px;"><?= number_format(isset($summary_stats['total']) ? $summary_stats['total'] : 0); ?></span>
                                        <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                                        <span class="progress-description" style="font-size: 12px;">
                                            Approved: <strong><?= (int)($summary_stats['approved'] ?? 0); ?></strong> | Pending: <strong><?= (int)($summary_stats['pending'] ?? 0); ?></strong>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Approved Meetings Card -->
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-green">
                                    <span class="info-box-icon"><i class="fa fa-check-circle-o"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Approved Meetings</span>
                                        <span class="info-box-number" style="font-size: 24px;"><?= number_format(isset($summary_stats['approved']) ? $summary_stats['approved'] : 0); ?></span>
                                        <div class="progress">
                                            <?php 
                                            $total_m = (isset($summary_stats['total']) && $summary_stats['total'] > 0) ? $summary_stats['total'] : 1;
                                            $app_pct = round(((isset($summary_stats['approved']) ? $summary_stats['approved'] : 0) / $total_m) * 100);
                                            ?>
                                            <div class="progress-bar" style="width: <?= $app_pct; ?>%"></div>
                                        </div>
                                        <span class="progress-description" style="font-size: 12px;">
                                            <?= $app_pct; ?>% of total meetings approved
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Day-wise Breakdown Card -->
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-yellow">
                                    <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Day-wise Meetings</span>
                                        <div style="margin-top: 4px; max-height: 52px; overflow-y: auto;">
                                            <?php if (!empty($summary_stats['days_breakdown'])): ?>
                                                <?php foreach ($summary_stats['days_breakdown'] as $db_item): ?>
                                                    <?php 
                                                    $display_day = trim($db_item['exhibition_day']);
                                                    if (is_numeric($display_day)) {
                                                        $display_day = 'Day ' . $display_day;
                                                    }
                                                    ?>
                                                    <span class="badge bg-navy" style="margin-bottom: 3px; font-size: 11px; padding: 3px 6px;"><?= html_escape($display_day); ?>: <strong><?= $db_item['count']; ?></strong></span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="small" style="color: #fff;">No day records</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hall-wise Breakdown Card -->
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-purple">
                                    <span class="info-box-icon"><i class="fa fa-building-o"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Hall-wise Meetings</span>
                                        <div style="margin-top: 4px; max-height: 52px; overflow-y: auto;">
                                            <?php if (!empty($summary_stats['halls_breakdown'])): ?>
                                                <?php foreach ($summary_stats['halls_breakdown'] as $hb_item): ?>
                                                    <span class="badge bg-black" style="margin-bottom: 3px; font-size: 11px; padding: 3px 6px;"><?= html_escape($hb_item['hall_title']); ?>: <strong><?= $hb_item['count']; ?></strong></span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="small" style="color: #fff;">No hall records</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Form with Event Day & Hall Filters -->
                        <form action="" method="get">
                            <input type="hidden" name="id" value="<?= $this->input->get('id') ?>">
                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label>Status</label>
                                    <select name="filter_status" class="form-control">
                                        <option value="">- all status -</option>
                                        <option value="approved" <?= (($this->input->get('filter_status') && $this->input->get('filter_status') == 'approved') ? 'selected' : '') ?>>Approved</option>
                                        <option value="pending" <?= (($this->input->get('filter_status') && $this->input->get('filter_status') == 'pending') ? 'selected' : '') ?>>Pending</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label>User Type</label>
                                    <select name="filter_user_type" class="form-control">
                                        <option value="">- all user types -</option>
                                        <option value="exhibitor_exhibitor" <?= (($this->input->get('filter_user_type') && $this->input->get('filter_user_type') == 'exhibitor_exhibitor') ? 'selected' : '') ?>>Exhibitor To Exhibitor</option>
                                        <option value="exhibitor_others" <?= (($this->input->get('filter_user_type') && $this->input->get('filter_user_type') == 'exhibitor_others') ? 'selected' : '') ?>>Exhibitor To Others</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label>Event Day</label>
                                    <select name="filter_day" class="form-control">
                                        <option value="">- all days -</option>
                                         <?php if (!empty($event_days)): ?>
                                             <?php foreach ($event_days as $day_k => $day_item): ?>
                                                 <?php 
                                                 $val = is_array($day_item) ? $day_item['value'] : $day_k;
                                                 $label = is_array($day_item) ? $day_item['label'] : ($day_k . ' (' . $day_item . ')');
                                                 $cur_filter = $this->input->get('filter_day');
                                                 $selected_day = ($cur_filter && ($cur_filter === $val || (preg_replace('/[^0-9]/', '', $cur_filter) !== '' && preg_replace('/[^0-9]/', '', $cur_filter) === preg_replace('/[^0-9]/', '', $val)))) ? 'selected' : ''; 
                                                 ?>
                                                 <option value="<?= html_escape($val); ?>" <?= $selected_day; ?>><?= html_escape($label); ?></option>
                                             <?php endforeach; ?>
                                         <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label>Halls</label>
                                    <select name="filter_hall" class="form-control">
                                        <option value="">- all halls -</option>
										<?php
											foreach ($event_halls as $event_hall) {
												$selected = ($this->input->get('filter_hall') && $this->input->get('filter_hall') == $event_hall->hall_id) ? 'selected' : '';
												echo '<option value="'.$event_hall->hall_id.'" '.$selected.'>'.$event_hall->hall_title.'</option>';
											}
										?>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-filter"></i> Filter</button>
                                </div>
                            </div>
                        </form>

                        <table width="100%" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th class="text-center col-srialno"  >#</th>
                                <th>Meeting From</th>
                                <th>Meeting To</th>
                                <th>Exhibition Day</th>
                                <th>Meeting Date</th>
                                <th>Meeting Time</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                        </table>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>


<div class="modal fade" id="agenda_modal" tabindex="-1" role="dialog" aria-labelledby="agenda_modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Meeting Agenda</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label>Agenda of meeting: <span class="text-danger">*</span></label>
					<input type="text" class="form-control" id="agenda_modal_agenda" disabled>
				</div>
				<div class="form-group">
					<label>Discussion Points: <span class="text-danger">*</span></label>
					<textarea class="form-control" id="agenda_modal_discussion_points" rows="6" disabled></textarea>
				</div>
				<div class="form-group">
					<label>Notes: <span class="text-muted small">(optional)</span></label>
					<textarea class="form-control" id="agenda_modal_notes" rows="4" disabled></textarea>
				</div>
				<div class="form-group">
					<label><input type="checkbox" id="agenda_modal_is_conducted" disabled /> Is Conducted</label>
				</div>
				<div class="form-group">
					<label>Feedback:</label>
					<textarea class="form-control" id="agenda_modal_feedback" rows="4" disabled></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="import-meetings-modal" tabindex="-1" role="dialog" aria-labelledby="import-meetings-modal-label">
    <div class="modal-dialog modal-lg" role="document" style="width: 85%;">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="import-meetings-modal-label"><i class="fa fa-file-excel-o"></i> Bulk Import Manual Meeting Entries</h4>
            </div>
            <div class="modal-body">
                <div id="import-msgbox"></div>
                <form id="bulk-import-form" enctype="multipart/form-data">
                    <input type="hidden" name="exhibition_id" id="import_exhibition_id" value="<?= !empty($exhibition->id) ? (int)$exhibition->id : html_escape($this->input->get('id')); ?>">
                    
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <label>Upload Completed Excel File (.xlsx): <span class="text-danger">*</span></label>
                                <input type="file" name="import_file" id="import_file" class="form-control" accept=".xlsx">
                                <p class="help-block"><small>Only modern Excel files (<strong>.xlsx</strong>) supported. Max size: 5MB.</small></p>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="auto_approve" id="import_auto_approve" value="1" checked> 
                                    <strong>Auto-Approve Imported Meetings</strong> (Status set to 'Approved' directly)
                                </label>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="callout callout-info" style="margin-bottom: 0; padding: 12px;">
                                <h4><i class="fa fa-info-circle"></i> Instructions</h4>
                                <ol style="padding-left: 18px; margin-bottom: 10px; font-size: 13px;">
                                    <li>Download the pre-filled template containing Exhibitors & Hall schedules.</li>
                                    <li>Fill in <code>Meeting_Entries</code> tab.</li>
                                    <li>Upload and click <strong>Validate File</strong> to preview.</li>
                                </ol>
                                <a href="<?= base_url('meeting-download-template.html?id=' . $this->input->get('id')); ?>" id="download-template-link" class="btn btn-block btn-default btn-sm" style="font-weight: bold; color: #0073b7;">
                                    <i class="fa fa-download text-primary"></i> Download Excel Template
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Live Validation Preview Table -->
                    <div id="import-preview-area" style="display: none; margin-top: 15px;">
                        <hr style="margin: 10px 0 15px 0;">
                        <h4><i class="fa fa-list-alt"></i> Preview & Validation Results</h4>
                        <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                            <table class="table table-bordered table-striped table-condensed" id="preview-table" style="font-size: 12px;">
                                <thead>
                                    <tr class="bg-gray-active">
                                        <th style="width: 40px;">#</th>
                                        <th>Sender (From)</th>
                                        <th>Receiver (To)</th>
                                        <th>Day</th>
                                        <th>Date & Time</th>
                                        <th>Location / Hall</th>
                                        <th>Agenda & Details</th>
                                        <th style="width: 80px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-validate-upload"><i class="fa fa-check"></i> Validate File</button>
                <button type="button" class="btn btn-success" id="btn-confirm-import" style="display: none;"><i class="fa fa-cloud-upload"></i> Confirm & Import</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/after_login/footer'); ?>

<script type="text/javascript">

    $(document).ready(function () {
        oTable = $('#crud-table').dataTable($.extend(datatable_settings, {
            "sAjaxSource": '<?php echo base_url('meeting-report_status-datatable.html'); ?>' + location.search,
        }));
        my_datatable(oTable, {
            exportable: true,
            file_name: 'Meeting Report',
            export_type: ['excel'],
            event_id: '<?= $this->input->get('id') ?>',
			headers: [
				'S.no',
				'Meeting From',
				'Meeting To',
				'Exhibition Day',
				'Meeting Date',
				'Meeting Time',
				'Status',
				'Action',
				'Country',
				'Meeting Agenda',
				'Discussion Points',
				'Is Conducted',
				'Feedback',
			]
        });

        var validatedRowsData = null;

        function resetValidationState() {
            validatedRowsData = null;
            $('#btn-confirm-import').hide();
            $('#btn-validate-upload').show().prop('disabled', false);
            $('#import-preview-area').hide();
            $('#preview-table tbody').html('');
            $('#import-msgbox').html('');
        }

        $('#import_file').on('change', function() {
            resetValidationState();
        });

        $('#import-meetings-modal').on('hidden.bs.modal', function () {
            $('#bulk-import-form')[0].reset();
            resetValidationState();
        });

        // Click Validate File
        $('#btn-validate-upload').click(function() {
            var file = $('#import_file').val();
            if (!file) {
                alert('Please select an Excel (.xlsx) file.');
                return;
            }

            var formData = new FormData($('#bulk-import-form')[0]);

            $('#import-msgbox').html('<div class="alert alert-info"><i class="fa fa-spin fa-refresh"></i> Validating Excel records, please wait...</div>');
            $('#btn-validate-upload').prop('disabled', true);
            $('#import-preview-area').hide();
            $('#preview-table tbody').html('');
            $('#btn-confirm-import').hide();

            $.ajax({
                url: '<?= base_url("meeting-import-validate.html") ?>',
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
                            tbody += '<td>' + htmlEntities(row.appointment_from_email) + '<br><small class="text-muted">' + htmlEntities(row.appointment_from_name) + '</small></td>';
                            tbody += '<td>' + htmlEntities(row.appointment_to_email) + '<br><small class="text-muted">' + htmlEntities(row.appointment_to_name) + '</small></td>';
                            tbody += '<td>' + htmlEntities(row.exhibition_day) + '</td>';
                            tbody += '<td>' + htmlEntities(row.appointment_date) + '<br><small class="text-muted">' + htmlEntities(row.appointment_time) + '</small></td>';
                            tbody += '<td>' + htmlEntities(row.meeting_location) + '</td>';
                            tbody += '<td><strong>' + htmlEntities(row.agenda_of_meeting) + '</strong>' + (row.discussion_points ? '<br><small class="text-muted">' + htmlEntities(row.discussion_points) + '</small>' : '') + '</td>';
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
                            $('#import-msgbox').html('<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> We found validation errors in the sheet (' + response.total_rows + ' total rows, invalid found). Please fix the highlighted rows in Excel and upload again.</div>');
                        } else {
                            $('#import-msgbox').html('<div class="alert alert-success"><i class="fa fa-check-circle"></i> All ' + response.total_rows + ' rows are valid! You can now click Confirm & Import below.</div>');
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
            $('#import-msgbox').html('<div class="alert alert-info"><i class="fa fa-spin fa-refresh"></i> Importing records into database, please wait...</div>');

            $.ajax({
                url: '<?= base_url("meeting-import-confirm.html") ?>',
                type: 'POST',
                data: {
                    exhibition_id: exhibition_id,
                    auto_approve: auto_approve,
                    rows: JSON.stringify(validatedRowsData)
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#import-msgbox').html('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + htmlEntities(response.message) + ' Reloading page...</div>');
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

	function show_agenda(id) {
		$.ajax({
				type:    'post',
				url:     '<?= base_url('meeting-report_status-details.html') ?>',
				data:    {
					id: id,
				},
				success: function (data) {
					console.log(data)
					data = JSON.parse(data)

					if (data.error == 0) {
						
						$('#agenda_modal_agenda').val(data.data.agenda_of_meeting)
						$('#agenda_modal_discussion_points').val(data.data.discussion_points)
						$('#agenda_modal_notes').val(data.data.meeting_notes)
						$('#agenda_modal_feedback').val(data.data.appointment_feedback)
						$('#agenda_modal_is_conducted').prop('checked', (data.data.is_conducted == 1))

						$('#agenda_modal').modal('show')
					}
				},
				error:   function () {
					alert('Something went wrong!')
				}
			});
	}
</script>


</body>
</html>
