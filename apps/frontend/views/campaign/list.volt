{% extends "layouts/base.volt" %}

{% block content %}

    <div class="container " id="content_noBorder">


        <div class="row-fluid">
            <div class="span12">

                <div class="event-list_i">

                    <div class="row-fluid" style="background:#ffffff;">
                        <div class="span12">
                            <div class="padd_30"></div>
                            <h3 class="title-name">Your campaigns</h3>
                        </div>

                        {% if object is defined %}
                            {% for campaign in object %}
                                <div class="events-list">
                                    <div class="row-fluid ">
                                        <div class="span12">
                                            <div class="event-one clearfix">
                                                <div class="event-one-img">
                                                    <a href="#">
														{% if campaign.logo != '' %}
															<img src= '/upload/img/campaign/{{campaign.logo}}' width='159px' height='159px'>
														{% endif %}
													</a>
                                                </div>

                                                <div class="event-one-text">
                                                    <a href="/campaign/edit/{{ campaign.id }}" class="name-link">{{ campaign.name }}</a>

                                                    <div class="event-text">
                                                        <p>{{ campaign.description}}</p>
                                                        <span class="hide-span"></span>
                                                    </div>
                                                    <div class="plans-box clearfix">
                                                        <button class="btn active editCampaign" id="{{ campaign.id }}">edit</button>
									                    <button class="btn active deleteCampaign" {% if campaign.event|length %} disabled="true" {% endif %}>delete</button>
                                                    </div>
                                                </div>
                                                
                                                <div class="event-list-btn clearfix">
													{% if campaign.location_id %}		                                               
														<div class=" place-address">
					                                        <span>{{ campaign.location.alias }}</span>
					                                    </div>
					                                {% endif %}
				                                    <div class="event-site clearfix">
				                                      	{% if campaign.contact|length %}
				                                      		{% for key, val in campaign.contact %}
				                                        		<p>{{ val.value }}</p>
				                                        	{% endfor %}
				                                        {% endif %}
				                                    </div>
				                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {% endfor %}
                        {% else %}
                            You didn't create campaigns yet
                        {% endif %}
                    </div>
                </div>
            </div>
        </div>
    </div>

{% endblock %}