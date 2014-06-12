<!DOCTYPE html>
<html lang="en">
  <head>
 <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Map plugin</title>
    <!-- Bootstrap -->
    <link href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/assets/css/bootstrap.min.css';?>" rel="stylesheet">
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="<?php echo "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/assets/js/jquery-1.11.1.min.js';?>" > </script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/assets/js/bootstrap.min.js'?>"></script>
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500">
     <style type="text/css">

      #maps_canvas { height: 500px }
      #map_modal { height: 500px; z-index: 100;}
	  .pac-container {
	    background-color: #FFF;
	    z-index: 20 !important;
	    position: fixed;
	    display: inline-block;
	    float: left;
	  }
		.modal{
		    z-index: 20;   
		}
		.modal-backdrop{
		    z-index: 10;        
		}​
    </style>
    
    

  <script type="text/javascript">
  var placeSearch, autocomplete, map, mapModal,place, marker;

 
  var componentForm = {
    //street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    country: 'long_name',
    postal_code: 'short_name'
  };


  function mapDefined(mapid){
	  var mapOptions = {
		        center: new google.maps.LatLng(6.668267,-66.578612),
		        zoom: 6,
		        mapTypeId: google.maps.MapTypeId.ROADMAP
		      };
		map = new google.maps.Map(document.getElementById(mapid),
		          mapOptions);

  } 
  
  function initialize() {
     
	 mapDefined('maps_canvas');
	
   }

    function loadScript() {
  	  var script = document.createElement("script");
  	  script.type = "text/javascript";
  	  script.src = "http://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyB74CMqagqbs4PKInYeMk4S9GBya3mOWV8&sensor=false&callback=initialize";
  	  document.body.appendChild(script);
  	}

  	window.onload = loadScript;

  function initializeAuto() {
    // Create the autocomplete object, restricting the search
    // to geographical location types.
    autocomplete = new google.maps.places.Autocomplete(
        /** @type {HTMLInputElement} */(document.getElementById('autocomplete')),
        { types: ['geocode'] });
    // When the user selects an address from the dropdown,
    // populate the address fields in the form.
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
      	fillInAddress();
	    
	      place = autocomplete.getPlace();

	      var mapOptions = {
			        center: place.geometry.location,
			        zoom: 17,
			        mapTypeId: google.maps.MapTypeId.ROADMAP
			      };
		mapModal = new google.maps.Map(document.getElementById('map_modal'),
			          mapOptions);
	          
	     marker = new google.maps.Marker({
	  	    map: mapModal,
	  	    //anchorPoint: new google.maps.Point(0, -29)
	  	  });
	    
	      marker.setVisible(false);
	      
	      if (!place.geometry) {
	        return;
	      }
	      // If the place has a geometry, then present it on a map.
	      if (place.geometry.viewport) {
	    	  mapModal.fitBounds(place.geometry.viewport);
	      } else {
	    	  mapModal.setCenter(place.geometry.location);
	    	  mapModal.setZoom(17);  // Why 17? Because it looks good.
	      }
	      marker.setIcon(/** @type {google.maps.Icon} */({
	        url: place.icon,
	        size: new google.maps.Size(71, 71),
	        origin: new google.maps.Point(0, 0),
	        anchor: new google.maps.Point(17, 34),
	        scaledSize: new google.maps.Size(35, 35)
	      }));
	      marker.setPosition(place.geometry.location);
	      marker.setVisible(true);
  		
    });
  }

  // [START region_fillform]
  function fillInAddress() {
    // Get the place details from the autocomplete object.
    var place = autocomplete.getPlace();

    for (var component in componentForm) {
      document.getElementById(component).value = '';
      //document.getElementById(component).disabled = false;
    }

    // Get each component of the address from the place details
    // and fill the corresponding field on the form.
    for (var i = 0; i < place.address_components.length; i++) {
      var addressType = place.address_components[i].types[0];
      if (componentForm[addressType]) {
        var val = place.address_components[i][componentForm[addressType]];
        document.getElementById(addressType).value = val;
      }
    }
  }
  // [END region_fillform]

  // [START region_geolocation]
  // Bias the autocomplete object to the user's geographical location,
  // as supplied by the browser's 'navigator.geolocation' object.
  function geolocate() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var geolocation = new google.maps.LatLng(
            position.coords.latitude, position.coords.longitude);
        autocomplete.setBounds(new google.maps.LatLngBounds(geolocation,
            geolocation));
      });
    }
  }
  // [END region_geolocation]

    $(document).ready(function(){

    	$('#openForm').click(function(){
    		$('#myModal').modal({show:true});
    		initializeAuto();

       	});

    	$('#goMap').click(function(){
        	if($('#autocomplete').val()!=""){
	    		$('#myModal').modal('hide');
	    		$('#modalMap').modal({show:true});
	    		$('#modalMap').on('shown.bs.modal', function () {
	        	    google.maps.event.trigger(mapModal, "resize");
	        	    mapModal.setCenter(place.geometry.location);
	        	});
        	}else{
        		$('#autocomplete').focus();
        	}
    		
       	});

      $('#btnEnd').click(function(){
    	 
    	  $('#modalMap').modal('hide');

    	  $('#modalMap').on('hidden.bs.modal', function () {
        	  marker.setMap(map);
    		  map.setCenter(place.geometry.location);
    		  map.setZoom(17);
      	});
    	  
			
      });

    });

</script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
<body >
   
  <div class="container">

	<div class="row clearfix">
		<div class="col-md-4 column">
			<h2>
				Dirección
			</h2>
			<p>
				Tu dirección exacta es privada y solo la compartiremos con los huéspedes que dispongan de reserva confirmada
			</p>

		</div>
		<div class="col-md-8 column">
			<div class="jumbotron" id="maps_canvas">
				
			</div>
		   	<div class="col-md-8 column">
			
			<p>
				<a data-toggle="modal"  id="openForm" class="btn btn-primary">Añadir dirección</a>
			</p>
		</div>
		</div>
	</div>
</div>


<div class="modal"  id="myModal" >
	<div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title">Escribe tu dirección</h4>
        </div>
        <div class="modal-body">
          <form role="form">
		  
		  <div class="form-group">
		    <label for="autocomplete">Dirección</label>
		    <input type="text" class="form-control" id="autocomplete" placeholder="Calle, Avenida + Nº Casa" onfocus="geolocate()" >
		  </div>
		  
		  <div class="form-group">
		    <label for="autocomplete">Calle</label>
		    <input type="text" class="form-control" id="route" disabled="disabled">
		  </div>
		  
		   <label for="country">País</label>
		    <input class="form-control" id="country" type="text" disabled="disabled">
		   
		  <div class="form-group">
		    <label for="locality">Ciudad / Población / Distrito </label>
		    <input type="text" class="form-control" id="locality" disabled="disabled">
		    
		  </div>
		  <div class="form-group">
		    <label for="administrative_area_level_1">Estado / Provincia / Condado / Región</label>
		    <input type="text" class="form-control" id="administrative_area_level_1" disabled="disabled">
		    
		  </div>
		  <div class="form-group">
		    <label for="postal_code">Código postal</label>
		    <input type="text" class="form-control" id="postal_code" disabled="disabled">
		    
		  </div>
		  
		</form>
        </div>
        <div class="modal-footer">
          <a href="#" data-dismiss="modal" class="btn">Cancelar</a>
          <a data-toggle="modal" id="goMap" class="btn btn-primary">Siguiente</a>
        </div>
      </div>
    </div>
</div>

<div class="modal fade" id="modalMap" >
	<div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title">Marca la ubicación</h4>
          <p>Desplázate en el mapa para marcar la ubicación exacta.</p>
        </div>
        <div class="modal-body">
          <div  id="map_modal">
          
          </div>
        </div>
        <div class="modal-footer">
          
          <a href="#" id="btnEnd" class="btn btn-primary">Terminar</a>
        </div>
      </div>
    </div>
</div>

  </body>
</html>