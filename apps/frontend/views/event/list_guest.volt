{% extends "layouts/base.volt" %}

{% block content %}
<h1>Your events</h1>
<div align="left">
	{% for event in object %}
		<div>
			{{ event.name }}<br>{{ event.description}}
		</div>
		<br><br>
	{% endfor %}
</div>
{% endblock %}