<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Google_map
{
	var $map_id = 0;
	var $google_api_key = 0;
	
	var $markers = array ();
	
	function setup_map ($id , $google_api)
	{
		$this->map_id = $id;
		$this->google_api_key = $google_api;

		return $this;
	}
	
	function print_map_head ($map_width = "100%" , $map_height = "400px" , $map_canvas_width="100%",$map_canvas_height="100%")
	{
		if ($this->map_id === 0 || $this->google_api_key === 0)
		{
			trigger_error('Please define googel api key and map id');
			exit;
		}
		
		?>
<style type="text/css">
#map_wrapper<?=$this->map_id?> {
	width:<?=$map_width?>;
    height: <?=$map_height?>;
}

#map_canvas<?=$this->map_id?> {
    width: <?=$map_canvas_width?>;
    height: <?=$map_canvas_height?>;
}
</style>
		<?php
	}
	
	
	function print_map_body ()
	{
		if ($this->map_id === 0 || $this->google_api_key === 0)
		{
			trigger_error('Please define googel api key and map id');
			exit;
		}
	?>
<div id="map_wrapper<?=$this->map_id?>">
    <div id="map_canvas<?=$this->map_id?>" class="mapping"></div>
</div>
    <?php	
	}


	function get_lat_long_with_address($address) {
		$url ="http://maps.googleapis.com/maps/api/geocode/xml?address=".urlencode($address)."&sensor=false";
		$result = @simplexml_load_file($url);
		if ($result === false) {
			return false;
		}
		if (!(isset ($result->result) &&
			isset ($result->result->geometry) &&
			isset ($result->result->geometry->location) &&
			isset ($result->result->geometry->location->lat) &&
			isset ($result->result->geometry->location->lng))
		) {
			return false;
		}

		$data = array ();
		$data['lat'] = (string) $result->result->geometry->location->lat ;
		$data['lng'] = (string) $result->result->geometry->location->lng ;

		return $data;
    }
	
	function get_latlong_with_postalcode ($postalCode)
	{
		$postalCode = strtoupper($postalCode);
		
		$r = get_instance()->db
		->select('id, data')
		->where('identifier',$postalCode)
		->where('type','getLatLong')
		->get ('sft_api_data');
		
		if ($r->num_rows () > 0)
		{
			$row = $r->row ();
			
			$data = base64_decode($row->data);
			$data = unserialize($data);
			$data['sft_api_data_id'] = $row->id;
			
			return $data;
		}
		
		$url ="http://maps.googleapis.com/maps/api/geocode/xml?address=".urlencode($postalCode)."&sensor=false";
		$result = @simplexml_load_file($url);
		if ($result === false)
			return false;
		
		if (
			!(isset ($result->result) && 
		      isset ($result->result->geometry) && 
			  isset ($result->result->geometry->location) && 
			  isset ($result->result->geometry->location->lat) && 
			  isset ($result->result->geometry->location->lng))
		  )
		 return false;
		
		$relavent_data = array ();
		$relavent_data['lat'] = (string) $result->result->geometry->location->lat ;
		$relavent_data['lng'] = (string) $result->result->geometry->location->lng ;
		
		
		$data = array (
			'identifier' => $postalCode ,
			'type' => 'getLatLong',
			'data' => base64_encode(serialize($relavent_data)),
			'xml' => '', 
			'expire' => date ('Y-m-d H:i:s' ,strtotime("+365 days"))
		);
		
		
		
		get_instance()->db
		->set($data)
		->insert ('sft_api_data');	
		
		$relavent_data['sft_api_data_id'] = get_instance()->db->insert_id();
		return $relavent_data;
	}
	
	
	function add_marker ($title , $lat , $long , $content , $icon = "")
	{
		$this->markers[] = array ($title, 
								  $lat , 
								  $long ,  
								  $content , 
								  $icon);
	}
	
	
	function execute_map_code ()
	{
		if ($this->map_id === 0 || $this->google_api_key === 0)
		{
			trigger_error('Please define googel api key and map id');
			exit;
		}
		
		?>
<script>
jQuery(function($) {
    /* Asynchronously Load the map API*/
    var script = document.createElement('script');
    script.src = "http://maps.googleapis.com/maps/api/js?key=<?=urlencode($this->google_api_key)?>&callback=initialize";
    document.body.appendChild(script);
});
window.map = null;
window.all_markers = [];
function initialize() {
/*var map;*/
var bounds = new google.maps.LatLngBounds();
var mapOptions = {
	mapTypeId: 'roadmap',
	zoom:14,
	scrollwheel: false,
	center: new google.maps.LatLng(24.897308, 67.078204),
	styles: [
		  {
			"featureType": "water",
			"elementType": "geometry",
			"stylers": [
			  {
				"color": "#a6a6a0"
			  },
			  {
				"lightness": 17
			  }
			]
		  },
		  {
			"featureType": "landscape",
			"elementType": "geometry",
			"stylers": [
			  {
				"color": "#f5f5f5"
			  },
			  {
				"lightness": 20
			  }
			]
		  },
		  {
			"featureType": "road.highway",
			"elementType": "geometry.fill",
			"stylers": [
			  {
				"color": "#ffffff"
			  },
			  {
				"lightness": 17
			  }
			]
		  },
		  {
			"featureType": "road.highway",
			"elementType": "geometry.stroke",
			"stylers": [
			  {
				"color": "#ffffff"
			  },
			  {
				"lightness": 29
			  },
			  {
				"weight": 0.2
			  }
			]
		  },
		  {
			"featureType": "road.arterial",
			"elementType": "geometry",
			"stylers": [
			  {
				"color": "#ffffff"
			  },
			  {
				"lightness": 18
			  }
			]
		  },
		  {
			"featureType": "road.local",
			"elementType": "geometry",
			"stylers": [
			  {
				"color": "#ffffff"
			  },
			  {
				"lightness": 16
			  }
			]
		  },
		  {
			"featureType": "poi",
			"elementType": "geometry",
			"stylers": [
			  {
				"color": "#f5f5f5"
			  },
			  {
				"lightness": 21
			  }
			]
		  },
		  {
			"featureType": "poi.park",
			"elementType": "geometry",
			"stylers": [
			  {
				"color": "#dedede"
			  },
			  {
				"lightness": 21
			  }
			]
		  },
		  {
			"elementType": "labels.text.stroke",
			"stylers": [
			  {
				"visibility": "on"
			  },
			  {
				"color": "#ffffff"
			  },
			  {
				"lightness": 16
			  }
			]
		  },
		  {
			"elementType": "labels.text.fill",
			"stylers": [
			  {
				"saturation": 36
			  },
			  {
				"color": "#333333"
			  },
			  {
				"lightness": 40
			  }
			]
		  },
		  {
			"elementType": "labels.icon",
			"stylers": [
			  {
				"visibility": "off"
			  }
			]
		  },
		  {
			"featureType": "transit",
			"elementType": "geometry",
			"stylers": [
			  {
				"color": "#f2f2f2"
			  },
			  {
				"lightness": 19
			  }
			]
		  },
		  {
			"featureType": "administrative",
			"elementType": "geometry.fill",
			"stylers": [
			  {
				"color": "#fefefe"
			  },
			  {
				"lightness": 20
			  }
			]
		  },
		  {
			"featureType": "administrative",
			"elementType": "geometry.stroke",
			"stylers": [
			  {
				"color": "#fefefe"
			  },
			  {
				"lightness": 17
			  },
			  {
				"weight": 1.2
			  }
			]
		  }
		]
};

/* Display a map on the page*/
map = new google.maps.Map(document.getElementById("map_canvas<?=$this->map_id?>"), mapOptions);
    map.setTilt(45);
        
    /* Multiple Markers*/
    var markers = [
	<?php
	foreach ($this->markers as $row):
	?>
       ['<?= get_instance()->db->escape_str($row[0]) ?>',  <?=$row[1]?>, <?=$row[2]?> , '<?=get_instance()->db->escape_str($row[4])?>'],
	<?php
	endforeach;	
	?>
    ];
                        
    /* Info Window Content*/
    var infoWindowContent = [
	<?php
	foreach ($this->markers as $row):
	?>
        [<?= json_encode($row[3]) ?>],
	<?php
	endforeach;	
	?>
    ];
        
    /* Display multiple markers on a map*/
    var infoWindow = new google.maps.InfoWindow(), marker, i;

    /* Loop through our array of markers & place each one on the map*/
    for( i = 0; i < markers.length; i++ ) {

		var marker_icon = "<?=base_url ('assets/img/map-icon.png');?>";
		if (markers[i][3] != '')
		{
			if (markers[i][3] == 'user_icon')
			marker_icon = "<?=base_url ('assets/img/person_online.png')?>";
			else if (markers[i][3] == 'user_icon_offline')
			marker_icon = "<?=site_url ('images/person.png')?>";
			else
			marker_icon = "<?=base_url ('assets/img/map-icon.png');?>?text="+encodeURIComponent(markers[i][3]);
		}
		
		var image = {
			  url: marker_icon,
			  size: new google.maps.Size(60, 68),
			  origin: new google.maps.Point(0, 0),
			  anchor: new google.maps.Point(0, 68)
		};
		
        var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
        bounds.extend(position);
        marker = new google.maps.Marker({
            position: position,
            map: map,
			icon: image,
            title: markers[i][0]
        });
        
        /* Allow each marker to have an info window*/
        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infoWindow.setContent(infoWindowContent[i][0]);
                infoWindow.open(map, marker);
            }
        })(marker, i));

		all_markers.push(marker);

        /* Automatically center the map fitting all markers on the screen*/
        /*map.fitBounds(bounds);*/
    }

    /* Override our map zoom level once our fitBounds function runs (Make sure it only runs once)*/
    var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
        this.setZoom(10);
        google.maps.event.removeListener(boundsListener);
    });
    
}
</script>       
        <?php
	}


	function refresh_marker_interval($ajax_route, $interval = 5000) {
		$this->markers = array();
	    ?>

        <script>

            function auto_refresh_map() {
            	var ajax_data = {};

            	if ($('.online_only').is(':checked')) {
            		ajax_data = {
						online_only: true
                    }
                }

				$.ajax({
					type:    'post',
					url:     '<?= $ajax_route ?>',
					data:    ajax_data,
					success: function (data) {
						data = JSON.parse(data);
						console.log('resp', data);

						/* clear previous markers*/
						for (var i = 0; i < all_markers.length; i++) {
							all_markers[i].setMap(null);
						}

						var markers = data.markers;
						/* Display multiple markers on a map*/
						var infoWindow = new google.maps.InfoWindow(), marker, i;
						var infoWindowContent = [];
						/* Loop through our array of markers & place each one on the map*/
						for( i = 0; i < markers.length; i++ ) {

							infoWindowContent.push(markers[i][3]);

							var marker_icon = "<?=site_url ('images/map-icon.png');?>";

							if (markers[i][4] != '')
							{
								if (markers[i][4] == 'user_icon')
									marker_icon = "<?=site_url ('images/person_online.png')?>";
								else if (markers[i][4] == 'user_icon_offline')
									marker_icon = "<?=site_url ('images/person.png')?>";
								else
									marker_icon = "<?=base_url ('map-icon.png');?>?text="+encodeURIComponent(markers[i][4]);
							}

							var image = {
								url: marker_icon,
								size: new google.maps.Size(60, 68),
								origin: new google.maps.Point(0, 0),
								anchor: new google.maps.Point(0, 68)
							};

							var position = new google.maps.LatLng(markers[i][1], markers[i][2]);

							marker = new google.maps.Marker({
								position: position,
								map: map,
								icon: image,
								title: markers[i][0]
							});

							/* Allow each marker to have an info window*/
							google.maps.event.addListener(marker, 'click', (function(marker, i) {
								return function() {
									infoWindow.setContent(infoWindowContent[i]);
									infoWindow.open(map, marker);
								}
							})(marker, i));

							all_markers.push(marker);
						}


						setTimeout(function () {
							auto_refresh_map()
						}, <?= $interval ?>);

					},
					error:   function () {

					}
				});
			}

			setTimeout(function () {
				auto_refresh_map()
			}, <?= $interval ?>);
        </script>

		<?php
	}
	
}