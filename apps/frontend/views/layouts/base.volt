<!DOCTYPE html>
<html>
<head>
    <title>EventWeekly</title>
    <meta charset="utf-8" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link type="image/ico" href="/img/128.ico" rel="icon">
    {% if event is defined %}
    	<meta property="og:image" content="http://events.apppicker.com/upload/img/event/{{ event['logo'] }}"/>
    	<meta property="og:title" content="EventWeekly"/>
    {% endif %}

    {{ stylesheet_link('css/bootstrap.min.css') }}
    {{ stylesheet_link('css/bootstrap-responsive.min.css') }}
    {{ stylesheet_link('css/bootstrap-datetimepicker.min.css') }}
    {{ stylesheet_link('css/style.css') }}
    {{ stylesheet_link('css/jake.css') }}
    
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->

    <script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&sensor=false&libraries=places"></script>
    <script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js"></script>

    {{ javascript_include('js/project/vendors/underscore.js') }}

    {{ javascript_include('js/project/vendors/jquery.cookie.js') }}

    {{ javascript_include('js/bootstrap.min.js') }}
    {{ javascript_include('js/bootstrap-datetimepicker.min.js') }}

    {{ javascript_include('js/main.js') }}
    {{ javascript_include('js/project/map/gmap.js') }}
    {{ javascript_include('js/project/map/gmap_events.js') }}

    {{ javascript_include('js/interface.js') }}
    {{ javascript_include('js/fb.js') }}

    {{ javascript_include('js/addressAutocomplete.js') }}
    {{ javascript_include('js/top_panel.js') }}

    <script type="text/javascript">
        $(document).ready(function(){
            topPanel.init({
                searchCityBlock: '.searchCityBlock'
            });
            app.Gmap.init({
                mapCenter: {
                    lat: '{{ location.latitude }}',
                    lng: '{{ location.longitude }}'
                }
            });
            app.GmapEvents.init();
        });
    </script>
</head>

<body>
	<div id="fb-root"></div>
	
  {% if external_logged is defined %}
    <div id="external_logged" extname="{{ external_logged }}"></div>
  {% endif %}
  {% if acc_external is defined %}
    <input type="hidden" id="member_ext_uid" value="{{ acc_external.account_uid }}">
  {% endif %}

	<div class="out">
	    <div class="container header">
	        <div class="row">
	                <div class="span2">
	                        <span  class="line"></span>
	                        <a href="/" class="logo"></a>	                
	                </div>
	                {% if member is defined %}
        						{% include 'layouts/accheader.volt' %}
        					{% else %}
        						{% include 'layouts/guestheader.volt' %}
        					{% endif %}
        	</div>

	       <div class="container">
                        <a href="#" class="btn-row-down">
                            <i class=" icon-white icon-chevron-down "></i>
                        </a>
                        <div id="back-to-top" class="text-center clearfix">

                            <div class=" location-box">
                                <div class="location clearfix">
                              {% if eventsTotal is defined %}
                                  <span class="location-count" id="events_count">{{ eventsTotal }}</span>
                              {% else %}
                                  <span class="location-count location-count_no" id="events_count">0</span>
                              {% endif %}
                          <div class="location-place location-place_country">
                              <a href="#" class="location-city">
                                  <i class="caret"></i>
                                  <span>{{ location.alias }}</span>
                              </a>
                              <div class="location-search searchCityBlock clearfix" style="width: 295px">
                                  <div class="input-append" style="float: none">
                                      <input class=" input-large"  size="16" type="text" placeholder="Event search engine" id="topSearchCity" style="width: 69%">
                                      <button class="btn" type="button">Find</button>
                                  </div>
                              </div>
                          </div>
                          <div class="location-place location-place_ask">
                              <a href="#">
                              <i class="caret"></i>
                                  <span>What are you looking for?</span>
                              </a>
                              <div class="location-search clearfix">
                                  <div class="input-append">
                                      <input class=" input-large"  size="16" type="text" placeholder="Search city">
                                      <button class="btn" type="button">Find</button>
                                  </div>
                              </div>

                          </div>

                        </div>
                                </div>
                                <span  class="line"></span>
                            </div>

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
      $('#user-down').slideToggle('2000');
    });
    $('#user-down-caret').click(function() {
      $('.user-box').toggleClass('active-box');
    });

    $('.location-place_ask > a ').click(function() {
      $('.location-place_ask .location-search').slideToggle('2000');
    });
        $('.location-place_ask > a ').click(function() {
      $('.location-place_ask').toggleClass('active-box');
    });
     $('.location-place_country .location-city').click(function() {
          $('.location-place_country').toggleClass('active-box');
     });

    $('.btn-row-down').click(function() {
      $('#back-to-top').slideToggle('slow');
    });


	</script>

    <script>

        // Wait until the document is ready
        $(function(){

            if($.fn.noUiSlider) {
                // Run noUiSlider
                $('.noUiSlider').noUiSlider({
                    range: [10,40]
                    ,start: [20,30]
                });
            }

        });


    </script>

</body>
</html>