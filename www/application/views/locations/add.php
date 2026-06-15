<?php $this->load->view('includes/after_login/header'); ?>
<?php $this->load->view('includes/after_login/sidebar'); ?>


<div class="content-wrapper" data-page="locations">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Locations
            <small></small>
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
                        <h3 class="box-title">Locations Add</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <form action="<?= base_url('locations-submit.html') ?>" method="post" id="crd_form"
                              enctype="multipart/form-data">


                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Location Name</label>
                                        <input type="text" class="form-control" name="location_title"
                                               placeholder="Location Name">
                                    </div>

                                    <div class="form-group">
                                        <label>Location Country</label>
                                        <input type="text" class="form-control" name="location_country"
                                               placeholder="Location Country" list="country_list">
                                        <datalist id="country_list">
											<?php
											$countries = $this->db
												->distinct('location_country')
												->select('location_country')
												->get('es_locations')
												->result();

											foreach ($countries as $country) {
												echo '<option value="'.$country->location_country.'">'.$country->location_country.'</option>';
											}
											?>
                                        </datalist>
                                    </div>

                                    <div class="form-group">
                                        <label>Location City</label>
                                        <input type="text" class="form-control" name="location_city"
                                               placeholder="Location City" list="cat_list">
                                        <datalist id="cat_list">
                                            <?php
                                            $cities = $this->db
                                                ->distinct('location_city')
                                                ->select('location_city')
                                                ->get('es_locations')
                                                ->result();

                                            foreach ($cities as $city) {
                                                echo '<option value="'.$city->location_city.'">'.$city->location_city.'</option>';
                                            }
                                            ?>
                                        </datalist>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Location Address</label>
                                        <input type="hidden" id="location_lat" name="location_lat" >
                                        <input type="hidden" id="location_lng" name="location_lng" >
                                        <input type="text" id="location_address" class="form-control" name="location_address" placeholder="Location Address">
                                        <div id="location_address_map" style="height: 200px"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- START HALLS -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <h4 class="panel-title">Halls</h4>
                                        </div>
                                        <div class="col-sm-6 text-right">
                                            <button type="button" class="btn btn-success add_hall_btn"><i class="fa fa-plus"></i> All Hall</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <label>Hall Title</label>
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Covering Area</label>
                                        </div>
                                        <div class="col-sm-5">
                                            <label>Description</label>
                                        </div>
                                        <div class="col-sm-1"></div>
                                    </div>

                                    <div id="hall_area"></div>
                                </div>
                            </div>
                            <!-- END HALLS -->


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

            </div>
        </div><!-- /.row -->
    </section><!-- /.content -->
    <!-- /.content -->
</div><!-- /.content-wrapper -->

<?php $this->load->view('includes/after_login/footer'); ?>
<!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCK7Llb8gm-vuU71quxIkYghf1kT03gQB8&libraries=places"
        async defer></script> -->
<script>
	$(function () {

		doFormValidation({
			'form':         '#crd_form',
			'msgbox':       '#crd_form .js-msgbox',
			'btnClick':     '#crd_form .js-form_btn',
			'urlValidator': "<?php echo base_url("locations-validate.html"); ?>",
			'loadingImg':   "<?php echo base_url("assets/img/load-indicator.gif"); ?>"
		});


	});

	function initlocationsMap() {
		var mapDiv = document.getElementById('location_address_map');
		var area_map_center = new google.maps.LatLng(24.8615, 67.0099);
		var area_map = new google.maps.Map(mapDiv, {
			center: area_map_center,
			zoom:   15,
			scrollwheel: false,
			disableDoubleClickZoom: true,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
		});
		// Create the search box and link it to the UI element.
		var input = document.getElementById('location_address');
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

			$('#location_lat').val(places[0].geometry.location.lat());
			$('#location_lng').val(places[0].geometry.location.lng());

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
				$('#location_lat').val(event.latLng.lat());
				$('#location_lng').val(event.latLng.lng());
			});
			if (place.geometry.viewport) {
				// Only geocodes have viewport.
				bounds.union(place.geometry.viewport);
			} else {
				bounds.extend(place.geometry.location);
			}

			area_map.fitBounds(bounds);
		});

	}

	$(document).ready(function () {
		setTimeout(function () {
			initlocationsMap();
		}, 1000)
	});


	function guid() {
		function s4() {
			return Math.floor((1 + Math.random()) * 0x10000)
				.toString(16)
				.substring(1);
		}
		return new Date().getTime() + '_' + s4() + s4();
	}

	function add_hall(data) {
		var id = guid();
		var hall_title = (data && data.hasOwnProperty('hall_title')) ? data.hall_title : '';
		var covering_area = (data && data.hasOwnProperty('covering_area')) ? data.covering_area : '';
		var description = (data && data.hasOwnProperty('description')) ? data.description : '';

        html = '<div class="form-group row" >\
                <div class="col-sm-3">\
                    <input type="text" class="form-control" name="hall['+id+'][hall_title]" placeholder="Hall Title"\
                           value="'+hall_title+'">\
                </div>\
                <div class="col-sm-3">\
                    <input type="number" class="form-control" name="hall['+id+'][covering_area]" placeholder="Covering Area"\
                           value="'+covering_area+'">\
                </div>\
                <div class="col-sm-5">\
                    <input type="text" class="form-control" name="hall['+id+'][description]" placeholder="Description"\
                           value="'+description+'">\
                </div>\
                <div class="col-sm-1">\
                    <button type="button" class="btn btn-danger btn-block remove_hall_btn"><i class="fa fa-trash"></i></button>\
                </div>\
            </div>';

        $('#hall_area').append(html);
	}

	add_hall();

    $(document).on('click', '.add_hall_btn', function (e) {
		e.stopImmediatePropagation();

		add_hall();
	});
	$(document).on('click', '.remove_hall_btn', function (e) {
		e.stopImmediatePropagation();

		if ($('#hall_area > .form-group').length <= 1) return;

		$(this).parents('.form-group').remove();
	});
</script>
</body>
</html>
