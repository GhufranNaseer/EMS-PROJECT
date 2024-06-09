<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>
<style>
    .col-srialno
    {
        width: 40px !important;
    }

</style>

<div class="content-wrapper" data-page="my_meeting">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            My Meetings
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
                        <h3 class="box-title">My Meetings</h3>
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
                                <div class="col-sm-3">
                                    <label>Event Day</label>
                                    <select name="filter_day" class="form-control">
                                        <option value="">- select -</option>
                                        <?php
										$event_days = $this->db
											->where('exhibition_id', $this->event->id)
											->get('es_exhibition_date')
											->result();
										foreach ($event_days as $count => $event_day) {
										    $day_selected = ($this->input->get('filter_day') && $this->input->get('filter_day') == ($count+1)) ? 'selected' : '';
										    echo '<option value="'.($count+1).'" '.$day_selected.'>Event day '.($count+1).'</option>';
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

                        <table class="table table-bordered table-striped" id="crud-table">
                            <thead>
                            <tr class="">
                                <th class="text-center col-srialno" >#</th>
                                <th>Appointment From</th>
                                <th>Appointment To</th>
                                <th>Event Day</th>
                                <th>Appointment Date</th>
                                <th>Appointment Time</th>
                                <th>Status</th>
                                <th>Agenda</th>
                                <th>Conducted</th>
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
			"sAjaxSource": '<?php echo base_url('my_meeting-datatable.html'); ?>' + location.search,
		}));
		my_datatable(oTable, {
			exportable: true,
			file_name: 'Meeting List',
			export_type: ['excel', 'pdf'],
			event_id: '<?= myid($this->event->id) ?>',
		})
	});

	function confirm_conducted(id) {
		swal({
			title: "Please add feedback about meeting:",
			text: "",
			type: "input",
			showCancelButton: true,
			closeOnConfirm: true,
			inputPlaceholder: "Write something"
		}, function (inputValue) {
			if (inputValue === false) return false;
			if (inputValue === "") {
				swal.showInputError("You need to write something!");
				return false
			}

			$.ajax({
				type:    'post',
				url:     '<?= base_url('meeting_report/meeting_conducted_ajax') ?>',
				data:    {
					id: id,
					feedback: inputValue,
				},
				success: function (data) {
					console.log(data)
					data = JSON.parse(data)

					if (data.error == 0) {
						oTable._fnAjaxUpdate()
					} else {
						swal.showInputError(data.message);
					}
				},
				error:   function () {
					alert('Something went wrong!')
					oTable._fnAjaxUpdate()
				}
			});
			
		});
	}

	function show_agenda(id) {
		$.ajax({
				type:    'post',
				url:     '<?= base_url('meeting_report/get_meeting_details_ajax') ?>',
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
