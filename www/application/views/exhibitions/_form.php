<div class="row">
	<div class="col-sm-6">
		<div class="form-group">
			<label>Event Name</label>
			<input type="text" class="form-control" name="exhibition_title"
                   value="<?= (isset($edit_data)) ? $edit_data->exhibition_title : '' ?>"
				   placeholder="Event Name">
		</div>
	</div>
    <div class="col-sm-6">
        <label>Event Organizer</label>
        <select name="event_organizer" class="form-control event_organizer_field">
            <option value="">- select -</option>
            <?php
            $organizers = $this->db
                ->where('is_deleted', 0)
                ->get('es_organizer')
                ->result();
            foreach ($organizers as $organizer) {
                $selected = (isset($edit_data) && $edit_data->event_organizer == $organizer->id) ? 'selected' : '';
                echo '<option value="'.$organizer->id.'" '.$selected.'>'.$organizer->organizer_company.'</option>';
            }
            ?>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Price Type</label>
            <select name="price_type" class="form-control">
                <option value="PKR" <?= (isset($edit_data) && $edit_data->price_type == 'PKR') ? 'selected' : '' ?>>Event in PKR</option>
                <option value="USD" <?= (isset($edit_data) && $edit_data->price_type == 'USD') ? 'selected' : '' ?>>Event in USD</option>
            </select>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Thank You Email Template</label>
            <select name="thank_you_template_id" class="form-control">
                <option value="">- select -</option>
                <?php
                $templates = $this->db
                    ->where('title', 'THANK_YOU_EXHIBITOR')
                    ->where('(exhibition_id IS NULL OR exhibition_id = ' . (isset($edit_data) ? $edit_data->id : 0) . ')')
                    ->get('email_template')
                    ->result();
                foreach ($templates as $template) {
                    $selected = (isset($edit_data) && $edit_data->thank_you_template_id == $template->id) ? 'selected' : '';
                    echo '<option value="'.$template->id.'" '.$selected.'>'.$template->subject.'</option>';
                }
                ?>
            </select>
        </div>
    </div>
</div>

<div class="panel panel-default panel-body">
    <div class="row">
        <div class="col-sm-6">
            <label>Event Location</label>
            <select name="location_id" class="form-control location_id_field">
                <option value="">- select -</option>
				<?php
				$locations = $this->db
					->where('is_deleted', 0)
					->get('es_locations')
					->result();
				foreach ($locations as $location) {
					$selected = (isset($edit_data) && $edit_data->location_id == $location->id) ? 'selected' : '';
					echo '<option value="'.$location->id.'" '.$selected.'>'.$location->location_title.'</option>';
				}
				?>
            </select>
        </div>
        <div class="col-sm-6">
            <label>Location Halls</label>
            <div class="row" id="location_halls_area">
				<?php
				if (isset($edit_data) && $edit_data->location_id) {
					$locations_halls = $this->db
						->where('location_id', $edit_data->location_id)
						->where('is_deleted', 0)
						->get('es_location_halls')
						->result();

					foreach ($locations_halls as $locations_hall) {
						$hall_selected = $this->db
							->where('exhibition_id', $edit_data->id)
							->where('hall_id', $locations_hall->id)
							->count_all_results('es_exhibition_halls');

						$hall_checked = ($hall_selected > 0) ? 'checked' : '';
						echo '<div class="col-sm-3">
                        <div class="checkbox">
                            <label><input type="checkbox" name="location_halls[]" '.$hall_checked.' value="'.$locations_hall->id.'"> '.$locations_hall->hall_title.'</label>
                        </div>
                    </div>';
					}
				}

				?>
            </div>
        </div>
    </div>


</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
            <div class="col-sm-6">
                <h4 class="panel-title control-text">Event Dates</h4>
            </div>
            <div class="col-sm-6 text-right">
                <button type="button" class="btn btn-success" onclick="add_open_close_date();"><i class="fa fa-plus"></i> Add Date</button>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Event Date</th>
                <th>Opening Time</th>
                <th>Closing Time</th>
                <th></th>
            </tr>
            </thead>
            <tbody id="opening_closing_time_area"></tbody>
        </table>
    </div>

</div>

<div class="row">
	<div class="col-sm-3">
		<label>Event Logo</label>
		<div id="event_logo_container"></div>
	</div>
    <div class="col-sm-3">
        <label>Event Logo Link</label>
        <input type="text" class="form-control" name="event_logo_link"
               value="<?= (isset($edit_data)) ? $edit_data->event_logo_link : '' ?>">
    </div>
	<div class="col-sm-3">
		<label>Associate Logo</label>
		<div id="event_associate_logo_container"></div>
	</div>
    <div class="col-sm-3">
        <label>Associate Logo Link</label>
        <input type="text" class="form-control" name="associate_logo_link"
               value="<?= (isset($edit_data)) ? $edit_data->associate_logo_link : '' ?>">
    </div>
</div>


<div class="form-group row">
    <div class="col-sm-3">
        <label>Event Manager Logo</label>
        <div id="event_manager_logo_container"></div>
    </div>
    <div class="col-sm-3">
        <label>Event Manager Logo Link</label>
        <input type="text" class="form-control" name="manager_logo_link"
               value="<?= (isset($edit_data)) ? $edit_data->manager_logo_link : '' ?>">
    </div>

    <div class="col-sm-3">
        <label>Event Background Image</label>
        <div id="event_background_container"></div>
    </div>
    <div class="col-sm-3">
        <label>Event Color</label>
        <input type="color" class="form-control" name="event_color"
               value="<?= (isset($edit_data) && !is_null($edit_data->event_color)) ? $edit_data->event_color : '' ?>">
    </div>
</div>

<hr>

<fieldset>
    <legend>Official Stall Builders Contractors</legend>

    <div class="row">
		<?php
		$contractors = $this->db
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->get('es_stall_builders')
            ->result();

		foreach ($contractors as $key => $contractor) {
			$contractor_checked = 'checked';

			if (isset($edit_data)) {
			    $old_contractors = explode(',', $edit_data->stall_builder_contractors);

				$contractor_checked = (in_array(str_replace(' ', '_', $contractor->company_name), $old_contractors)) ? 'checked' : '';
            }

			echo '<div class="col-sm-2">
                        <div class="checkbox">
                            <label><input type="checkbox" name="stall_builder_contractors[]" '.$contractor_checked.' value="'. str_replace(' ', '_', $contractor->company_name) .'"> '. $contractor->company_name .'</label>
                        </div>
                    </div>';
		}
		?>
    </div>
</fieldset>

<fieldset>
    <legend>Official Freight Forwarders</legend>

    <div class="row">
		<?php
		$freight_forwarders = $this->db
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->get('es_freight_forwarders')
            ->result();

		foreach ($freight_forwarders as $key => $freight_forwarder) {
			$contractor_checked = 'checked';

			if (isset($edit_data)) {
			    $old_contractors = explode(',', $edit_data->event_freight_forwarders);

				$contractor_checked = (in_array(str_replace(' ', '_', $freight_forwarder->company_name), $old_contractors)) ? 'checked' : '';
            }

			echo '<div class="col-sm-2">
                        <div class="checkbox">
                            <label><input type="checkbox" name="event_freight_forwarders[]" '.$contractor_checked.' value="'. str_replace(' ', '_', $freight_forwarder->company_name) .'"> '. $freight_forwarder->company_name .'</label>
                        </div>
                    </div>';
		}
		?>
    </div>
</fieldset>