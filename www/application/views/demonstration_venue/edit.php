<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="demonstration_venue">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Demonstration
            <small>Venue</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
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
                        <h3 class="box-title">Venue Edit</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('demonstration-venue-edit-submit.html') ?>?id=<?= $this->input->get('id') ?>"
                              method="post" id="crd_form" enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="user_first_name">Venue Title</label>
                                        <input type="text" class="form-control" name="venue_title"
                                               value="<?= html_escape($this->formdata->venue_title) ?>"
                                               placeholder="Venue Title">
                                    </div>

                                    <div class="form-group">
                                        <label>Venue Category</label>
                                        <input type="text" class="form-control" name="venue_category"
                                               value="<?= html_escape($this->formdata->venue_category) ?>"
                                               placeholder="Venue Category" list="cat_list">
                                        <datalist id="cat_list">
											<?php
											$venue_category = $this->db
												->distinct('venue_category')
												->select('venue_category')
												->get('es_demonstration_venue')
												->result();

											foreach ($venue_category as $category) {
												echo '<option value="'.$category->venue_category.'">'.$category->venue_category.'</option>';
											}
											?>
                                        </datalist>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Start Time</label>
                                                <div class="input-group bootstrap-timepicker timepicker">
                                                    <input type="text" class="form-control" name="start_time"
                                                           value="<?= date('h:i A', strtotime($this->formdata->start_time)) ?>"
                                                           placeholder="Start Time">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>End Time</label>
                                                <div class="input-group bootstrap-timepicker timepicker">
                                                    <input type="text" class="form-control" name="end_time"
                                                           value="<?= date('h:i A', strtotime($this->formdata->end_time)) ?>"
                                                           placeholder="End Time">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Venue Cost - USD</label>
                                                <input type="number" class="form-control" name="venue_cost_usd"
                                                       value="<?= html_escape($this->formdata->venue_cost_usd) ?>"
                                                       placeholder="Venue Cost - USD">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Venue Cost - PKR</label>
                                                <input type="number" class="form-control" name="venue_cost_pkr"
                                                       value="<?= html_escape($this->formdata->venue_cost_pkr) ?>"
                                                       placeholder="Venue Cost - PKR">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Venue Address</label>
                                        <input type="hidden" id="venue_lat" name="venue_lat" value="<?= html_escape($this->formdata->venue_lat) ?>">
                                        <input type="hidden" id="venue_long" name="venue_long" value="<?= html_escape($this->formdata->venue_lng) ?>">
                                        <input type="text" id="venue_address" class="form-control" name="venue_address" placeholder="Venue Address" value="<?= html_escape($this->formdata->venue_address) ?>">
                                        <div id="venue_address_map" style="height: 200px"></div>
                                    </div>
                                </div>
                            </div>



                            <p class="bg-danger js-msgbox"></p>

                            <div class="row">
                                <div class="col-xs-10">

                                </div>
                                <div class="col-xs-2">
                                    <a href="javascript:void(0);"
                                       class="btn btn-primary btn-block margin-bottom js-form_btn">Save</a>
                                </div>
                            </div>

                        </form>

                    </div><!-- /.box-body -->
                </div><!-- /.box -->

                <!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->
    <!-- /.content -->
</div>

<?php $this->load->view('includes/after_login/footer'); ?>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCK7Llb8gm-vuU71quxIkYghf1kT03gQB8&libraries=places"
        async defer></script>

<script>
	$(function () {

		$('.timepicker input').timepicker({
			showInputs: false
		});

		doFormValidation({
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("demonstration-venue-edit-validate.html"); ?>?id=<?= $this->input->get('id') ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		});
	});

	function initvenueMap() {
		var mapDiv = document.getElementById('venue_address_map');
		var area_map_center = new google.maps.LatLng(24.8615, 67.0099);
		<?php if ($this->formdata->venue_lat && $this->formdata->venue_lng) {
		echo 'area_map_center = new google.maps.LatLng('.$this->formdata->venue_lat.', '.$this->formdata->venue_lng.');';
        } ?>
		var area_map = new google.maps.Map(mapDiv, {
			center: area_map_center,
			zoom:   15,
			scrollwheel: false,
			disableDoubleClickZoom: true,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
		});
		// Create the search box and link it to the UI element.
		var input = document.getElementById('venue_address');
		var defaultBounds = new google.maps.LatLngBounds(
			new google.maps.LatLng(24.8615, 67.0099));

		var searchBox = new google.maps.places.SearchBox(input, {
			bounds: defaultBounds
		});

		// Bias the SearchBox results towards current map's viewport.
		area_map.addListener('bounds_changed', function () {
			searchBox.setBounds(area_map.getBounds());
		});

		var marker = null;
		// Listen for the event fired when the user selects a prediction and retrieve
		// more details for that place.
		searchBox.addListener('places_changed', function () {
			var places = searchBox.getPlaces();
			console.log('places', places);

			if (places.length == 0) {
				return;
			}
			console.log('selected location lat: ', places[0].geometry.location.lat());
			console.log('selected location lng: ', places[0].geometry.location.lng());

			$('#venue_lat').val(places[0].geometry.location.lat());
			$('#venue_long').val(places[0].geometry.location.lng());

			if (marker && marker != null) {
				marker.setMap(null);
			}

			// For each place, get the icon, name and location.
			var bounds = new google.maps.LatLngBounds();
			var place = places[0];
			if (!place.geometry) {
				console.log("Returned place contains no geometry");
				return;
			}
			var icon = {
				url:        place.icon,
				size:       new google.maps.Size(71, 71),
				origin:     new google.maps.Point(0, 0),
				anchor:     new google.maps.Point(17, 34),
				scaledSize: new google.maps.Size(25, 25)
			};

			marker = new google.maps.Marker({
				map:      area_map,
				title:    place.name,
				position: place.geometry.location,
				draggable: true
			});

			marker.addListener('dragend', function(event) {
				console.log('final position is '+event.latLng.lat()+' / '+event.latLng.lng());
				$('#venue_lat').val(event.latLng.lat());
				$('#venue_long').val(event.latLng.lng());
			});
			if (place.geometry.viewport) {
				// Only geocodes have viewport.
				bounds.union(place.geometry.viewport);
			} else {
				bounds.extend(place.geometry.location);
			}

			area_map.fitBounds(bounds);
		});

		<?php if ($this->formdata->venue_lat && $this->formdata->venue_lng) { ?>
		marker = new google.maps.Marker({
			map:      area_map,
			title:    '<?= $this->formdata->venue_address ?>',
			position: {lat: <?= $this->formdata->venue_lat ?>, lng: <?= $this->formdata->venue_lng ?>},
			draggable: true
		});
		marker.addListener('dragend', function(event) {
			console.log('final position is '+event.latLng.lat()+' / '+event.latLng.lng());
			$('#venue_lat').val(event.latLng.lat());
			$('#venue_long').val(event.latLng.lng());
		});
		<?php } ?>
	}

	$(document).ready(function () {
		setTimeout(function () {
			initvenueMap();
		}, 1000)
	});
</script>
</body>
</html>
