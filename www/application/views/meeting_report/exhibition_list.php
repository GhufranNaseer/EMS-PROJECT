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
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        <form action="" method="get">
                            <input type="hidden" name="id" value="<?= $this->input->get('id') ?>">
                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <label>Status</label>
                                    <select name="filter_status" class="form-control">
                                        <option value="">- select -</option>
                                        <option value="approved" <?= (($this->input->get('filter_status') && $this->input->get('filter_status') == 'approved') ? 'selected' : '') ?>>Approved</option>
                                        <option value="pending" <?= (($this->input->get('filter_status') && $this->input->get('filter_status') == 'pending') ? 'selected' : '') ?>>Pending</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label>User Type</label>
                                    <select name="filter_user_type" class="form-control">
                                        <option value="">- select -</option>
                                        <option value="exhibitor_exhibitor" <?= (($this->input->get('filter_user_type') && $this->input->get('filter_user_type') == 'exhibitor_exhibitor') ? 'selected' : '') ?>>Exhibitor To Exhibitor</option>
                                        <option value="exhibitor_others" <?= (($this->input->get('filter_user_type') && $this->input->get('filter_user_type') == 'exhibitor_others') ? 'selected' : '') ?>>Exhibitor To Others</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label>Halls</label>
                                    <select name="filter_hall" class="form-control">
                                        <option value="">- select -</option>
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
                                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
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
        })
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
