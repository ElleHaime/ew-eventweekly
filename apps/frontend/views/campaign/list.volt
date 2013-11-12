{% extends "layouts/base.volt" %}

{% block content %}

<div class="container content">
    <h3 style="color: #FAF5F9; font-weight:bold; padding-left:15px;">Your campaigns</h3>
    <div class="row-fluid ">
        <div class="span12">

			{% for item in object %}
				<div class="list-event clearfix">
					<div class="list-event-img">
						<a href="#">{{ image('img/demo/h_back_1.jpg') }}</a>
					</div>
					<div class="list-event-text">
						{{ link_to ('campaigns/edit/' ~ item.id, item.name) }}
						<p>{{ item.description}}</p>
	                    <div class="date-list">
	                        <i class="icon-time"></i> start
	                        <span class="date-start">20/03/23</span> finish
	                        <span class="date-finish">30/03/23</span>
	                    </div>

	                    <button class="btn" style="padding-left:5px; padding-right:10px;" onclick="javascript:dropCampaign();"><span class="btn-text">delete</span></button>
	                    <button class="btn" style="padding-left:5px; padding-right:10px;" onclick="javascript:dropCampaign();"><span class="btn-text">stash events</span></button>
					</div>
				</div>
			{% endfor %}

        </div>
    </div>
 </div>

{% endblock %}