{% extends "layouts/base.volt" %}

{% block content %}

<div class="container content">
    <h4 style="color: #FAF5F9; font-weight:bold; padding-left:15px; padding-top:10px;">Edit event</h4>
    <div class="row-fluid">
        <div class="span6 offset2">
        	 <form class="form-horizontal" method="post">

        	 	<div class="control-group">
					<label class="control-label" for="inputEmail">{{ form.label('name') }}</label>
					<div class="controls">
						{{ form.render('name') }}
						{{ form.messages('name') }}
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="inputEmail">{{ form.label('description') }}</label>
					<div class="controls">
						{{ form.render('description') }}
						{{ form.messages('description') }}
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="inputEmail">{{ form.label('address') }}</label>
					<div class="controls">
						{{ form.render('address') }}
						{{ form.messages('address') }}
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="inputEmail">{{ form.label('start_date') }}</label>
					<div class="controls">
						{{ form.render('start_date') }}
						{{ form.messages('start_date') }}
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="inputEmail">{{ form.label('end_date') }}</label>
					<div class="controls">
						{{ form.render('end_date') }}
						{{ form.messages('end_date') }}
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="inputEmail">{{ form.label('tickets_url') }}</label>
					<div class="controls">
						{{ form.render('tickets_url') }}
						{{ form.messages('tickets_url') }}
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="inputEmail">{{ form.label('recurring') }}</label>
					<div class="controls">
						{{ form.render('recurring') }}
						{{ form.messages('recurring') }}
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="inputEmail">{{ form.label('current_location') }}</label>
					<div class="controls">
						{{ form.render('current_location') }}
						{{ form.messages('current_location') }}

						{{ form.render('location_id') }}
						{{ form.render('prev_location') }}
						{{ form.render('member_id') }}

						<div id="results" hidden="hidden">
							<ul data-role="listview" id="locations" data-inset="true">
								<li data-role="list-divider" role="heading">Select one:</li>
							</ul>
				     	</div>
					</div>
				</div>

				<div class="control-group">
					<div class="controls">
						<button type="submit" class="btn">Save</button>
					</div>
				</div>

        	 </form>
        </div>
    </div>
</div>
<script type='text/javascript'>
		$('#current_location').keyup(function() {
			var text = encodeURI($('#current_location').val()),
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
			$('#current_location').val($loc_text);
			$('#locations').empty();
			$.mobile.loading("hide");		
		});
</script>

{% endblock %}