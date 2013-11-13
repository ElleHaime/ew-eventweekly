<!DOCTYPE html>
<html>
<head>
    <title>EventWeekly Demo</title>
    <meta charset="utf-8" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{ stylesheet_link('css/bootstrap.min.css') }}
    {{ stylesheet_link('css/bootstrap-responsive.min.css') }}
    {{ stylesheet_link('css/style.css') }}
    {{ stylesheet_link('css/jake.css') }}
    
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    {{ javascript_include('js/bootstrap.min.js') }}
    {{ javascript_include('js/interface.js') }}
    {{ javascript_include('js/fb.js') }}

	<script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&sensor=false"></script>
	<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js"></script>
</head>

<body>
	<div class="out">
	    <div class="container header">
	        <div class="row-fluid ">
	                <div class="span2">
	                        <a href="/" class="logo">{{ image('img/demo/logo.png', 'alt': 'EventWeekly') }}</a>
	                </div>

					{% if member is defined %}
						{% include 'layouts/accheader.volt' %}
					{% else %}
						{% include 'layouts/guestheader.volt' %}
					{% endif %}
	        </div>
	    </div>
	</div>

	{% block content %}
	{% endblock %}


	<script type='text/javascript'>
		$('#location').keyup(function() {
			var text = encodeURI($('#location').val()),
				url = 'http://maps.googleapis.com/maps/api/geocode/json?address='+text+'&sensor=false&language=en';
			if (text!='') {
	            $.get(url, function(data) {
	                if (data.status == 'OK') {
	                    $('#results').show();
	                    $("#locations").empty();
	                    $.each( data.results, function(key, val) {
	                        $('#locations').append('<li><a id="loc_item" data-lng='+val.geometry.location.lng+' data-lat='+val.geometry.location.lat+'>'+val.formatted_address+'</a></li>');
	                    });
	                    $('#locations').listview('refresh');
	                } else {
	                    $("#locations").empty();
	                }
				});
			}
        });
        
		$('body').on('click', '#loc_item', function(e){
	        var $loc_text = $(this).html();
	
	        $.mobile.loading("show");
			$('#location').val($loc_text);
			$('#locations').empty();
			$.mobile.loading("hide");		
		});
		
		$('#user-down-caret').click(function() {
                $('#user-down').slideToggle('slow');
        });
		
	</script>

</body>
</html>