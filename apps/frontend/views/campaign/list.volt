{% extends "layouts/base.volt" %}

{% block content %}

    <div class="container " id="content_noBorder">


        <div class="row-fluid">
            <div class="span12">
                <div class="event-list_i">
                    <div class="row-fluid">
                        <div class="span12">

                            <div class="padd_30"></div>
                            <div class="page-top clearfix">
                                <h3 class="title-name">Your campaigns</h3>
                                <button class="btn" onclick="location.href = '/campaign/edit'">Add campaign</button>



                        {% if object is defined %}
                            {% for campaign in object %}
                             <hr/>
                                <div class="events-list" id="element_{{ campaign.id }}">
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
                                                        <button class="btn editCampaign" id="{{ campaign.id }}">Edit</button>
									                    <button class="btn deleteCampaign"  id="{{ campaign.id }}" {% if campaign.event|length %} disabled="true" {% endif %}>Delete</button>
                                                    </div>
                                                </div>
                                                
                                                <div class="event-list-btn clearfix">
													{% if campaign.location_id %}
                                                        <div class=" place-address tooltip-text"  data-original-title="{{ campaign.location.alias }} " title="" rel="tooltip">
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
                            <p>You didn't create campaigns yet</p><br/>
                        {% endif %}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

{% endblock %}