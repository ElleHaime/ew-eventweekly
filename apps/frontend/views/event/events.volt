{% extends "layouts/base.volt" %}

{% block content %}

<div class="container content">
    <h3 style="color: #FAF5F9; font-weight:bold; padding-left:15px;">Your events:</h3>
    <div class="row-fluid ">
        <div class="span12">
        	{% for event in userEvents %}
        		<div class="list-event clearfix">
					<div class="list-event-img">
						<a href="#"><img src="{{ event['pic_square'] }}"></a>
					</div>
					<div class="list-event-text">
                        {{ link_to ('event/show/' ~ event['id'], event['name']) }}
						<p>{{ event['anon']}}</p>
	                    <div class="date-list">
	                        <i class="icon-time"></i>
                            start <span class="date-start">{{ event['start_time'] }}</span>
                            finish <span class="date-finish">{{ event['end_time'] }}</span>
	                    </div>
					</div>
				</div>
        	{% endfor %}
        </div>
    </div>
    <h3 style="color: #FAF5F9; font-weight:bold; padding-left:15px;">Friend events:</h3>
    <div class="row-fluid ">
        <div class="span12">
            {% for event in friendEvents %}
                <div class="list-event clearfix">
                    <div class="list-event-img">
                        <a href="#"><img src="{{ event['pic_square'] }}"></a>
                    </div>
                    <div class="list-event-text">
                        {{ link_to ('event/show/' ~ event['id'], event['name']) }}
                        <p>{{ event['anon']}}</p>
                        <div class="date-list">
                            <i class="icon-time"></i>
                                start <span class="date-start">{{ event['start_time'] }}</span>
                                finish <span class="date-finish">{{ event['end_time'] }}</span>
                        </div>
                    </div>
                </div>
            {% endfor %}
        </div>
    </div>
</div>

{% endblock %}