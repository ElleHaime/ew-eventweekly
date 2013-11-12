{% extends "layouts/member.volt" %}

{% block content %}
<h1>Your events</h1>
<div align="left">
	{% for event in events %}
		<div>
			{{ link_to ('events/edit/' ~ event.id, event.name) }}<br>{{ event.description}}
		</div>
		<br><br>
	{% endfor %}
</div>
{% endblock %}