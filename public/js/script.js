$(document).ready(function() {
/************************************ Slider ******************************************/
        $.featureList(
            $("#tabs li a"),
            $("#output li"), {
                start_item	:	1
            }
        );
/************************************ Slider ******************************************/
/************************************ Testimonials ******************************************/
	// Hiding all the testimonials, except for the first one.
	$('#testimonials li').hide().eq(0).show();

	// A self executing function that loops through the testimonials:
	(function showNextTestimonial(){

		// Wait for 7.5 seconds and hide the currently visible testimonial:
		$('#testimonials li:visible').delay(7500).fadeOut('slow',function(){

			// Move it to the back:
			$(this).appendTo('#testimonials ul');

			// Show the next testimonial:
			$('#testimonials li:first').fadeIn('slow',function(){

				// Call the function again:
				showNextTestimonial();
			});
		});
	})();
/************************************ Testimonials ******************************************/

/************************************ Currency Converter ******************************************/

    if($('#MDL').val() == ""){
            var MDL_Value = 1000;
            $('#MDL').val(MDL_Value);
            $('#EUR').val(roundNumber(MDL_Value/currency_information.EUR.value, 2));
            $('#USD').val(roundNumber(MDL_Value/currency_information.USD.value, 2));
            $('#RUB').val(roundNumber(MDL_Value/currency_information.RUB.value, 2));
            $('#RON').val(roundNumber(MDL_Value/currency_information.RON.value, 2));
            $('#UAH').val(roundNumber(MDL_Value/currency_information.UAH.value, 2));
    }

     $('.currency_converter_input').bind('keypress', function(e) {
        //alert(e.which);
        if ( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) {
            return false;
        } else {
            return true;
        }
    }); 

    $('.currency_converter_input').bind('keyup', function(){

        var charCode = $(this).attr('id');
        var inputValue = $(this).val();

        if(charCode == "MDL"){
            var MDL_Value = inputValue;
        }else if(charCode == "EUR"){
            var MDL_Value = currency_information.EUR.value*inputValue;
        }else if(charCode == "USD"){
            var MDL_Value = currency_information.USD.value*inputValue;
        }else if(charCode == "RUB"){
            var MDL_Value = currency_information.RUB.value*inputValue;
        }else if(charCode == "RON"){
            var MDL_Value = currency_information.RON.value*inputValue;
        }else if(charCode == "UAH"){
            var MDL_Value = currency_information.UAH.value*inputValue;
        }
          $('#MDL').val(roundNumber(MDL_Value, 2));
          $('#EUR').val(roundNumber(MDL_Value/currency_information.EUR.value, 2));
          $('#USD').val(roundNumber(MDL_Value/currency_information.USD.value, 2));
          $('#RUB').val(roundNumber(MDL_Value/currency_information.RUB.value, 2));
          $('#RON').val(roundNumber(MDL_Value/currency_information.RON.value, 2));
          $('#UAH').val(roundNumber(MDL_Value/currency_information.UAH.value, 2));        
    });

/************************************ Currency Converter ******************************************/


});


//--------------------------------------- Functions -----------------------------------------------/

/************************************ Currency Converter ******************************************/
    function roundNumber(num, dec) {
        var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
        return result;
    }
/************************************ Currency Converter ******************************************/

/************************************ Login/signup ******************************************/
    function logIn()
    {
        $('#userSelectOptions').toggle('slow', function() {});
    }

    function startLogin()
    {
        $('#status-message').html('');
        $('#login-submit-btn').css('display', 'none');
    }

    function stopLogin(success, message)
    {
        $('#login-submit-btn').css('display', 'block');
        if(success == 0)
            {
                $('#status-message').html('<span style="color:#ff9999;">'+message+'</span>');
            }else
                {
                    $('#status-message').html('<font color="green">'+message+'</font>');
                    $('#login_container').hide('fast');
                    $('#signup_container').hide('fast');
                    $('#success_login').show('slow');
                    setTimeout('logIn();', 1000 );
                }
    }

/************************************ Login/signup ******************************************/

/************************************ The map ******************************************/
      function init() {
        map = new OpenLayers.Map("map");
        var mapnik = new OpenLayers.Layer.OSM();
        map.addLayer(mapnik);
        var lonLat = new OpenLayers.LonLat( 28.841551 ,47.026859 )
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            map.getProjectionObject() // to Spherical Mercator Projection
          );
        var zoom=7;

        var markers = new OpenLayers.Layer.Markers( "Markers" );
        map.addLayer(markers);

        markers.addMarker(new OpenLayers.Marker(lonLat));

        map.setCenter (lonLat, zoom);

/*        map.setCenter(new OpenLayers.LonLat(28, 47) // Center of the map
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            new OpenLayers.Projection("EPSG:900913") // to Spherical Mercator Projection
          ), 7 // Zoom level
        );*/
      }
/************************************ The map ******************************************/

//--------------------------------------- Functions -----------------------------------------------/