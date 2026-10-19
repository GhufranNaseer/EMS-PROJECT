<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="officer">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            B2B Users
            <small>List</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('officer.html'); ?>"><i class="fa fa-dashboard"></i> B2B Users</a></li>
            <li class="active">List</li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">B2B Users List</h3>
                        <a href="javascript:void(0);" class="btn btn-success pull-right" data-toggle="modal" data-target="#import-officer-modal"><i class="fa fa-file-excel-o"></i> Import Bulk B2B Users</a>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <div class="row">
                            <!--<div class="col-xs-3">
                                <a href="<?/*= base_url('officer-add.html') */?>?id=<?/*= $this->input->get('id') */?>"
                                                     class="btn btn-primary btn-block margin-bottom">Add New</a>
                            </div>-->
                            <div class="col-xs-2">
                                <a href="<?= base_url('officer-add-foreign-delegations.html') ?>?id=<?= $this->input->get('id') ?>"
                                   class="btn btn-primary btn-block margin-bottom">Add Foreign Delegations</a>
                            </div>
                            <div class="col-xs-2">
                                <a href="<?= base_url('officer-add-local-delegates.html') ?>?id=<?= $this->input->get('id') ?>"
                                   class="btn btn-primary btn-block margin-bottom">Add Local Delegations</a>
                            </div>
							<div class="col-xs-2">
                                <a href="<?= base_url('officer-add-armed-force.html') ?>?id=<?= $this->input->get('id') ?>"
                                   class="btn btn-primary btn-block margin-bottom">Armed Forces (Pakistan)</a>
                            </div>
                            <div class="col-xs-2">
                                <a href="<?= base_url('officer-add-government-officials.html') ?>?id=<?= $this->input->get('id') ?>"
                                   class="btn btn-primary btn-block margin-bottom">Government Officials</a>
                            </div>
                            <div class="col-xs-2">
                                <a href="<?= base_url('officer-add-organizer.html') ?>?id=<?= $this->input->get('id') ?>"
                                   class="btn btn-primary btn-block margin-bottom">Organizers</a>
                            </div>
                            <div class="col-xs-2">
                                <a href="<?= base_url('officer-add-chief-of-servicing.html') ?>?id=<?= $this->input->get('id') ?>"
                                   class="btn btn-primary btn-block margin-bottom">Add Chief of Servicing</a>
                            </div>
                        </div>


                        <form action="" method="get">
                            <input type="hidden" name="id" value="<?= $this->input->get('id') ?>">
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label>Type</label>
                                    <select name="filter_type" class="form-control">
                                        <option value="">- select -</option>
                                        <option value="foreign_delegates" <?= (($this->input->get('filter_type') && $this->input->get('filter_type') == 'foreign_delegates') ? 'selected' : '') ?>>Foreign Delegates</option>
                                        <option value="local_delegates" <?= (($this->input->get('filter_type') && $this->input->get('filter_type') == 'local_delegates') ? 'selected' : '') ?>>Local Delegates</option>
                                        <option value="chief_of_servicing" <?= (($this->input->get('filter_type') && $this->input->get('filter_type') == 'chief_of_servicing') ? 'selected' : '') ?>>Gov. Chief of Servicing</option>
                                        <option value="armed_force" <?= (($this->input->get('filter_type') && $this->input->get('filter_type') == 'armed_force') ? 'selected' : '') ?>>Armed Forces (Pakistan)</option>
                                        <option value="government_officials" <?= (($this->input->get('filter_type') && $this->input->get('filter_type') == 'government_officials') ? 'selected' : '') ?>>Government Officials</option>
                                        <option value="organizer" <?= (($this->input->get('filter_type') && $this->input->get('filter_type') == 'organizer') ? 'selected' : '') ?>>Organizers</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label>Is Representative</label>
                                    <select name="filter_representative" class="form-control">
                                        <option value="">Both</option>
                                        <option value="self" <?= (($this->input->get('filter_representative') && $this->input->get('filter_representative') == 'self') ? 'selected' : '') ?>>Self</option>
                                        <option value="representative" <?= (($this->input->get('filter_representative') && $this->input->get('filter_representative') == 'representative') ? 'selected' : '') ?>>Representative</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
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
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-filter"></i> Filter</button>
                                </div>
                            </div>
                        </form>

                        <table width="100%" class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th class="text-center" width="5%">#</th>
                                <th>Type</th>
                                <th>Designation</th>
                                <th>Country</th>
                                <th>Profile</th>
                                <th>Contact person</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th class="text-center" width="11%">Action</th>
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
$current_raw_id = $this->input->get('id');
?>

<!-- Import Modal -->
<div class="modal fade" id="import-officer-modal" tabindex="-1" role="dialog" aria-labelledby="importOfficerModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="importOfficerModalLabel"><i class="fa fa-file-excel-o"></i> Import Bulk B2B Users via Excel</h4>
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
                                        <?php 
                                        $enc_id = myid($exhibition->id);
                                        $is_selected = ($current_raw_id === $enc_id || (string)$current_raw_id === (string)$exhibition->id) ? 'selected' : '';
                                        ?>
                                        <option value="<?= $exhibition->id; ?>" data-enc-id="<?= $enc_id; ?>" <?= $is_selected; ?>><?= html_escape($exhibition->exhibition_title); ?></option>
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
                            <div class="checkbox" style="margin-top: 5px;">
                                <label>
                                    <input type="checkbox" name="send_credentials" id="import_send_credentials"> <strong>Send login credentials email to imported users</strong>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="<?= base_url('officer-download-template.html'); ?><?= !empty($current_raw_id) ? '?exhibition_id=' . urlencode($current_raw_id) : ''; ?>" class="btn btn-link" id="download-template-link"><i class="fa fa-download"></i> Download Sample Excel Template</a>
                        </div>
                    </div>
                    
                    <hr>

                    <div id="import-msgbox"></div>

                    <!-- Verification Preview Area -->
                    <div id="import-preview-area" style="display: none; max-height: 380px; overflow-y: auto;">
                        <h4 style="margin-top: 0;">Validation Preview</h4>
                        <table class="table table-bordered table-striped" id="preview-table">
                            <thead>
                                <tr>
                                    <th>Row</th>
                                    <th>Type</th>
                                    <th>Contact Person</th>
                                    <th>Designation</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Country</th>
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
                <button type="button" class="btn btn-primary" id="btn-validate-upload"><i class="fa fa-check"></i> Validate File</button>
                <button type="button" class="btn btn-success" id="btn-confirm-import" style="display: none;"><i class="fa fa-cloud-upload"></i> Confirm & Import</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/after_login/footer'); ?>

<script type="text/javascript">
	$(document).ready(function () {
		var oTable = $('#crud-table').dataTable($.extend({}, (typeof datatable_settings !== 'undefined' ? datatable_settings : {}), {
			"bProcessing": true,
			"bServerSide": true,
			"sScrollX":    '100%',
			"sAjaxSource": '<?php echo base_url('officer-datatable.html'); ?>'+ location.search,
			"bJQueryUI":   true,
			"fnRowCallback":   function (nRow, aData, iDisplayIndex) {
				var oSettings = oTable.fnSettings();
				$("td:first", nRow).html(oSettings._iDisplayStart + iDisplayIndex + 1);
				return nRow;
			},
			"aaSorting":       [[0, 'desc']],
			"sPaginationType": "full_numbers",
			"iDisplayStart":   0,
			"oLanguage":       {
				"sProcessing": "<img src='<?php echo base_url(); ?>assets/images/ajax-loader_dark.gif'>"
			},
			"fnInitComplete":  function () {
				oTable.fnAdjustColumnSizing();
			},
			'fnServerData':    function (sSource, aoData, fnCallback) {
				$.ajax({
					'dataType': 'json',
					'type':     'POST',
					'url':      sSource,
					'data':     aoData,
					'success':  fnCallback
				});
			}
		}));

		if (typeof my_datatable === 'function') {
			my_datatable(oTable, {
				exportable: true,
				file_name: 'B2B Users List',
				export_type: ['excel'],
				event_id: '<?= $this->input->get('id') ?>',
				headers: [
					'S.no',
					'Type',
					'Designation',
					'Country',
					'Profile',
					'Contact person',
					'Phone',
					'Email',
					'Password'
				]
			});
		}

		var validatedRowsData = null;

		function resetValidationState() {
			validatedRowsData = null;
			$('#btn-confirm-import').hide();
			$('#btn-validate-upload').show().prop('disabled', false);
			$('#import-preview-area').hide();
			$('#preview-table tbody').html('');
			$('#import-msgbox').html('');
		}

		$('#import_file, #import_exhibition_id').on('change', function() {
			resetValidationState();
		});

		$('#download-template-link').click(function(e) {
			var exhibition_id = $('#import_exhibition_id').val();
			if (!exhibition_id) {
				e.preventDefault();
				alert('Please select an Exhibition first to download its template.');
				return false;
			}
		});

		$('#import_exhibition_id').on('change', function() {
			var exhibition_id = $(this).val();
			var baseUrl = '<?= base_url("officer-download-template.html"); ?>';
			if (exhibition_id) {
				$('#download-template-link').attr('href', baseUrl + '?exhibition_id=' + exhibition_id);
			} else {
				$('#download-template-link').attr('href', baseUrl);
			}
		});

		$('#import-officer-modal').on('hidden.bs.modal', function () {
			$('#bulk-import-form')[0].reset();
			resetValidationState();
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
				url: '<?= base_url("officer-import-validate.html") ?>',
				type: 'POST',
				data: formData,
				contentType: false,
				processData: false,
				dataType: 'json',
				success: function(response) {
					$('#btn-validate-upload').prop('disabled', false);
					if (response.status === 'success') {
						validatedRowsData = response.rows;
						var tbody = '';
						var validRowsCount = 0;
						
						$.each(response.rows, function(idx, item) {
							var statusBadge = '';
							var rowClass = '';
							if (item.status === 'valid') {
								statusBadge = '<span class="label label-success"><i class="fa fa-check"></i> Valid</span>';
								validRowsCount++;
							} else {
								rowClass = 'danger';
								statusBadge = '<span class="label label-danger" title="' + (item.errors ? item.errors.join(' | ') : '') + '"><i class="fa fa-times"></i> Error</span>';
								if (item.errors && item.errors.length > 0) {
									statusBadge += '<br><small class="text-danger">' + item.errors.join('<br>') + '</small>';
								}
							}

							tbody += '<tr class="' + rowClass + '">';
							tbody += '<td>Row ' + item.row_idx + '</td>';
							tbody += '<td>' + (item.type_label || item.officer_type) + '</td>';
							tbody += '<td>' + item.contact_person + '</td>';
							tbody += '<td>' + item.designation + '</td>';
							tbody += '<td>' + item.email + '</td>';
							tbody += '<td>' + (item.phone || '-') + '</td>';
							tbody += '<td>' + (item.country || '-') + '</td>';
							tbody += '<td>' + statusBadge + '</td>';
							tbody += '</tr>';
						});

						$('#preview-table tbody').html(tbody);
						$('#import-preview-area').show();

						if (response.has_errors) {
							$('#import-msgbox').html('<div class="alert alert-warning"><strong>Found errors in ' + response.error_count + ' record(s).</strong> ' + response.valid_count + ' valid record(s) can still be imported. Please review the table below.</div>');
						} else {
							$('#import-msgbox').html('<div class="alert alert-success"><strong>All ' + response.valid_count + ' records are valid!</strong> Click "Confirm & Import" to save them.</div>');
						}

						if (validRowsCount > 0) {
							$('#btn-confirm-import').show().html('<i class="fa fa-cloud-upload"></i> Confirm & Import (' + validRowsCount + ' Valid Users)');
						} else {
							$('#btn-confirm-import').hide();
						}
					} else {
						$('#import-msgbox').html('<div class="alert alert-danger">' + response.message + '</div>');
					}
				},
				error: function(xhr, status, error) {
					$('#btn-validate-upload').prop('disabled', false);
					$('#import-msgbox').html('<div class="alert alert-danger">An unexpected error occurred during validation: ' + (xhr.responseText || error) + '</div>');
				}
			});
		});

		// Click Confirm & Import
		$('#btn-confirm-import').click(function() {
			if (!validatedRowsData || validatedRowsData.length === 0) {
				alert('No validated records available to import.');
				return;
			}

			var exhibition_id = $('#import_exhibition_id').val();
			var send_credentials = $('#import_send_credentials').is(':checked') ? 1 : 0;

			$('#btn-confirm-import').prop('disabled', true).html('<i class="fa fa-spin fa-refresh"></i> Importing, please wait...');
			$('#btn-validate-upload').prop('disabled', true);

			$.ajax({
				url: '<?= base_url("officer-import-confirm.html") ?>',
				type: 'POST',
				data: {
					exhibition_id: exhibition_id,
					rows: JSON.stringify(validatedRowsData),
					send_credentials: send_credentials
				},
				dataType: 'json',
				success: function(res) {
					if (res.status === 'success') {
						alert(res.message || 'Users imported successfully!');
						$('#import-officer-modal').modal('hide');
						location.reload();
					} else {
						$('#btn-confirm-import').prop('disabled', false).html('<i class="fa fa-cloud-upload"></i> Confirm & Import');
						$('#btn-validate-upload').prop('disabled', false);
						$('#import-msgbox').html('<div class="alert alert-danger">' + res.message + '</div>');
					}
				},
				error: function(xhr, status, error) {
					$('#btn-confirm-import').prop('disabled', false).html('<i class="fa fa-cloud-upload"></i> Confirm & Import');
					$('#btn-validate-upload').prop('disabled', false);
					$('#import-msgbox').html('<div class="alert alert-danger">An unexpected error occurred while saving: ' + (xhr.responseText || error) + '</div>');
				}
			});
		});

	});
</script>

</body>
</html>
