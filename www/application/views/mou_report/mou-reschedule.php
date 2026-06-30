<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>

<?php

$meeting = $this->db
    ->where(mycolumn(), $this->input->get('id'))
    ->get('es_exhibition_mou_sign')
    ->row();

$event_days = $this->db
	->where('exhibition_id', $meeting->exhibition_id)
	->get('es_exhibition_date')
	->result();

$book_to_data = $this->db
    ->where('id', $meeting->request_from_id)
    ->get('es_customers')
    ->row();

$book_to_name = $book_to_data->company;

?>

<style>
    .date-box {
        width: 300px;
        padding: 10px;
        text-align: center;
        margin: 0 0 15px;
        background: <?= (isset($this->event) && !is_null($this->event) && isset($this->event->event_color) && $this->event->event_color != '') ? $this->event->event_color : '#3c8dbc' ?> ;
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
<div class="content-wrapper" data-page="my_meeting">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Mou Signing Re-Schedule</h1>
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
                        <h3 class="text-center">Mou Signing Re-Schedule With <?= $book_to_name ?></h3>
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
                                <select name="mou_sign_location" form="crd_form" class="form-control">
                                    <option value="">- None -</option>
                                    <?php 
                                    $locations = $this->db
                                        ->where('exhibition_id', $meeting->exhibition_id)
                                        ->where('type', 'mou_location')
                                        ->get('location_for_meeting')
                                        ->result();
                                    foreach($locations as $location){
                                    ?>    
                                    <option value="<?= $location->location ?>" <?= ($meeting->mou_sign_location == $location->location) ? 'selected' : ''; ?>><?= $location->location ?></option>
                                    <?php }
                                    ?>
                                </select>
                                <h4>Select Day</h4>
                                <ul class="day-select">
                                    <?php
                                    foreach ($event_days as $count => $event_day) {
                                        $date_selected = ($event_day->date == $meeting->mou_sign_date) ? 'selected' : '';
                                        echo '<li class="'.$date_selected.'" data-day="'.($count+1).'" data-date="'. date('Y-m-d', strtotime($event_day->date)) .'" data-date_formated="'. date('d F Y', strtotime($event_day->date)) .'">Exhibition day '.($count+1).'</li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div class="col-md-9">
                                <h4>Select Time</h4>
                                <div class="row" id="available_times">

                                </div>

                                <?php
                                    $commercial_value = $meeting->commercial_value;
                                    $currency = $meeting->commercial_value;
                                    $spilt = explode(' ', $currency);
                                    
                                ?>
                                <div class="text-right">
                                    <form action="<?= base_url('mou_re_schedule_submit.html') ?>?id=<?= $this->input->get('id') ?>" method="post" id="crd_form">
                                        <div class="row">
                                            <div class="col-md-6">
												<label style="float:left">Commercial Value <span class="text-red">*</span></label><br/>
                                                <div class="form-group" style="width: 100% !important;">
													<label class="sr-only" for="exampleInputAmount">Amount (in dollars)</label>
													<div class="input-group" style="width: 100% !important;">
													  <input type="text" class="form-control" id="exampleInputAmount" value="<?= intval(preg_replace('@[^0-9]@', "", $commercial_value))  ?>" disabled>
													  <div class="input-group-addon"><?= $spilt[1] ?></div>
													</div>
												</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label style="float:left">Description <span class="text-red">*</span></label>
                                                <textarea name="description" class="form-control" rows="3" disabled><?= $meeting->description ?></textarea>
                                            </div>
                                        </div>
										<br/>
                                        <p class="bg-danger js-msgbox"></p>

                                        <input type="hidden" name="booking_day" class="booking_day">
                                        <input type="hidden" name="booking_date" class="booking_date">
                                        <input type="hidden" name="booking_time" class="booking_time">
                                        <input type="hidden" name="booking_to" value="<?= $book_to_data->id ?>">
                                        <input type="hidden" name="user_type" value="<?= $meeting->user_type_from ?>">
                                        <button type="button" class="btn btn-primary btn-lg js-form_btn">Save Schedule</button>
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



<?php $this->load->view('includes/after_login/footer'); ?>

<script>
	$(function () {
		doFormValidation({
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("mou_re_schedule_validate.html"); ?>?id=<?= $this->input->get('id') ?>",
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
			url:     '<?= base_url('mou_report/get_available_time') ?>',
			data:    {
				day: day,
				date: date,
				type: '<?= $meeting->user_type_from ?>',
				id: '<?= myid($meeting->request_from_id) ?>',
                mou_id: '<?= $meeting->id ?>'
            },
			success: function (data) {
				$('#available_times').html(data);

				$('.booking_time').val('');

				if (date == '<?= $meeting->mou_sign_date ?>') {
					$('#available_times .small-box[data-time="<?= date('H:i', strtotime($meeting->mou_sign_time)) ?>"]').addClass('selected')
				}
			},
			error:   function () {
			}
		});
    });


    $(document).on('ready', function () {
		$('.day-select li.selected').click();
	});
</script>

</body>
</html>