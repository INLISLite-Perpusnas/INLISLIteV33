<style type="text/css">
  .pac-container{
    z-index: 99999999999999 !important;
  }
</style>

<style>
#map {
  height: 100%;
}
</style>
<div class="modal fade point-map" id="modal_map" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Google Maps
                </h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
				</a>
            </div>
            
            <div class="modal-body">
				<div class="row">
					<div class="form-group col-sm-12">
						<div class="input-group">
							<textarea class="form-control" name="pac_input" id="pac_input" rows="2" placeholder="Search Box"></textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-sm-12">
						<div id="map" class="map_canvas margin-auto" style="width: 100%; height: 500px;"></div>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-sm-12">
						<a href="javascript:void(0)" class="btn btn-warning btn-block btn-lg btn-pick-address"><i class="fa fa-map-marker"></i> Save Address</a>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC58CZr-iYkzHQRS5d2JRmSRQ0-mzKD5-4&libraries=places&callback=initAutocomplete" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/geocomplete/1.7.0/jquery.geocomplete.js" integrity="sha512-W5toA/3gsFXssbDSpzsayIZM7raeIEuYgb9VDLKu159OF4WXDuDnDCAGrW21rcifidjWeR8ARDYOvn5xzmv+1g==" crossorigin="anonymous"></script>
<script>
	function initAutocomplete() {
		$("#pac_input").geocomplete({
			map: ".map_canvas",
			details: "form ",
			markerOptions: {
				draggable: true
			}
		});

		const map = new google.maps.Map(document.getElementById("map"), {
			center: { lat: -6.1762117, lng: 106.8259329 },
			zoom: 15,
			mapTypeId: "roadmap",
			draggable:true,
		});

		map.setOptions({draggable: true});
		// Create the search box and link it to the UI element.
		const input = document.getElementById("pac_input");
		const searchBox = new google.maps.places.SearchBox(input);

		// Bias the SearchBox results towards current map's viewport.
		map.addListener("bounds_changed", () => {
			searchBox.setBounds(map.getBounds());
		});

		
		let markers = [];

		// Listen for the event fired when the user selects a prediction and retrieve
		// more details for that place.
		searchBox.addListener("places_changed", () => {
			$("#pac_input").trigger("geocode");

			// const places = searchBox.getPlaces();
			// if (places.length == 0) {
			// 	return;
			// }

			// // Clear out the old markers.
			// markers.forEach((marker) => {
			// 	marker.setMap(null);
			// });
			// markers = [];

			// // For each place, get the icon, name and location.
			// const bounds = new google.maps.LatLngBounds();

			// places.forEach((place) => {
			// 	if (!place.geometry || !place.geometry.location) {
			// 		console.log("Returned place contains no geometry");
			// 		return;
			// 	}

			// 	const icon = {
			// 		url: place.icon,
			// 		size: new google.maps.Size(71, 71),
			// 		origin: new google.maps.Point(0, 0),
			// 		anchor: new google.maps.Point(17, 34),
			// 		scaledSize: new google.maps.Size(25, 25),
			// 	};

			// 	// Create a marker for each place.
			// 	markers.push(
			// 		new google.maps.Marker({
			// 			map,
			// 			icon,
			// 			title: place.name,
			// 			position: place.geometry.location,
			// 		})
			// 	);
			// 	if (place.geometry.viewport) {
			// 		// Only geocodes have viewport.
			// 		bounds.union(place.geometry.viewport);
			// 	} else {
			// 		bounds.extend(place.geometry.location);
			// 	}
			// });
			// map.fitBounds(bounds);
		});
	}

	window.initAutocomplete = initAutocomplete;

	$('.btn-pick-address').click(function() {
        $('#item_address').val($("#pac_input").val());
        $('.point-map').modal('hide');
    })
</script>

