{% extends "layouts/base.volt" %}

{% block content %}
<div class="padd_70"></div>
<div class="container content_noBorder">
    <h3 style="color: #2a2a2a; font-weight:bold; padding-left:15px;">Your events:</h3>
    <div class="row">
        <div class="span12">
        	{% for event in userEvents %}
        		<div class="list-event clearfix">
					<div class="list-event-img_more">
						<a href="#">
							{% if event['logo'] is defined %}
								<img src="{{ event['logo'] }}">
							{% else %}
								<img src="{{ event['pic_square'] }}">
							{% endif %}
						</a>
					</div>
					<div class="list-event-text_more">
                        {{ link_to ('event/show/' ~ event['id'], event['name']) }}
						<p>{{ event['anon']}}</p>
	                    <div class="date-list">
                        {% if event['start_time'] is defined %}
	                        <i class="icon-time"></i>start <span class="date-start">{{ event['start_time'] }}</span>
                        {% endif %}
                        {% if event['end_time'] is defined %}
                            finish <span class="date-finish">{{ event['end_time'] }}</span>
                        {% endif %}
	                    </div>
					</div>
				</div>
                <hr/>
        	{% endfor %}

        </div>
    </div>
    <h3 style="color: #2a2a2a; font-weight:bold; padding-left:15px;">Friend events:</h3>
    <div class="row">
        <div class="span12">
            {% for event in friendEvents %}
                <div class="list-event clearfix">
                    <div class="list-event-img_more">
                        <a href="#">
							{% if event['logo'] is defined %}
								<img src="{{ event['logo'] }}">
							{% else %}
								<img src="{{ event['pic_square'] }}">
							{% endif %}
						</a>
                    </div>
                    <div class="list-event-text_more">
                        {{ link_to ('event/show/' ~ event['id'], event['name']) }}
                        <p>{{ event['anon']}}</p>
                        <div class="date-list">
                            {% if event['start_time'] is defined %}
                                <i class="icon-time"></i>start <span class="date-start">{{ event['start_time'] }}</span>
                            {% endif %}
                            {% if event['end_time'] is defined %}
                                finish <span class="date-finish">{{ event['end_time'] }}</span>
                            {% endif %}
                        </div>
                    </div>
                </div>
                <hr/>
            {% endfor %}
        </div>
    </div>
</div>

{% endblock %}