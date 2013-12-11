{% extends "layouts/base.volt" %}

{% block content %}

<div class="container content">
    <h3 style="color: #FAF5F9; font-weight:bold; padding-left:15px;">Your events</h3>
    <div class="row-fluid ">
        <div class="span12">
	    {% if object is defined %}
        	{% for event in object %}
        		<div class="list-event clearfix">
					<div class="list-event-img">
						<a href="#">
							{% if event.logo != '' %}
								{{ image('/upload/img/event/' ~ event.logo ) }}
							{% endif %}
						</a>
					</div>
					<div class="list-event-text">
						{{ link_to ('/event/edit/' ~ event.id, event.name) }}
						<p>{{ event.description}}</p>
	                    <div class="date-list">
	                        <i class="icon-time"></i> start
	                        	<span class="date-start">{{ event.start_date }} at {{ event.start_time }}</span><br>
	                        <i class="icon-time"></i> finish
	                        	<span class="date-start">{{ event.end_date }} at {{ event.end_time }}</span>
	                    </div>

	                    <button class="btn editEvent" style="padding-left:5px; padding-right:10px;" id="{{ event.id }}">
	                    	<span class="btn-text">edit</span>
	                    </button>

	                    <button class="btn deleteEvent" style="padding-left:5px; padding-right:10px;" id="{{ event.id }}">
	                    	<span class="btn-text">delete</span>
	                    </button>

	                    {% if event.event_status == 1 %}
	                    	<button class="btn unpublishEvent" style="padding-left:5px; padding-right:10px;" id="{{ event.id }}">
	                     		<span class="btn-text">unpublish</span>
	                     	</button>
	                    {% else %}
	                    	<button class="btn publishEvent" style="padding-left:5px; padding-right:10px;" id="{{ event.id }}">
								<span class="btn-text">publish</span>
							</button>
	                    {% endif %}
	                    </button>
					</div>
				</div>
        	{% endfor %}
	    {% else %}
		You didn't create events yet.
	    {% endif %}
        </div>
    </div>
</div>

{% endblock %}