{% extends "layouts/base.volt" %}

{% block content %}

<div class="container content">
<h1>Profile</h1>
<div align="left">
	{{ form('member/edit', 'method': 'post', 'enctype': 'multipart/form-data') }}
		<table>
            <tr>
                <td>{{ form.label('extra_email') }}</td>
                <td>
                    {{ form.render('extra_email') }}
                    {{ form.messages('extra_email') }}
                </td>
            </tr>

			<tr>
				<td>{{ form.label('name') }}</td>
				<td>
					{{ form.render('name') }}	
					{{ form.messages('name') }}
				</td>
			</tr>

			<tr>
				<td>{{ form.label('address') }}</td>
				<td>
					{{ form.render('address') }}	
					{{ form.messages('address') }}
				</td>
			</tr>

			<tr>
				<td>{{ form.label('phone') }}</td>
				<td>
					{{ form.render('phone') }}	
					{{ form.messages('phone') }}
				</td>
			</tr>

			<tr>
				<td>{{ form.label('current_location') }}</td>
				<td>
					{{ form.render('prev_location') }}
					{{ form.render('location_id') }}
					{{ form.render('current_location') }}
					{{ form.messages('current_location') }}
					
					<div id="results" hidden="hidden">
			          <ul data-role="listview" id="locations" data-inset="true">
			              <li data-role="list-divider" role="heading">Select one:</li>
			          </ul>
			      </div>
			      
				</td>
			</tr>

            <tr>
                <td>{{ form.label('logo') }}</td>
                <td>
                    {{ form.render('logo') }}
                    {{ form.messages('logo') }}
                </td>
            </tr>

			<tr>
				<td colspan="2">{{ form.render('Save') }}</td>
			</tr>

		</table>
	</form>
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