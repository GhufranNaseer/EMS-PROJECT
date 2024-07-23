<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<?php

$event_days = $this->db
	->where('exhibition_id', $this->event->id)
	->get('es_exhibition_date')
	->result();

if ($this->input->get('type') == 'officer') {
    $book_to_data = $this->db
        ->where(mycolumn(), $this->input->get('id'))
        ->get('es_officer')
        ->row();
    $book_to_name = $book_to_data->officer_designation . ' ('.ucwords(str_replace('_', ' ', $book_to_data->officer_type)).')';
} else {
	$book_to_data = $this->db
		->where(mycolumn(), $this->input->get('id'))
		->get('es_customers')
		->row();

	$book_to_name = $book_to_data->company;
}


?>

<style>
    .date-box {
        width: 300px;
        padding: 10px;
        text-align: center;
        margin: 0 0 15px;
        background: <?= $this->event->event_color ?> ;
        color: #fff;
        border-radius: 8px;
    }

    .small-box {
        background: #eee;
        text-align: center;
        color: #333 !important;
        cursor: pointer;
        height: 68px;
    }
    .small-box.selected {
        background: #00a65a;
    }
    .small-box.booked {
        background: #dd4b39 !important;
        cursor: not-allowed !important;
    }


    .day-select {
        list-style: none;
        padding: 0px;
        background: #eeeeee;
        border-radius: 5px;
    }
    .day-select li {
        padding: 10px;
        font-size: 18px;
        cursor: pointer;
    }
    .day-select li.selected {
        background: #00a65a;
    }
    .day-select li.booked {
        background: #dd4b39 !important;
    }
</style>
<div class="content-wrapper" data-page="<?= (($this->input->get('type') == 'officer') ? ('meeting-officer-' . $book_to_data->officer_type) : 'meeting-exhibitors') ?>">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Appointment Schedule</h1>
        <ol class="breadcrumb">
            <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header ">
                        <h3 class="text-center">Appointment Schedule With <?= $book_to_name ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-body">
                        <div class="clearfix">
                            <h3 class="date-box pull-right"><?= date('d F Y', strtotime($event_days[0]->date)) ?></h3>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <h4>Location for Meeting</h4>
                                <select name="meeting_location" form="crd_form" class="form-control">
                                    <option value="">- None -</option>
                                    <?php 
                                    $locations = $this->db
                                        ->where('exhibition_id', $this->event->id)
                                        ->where('type', 'meeting_location')
                                        ->get('location_for_meeting')
                                        ->result();
                                    foreach($locations as $location){
                                    ?>    
                                    <option value="<?= $location->location ?>"><?= $location->location ?></option>
                                    <?php }
                                    ?>
                                </select>
                                <h4>Select Day</h4>
                                <ul class="day-select">
                                    <?php
                                    foreach ($event_days as $count => $event_day) {
                                        echo '<li data-day="'.($count+1).'" data-date="'. date('Y-m-d', strtotime($event_day->date)) .'" data-date_formated="'. date('d F Y', strtotime($event_day->date)) .'">Exhibition day '.($count+1).'</li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div class="col-md-9">
                                <h4>Select Time</h4>
                                <div class="row" id="available_times">

                                </div>

                                <div class="text-right">
                                    <form action="<?= base_url('meeting_schedule_submit.html') ?>" method="post" id="crd_form">
                                        <p class="bg-danger js-msgbox"></p>
                                        <input type="hidden" name="booking_day" class="booking_day">
                                        <input type="hidden" name="booking_date" class="booking_date">
                                        <input type="hidden" name="booking_time" class="booking_time">
                                        <input type="hidden" name="agenda_of_meeting" class="booking_agenda">
										<input type="hidden" name="discussion_points" class="booking_discussion_points">
                                        <input type="hidden" name="meeting_notes" class="booking_meeting_notes">
                                        <input type="hidden" name="booking_to" value="<?= $book_to_data->id ?>">
                                        <input type="hidden" name="user_type" value="<?= $this->input->get('type') ?>">
                                        <button type="button" class="btn btn-primary btn-lg add-agenda-btn">Proceed</button>
                                        <button type="button" class="btn btn-primary btn-lg js-form_btn" style="display: none;">Proceed</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
</div>


<!-- Modal -->
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
					<input type="text" class="form-control" id="agenda_modal_agenda" >
				</div>
				<div class="form-group">
					<label>Discussion Points: <span class="text-danger">*</span></label>
					<textarea class="form-control" id="agenda_modal_discussion_points" rows="6"></textarea>
				</div>
				<div class="form-group">
					<label>Notes: <span class="text-muted small">(optional)</span></label>
					<textarea class="form-control" id="agenda_modal_notes" rows="4"></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="agenda_modal_submit">Proceed</button>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('includes/after_login/footer'); ?>

<script>
	$(function () {
		doFormValidation({
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("meeting_schedule_validate.html"); ?>",
			'loadingImg':   "<?php echo base_url("../assets/img/load-indicator.gif"); ?>"
		});
	});

    $(document).on('click', '.small-box:not(.booked)', function (e) {
        e.stopImmediatePropagation();

        var time = $(this).attr('data-time');

        $('.small-box').removeClass('selected');
        $(this).addClass('selected');

        $('.booking_time').val(time);
    });

    $(document).on('click', '.day-select li', function (e) {
        e.stopImmediatePropagation();

        var date = $(this).attr('data-date');
        var date_formated = $(this).attr('data-date_formated');
        var day = $(this).attr('data-day');

        $('.day-select li').removeClass('selected');
        $(this).addClass('selected');

        $('.date-box').html(date_formated);

        $('.booking_date').val(date);
        $('.booking_day').val(day);

        $.ajax({
			type:    'post',
			url:     '<?= base_url('meeting/get_available_time') ?>',
			data:    {
				day: day,
				date: date,
				type: '<?= $this->input->get('type') ?>',
				id: '<?= $this->input->get('id') ?>'
            },
			success: function (data) {
				$('#available_times').html(data);

				$('.booking_time').val('');
			},
			error:   function () {
			}
		});
    });

    $(document).on('click', '.add-agenda-btn', function (e) {
        e.stopImmediatePropagation();

		$('#agenda_modal').modal({backdrop: 'static', keyboard: false, show: true}); // open lightbox
        
		// swal({
        //     title: "Insert agenda of meeting:",
        //     text: "",
        //     type: "input",
        //     showCancelButton: true,
        //     closeOnConfirm: false,
        //     inputPlaceholder: "Write something"
        // }, function (inputValue) {
        //     if (inputValue === false) return false;
        //     if (inputValue === "") {
        //         swal.showInputError("You need to write something!");
        //         return false
        //     }

        //     $('.booking_agenda').val(inputValue);
        //     $('.add-agenda-btn').hide();
        //     $('.js-form_btn').show().click();
        // });
    });

	$(document).on('click', '#agenda_modal_submit', function (e) {
		e.stopImmediatePropagation();

		$('#agenda_modal').modal('hide');

		let agenda = $('#agenda_modal_agenda').val();
		let discussion_points = $('#agenda_modal_discussion_points').val();
		let notes = $('#agenda_modal_notes').val();

		if (agenda == '') {
			swal('Agenda of meeting is required');
			return;
		}
		if (discussion_points == '') {
			swal('Discussion points is required');
			return;
		}

		$('.booking_agenda').val(agenda);
		$('.booking_discussion_points').val(discussion_points);
		$('.booking_meeting_notes').val(notes);

		$('#agenda_modal').modal('hide');
		$('.add-agenda-btn').hide();
		$('.js-form_btn').show().click();
	});

    $(document).on('ready', function () {
		$('.day-select li:first-child').click();
	});
</script>

</body>
</html>