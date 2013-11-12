{% extends "layouts/base.volt" %}

{% block content %}

<div class="container content">
    <h3 style="color: #FAF5F9; font-weight:bold; padding-left:15px;">Your events</h3>
    <div class="row-fluid ">
        <div class="span12">
        	{% for event in object %}
        		<div class="list-event clearfix">
					<div class="list-event-img">
						<a href="#">{{ image('img/demo/h_back_1.jpg') }}</a>
					</div>
					<div class="list-event-text">
						{{ link_to ('event/edit/' ~ event.id, event.name) }}
						<p>{{ event.description}}</p>
	                    <div class="date-list">
	                        <i class="icon-time"></i> start
	                        <span class="date-start">20/03/23</span> finish
	                        <span class="date-finish">30/03/23</span>
	                    </div>

	                    <button class="btn" style="padding-left:5px; padding-right:10px;" onclick="javascript:dropCampaign();"><span class="btn-text">delete</span></button>
					</div>
				</div>
        	{% endfor %}
        </div>
    </div>
</div>

{% endblock %}