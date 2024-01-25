<script>
	function guid() {
		function s4() {
			return Math.floor((1 + Math.random()) * 0x10000)
				.toString(16)
				.substring(1);
		}
		return new Date().getTime() + '_' + s4() + s4();
	}

	function add_open_close_date(date, open_time, close_time) {
		open_time = open_time || '';
		close_time = close_time || '';

		if (!date) {
			if ($('#opening_closing_time_area tr').length > 0) {
				date = moment(new Date($('#opening_closing_time_area tr:last-child .opening_closing_date_picker').val())).add('days', 1);
            } else {
				date = new Date();
            }
        }

		var id = guid();
		var h = '<tr>\
				<td>\
				    <input type="text" class="form-control opening_closing_date_picker" name="opening_closing['+id+'][date]" value="'+moment(date).format('MM/DD/YYYY')+'">\
				</td>\
				<td>\
                    <div class="input-group bootstrap-timepicker">\
                        <input type="text" name="opening_closing['+id+'][open_time]" class="form-control timepicker" value="'+open_time+'">\
                    </div>\
				</td>\
				<td>\
                    <div class="input-group bootstrap-timepicker">\
                        <input type="text" name="opening_closing['+id+'][closing_time]" class="form-control timepicker" value="'+close_time+'">\
                    </div>\
				</td>\
				<td><button type="button" class="btn btn-danger remove_exhibition_date"><i class="fa fa-times"></i></button></td>\
				</tr>';
		$('#opening_closing_time_area').append(h);

		$('#opening_closing_time_area tr:last-child .timepicker').timepicker();
		$('#opening_closing_time_area tr:last-child .opening_closing_date_picker').datepicker({
			startDate: new Date()
		});
	}




    //CKEDITOR.replace('dashboard_content');


	$(document).on('click', '.remove_exhibition_date', function (e) {
		e.stopImmediatePropagation();

		if ($('#opening_closing_time_area > tr').length > 1) {
			$(this).parents('tr').remove();
		}
	});






	$(document).on('change', '.location_id_field', function (e) {
		e.stopImmediatePropagation();

		var id = $(this).val();
		if (id && id != '') {
			$('#location_halls_area').html('<div class="col-sm-12">Loading...</div>');
			$.ajax({
				type:    'post',
				url:     '<?= base_url('Locations/get_halls_json') ?>',
				data:    {location_id: id},
				success: function (data) {
					$('#location_halls_area').html('');
					try {
						data = JSON.parse(data);
						if (data.hasOwnProperty('error') && data.error == 1) {
							$('#location_halls_area').html('<div class="col-sm-12">'+data.message+'</div>');
							return;
                        }

						var h = '';
                        for (var i=0; i<data.length; i++) {
							h = '<div class="col-sm-2">\
								<div class="checkbox">\
								<label><input type="checkbox" name="location_halls[]" value="'+data[i].id+'"> '+data[i].hall_title+'</label>\
								</div>\
								</div>';
							$('#location_halls_area').append(h);
                        }

                    } catch (e) {
						console.log(e);
					}
				},
				error:   function () {
					$('#location_halls_area').html('');
				}
			});
        } else {
			$('#location_halls_area').html('');
        }
	});

	<?php
	if (isset($edit_data)) {
	    // dates
		$exhibition_dates = $this->db
			->where('exhibition_id', $edit_data->id)
			->get('es_exhibition_date')
			->result();

		echo '$("#opening_closing_time_area").html("");';
		foreach ($exhibition_dates as $exhibition_date) {
			echo 'add_open_close_date("'.$exhibition_date->date.'", "'.date('h:i A', strtotime($exhibition_date->open_time)).'", "'.date('h:i A', strtotime($exhibition_date->closing_time)).'"); ';
		}
		echo '$(".timepicker").timepicker();';


    } else {
		echo 'add_open_close_date();';
    }

    ?>

</script>