{% extends "layouts/base.volt" %}

{% block content %}
	<input type="hidden" id="lat" value="{{ user_loc['lat'] }}">
    <input type="hidden" id="lng" value="{{ user_loc['lng'] }}">
    <div class="map">
        <div id="map_canvas"></div>
    </div>
{% endblock %}