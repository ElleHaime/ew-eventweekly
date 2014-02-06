{% extends "layouts/base.volt" %}

{% block content %}
	<input type="hidden" id="lat" value="{{ location.latitude }}">
    <input type="hidden" id="lng" value="{{ location.longitude }}">
    <div class="map">
        <div id="map_canvas" style="height:100%;"></div>
    </div>

    <script type="text/javascript">
        var searchResults = {{ list }};
    </script>
{% endblock %}