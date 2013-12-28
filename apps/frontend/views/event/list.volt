{% extends "layouts/base.volt" %}

{% block content %}
    <div class="container" id="content_noBorder">
        <div class=" profile-box">
            <div class="profile-body">
                <div class="events-list">
                    {% if object is defined %}
                    
                        {% for event in object %}
                            <div class="row-fluid eventListing" id="element_{{ event.id }}">
                                <div class="span12">
                                    <div class="event-one-img">
                                        <a href="#">
											{% if event.logo != '' %}
												<img src= '/upload/img/event/{{event.logo}}' width='159px' height='159px'>
											{% endif %}
										</a>
                                    </div>

                                    <div class="event-one-text">
                                        <a href="/event/show/{{ event.id }}" class="name-link">{{ event.name }}</a>

                                        <div class="date-list">
                                            <i class="icon-time"></i>
                                            <span class="date-start">{{ event.start_date }}</span> start at
                                            <span class="date-time">{{ event.start_time }}</span>
                                        </div>
                                        <div class="event-text">
                                            <p> {{ event.description|striptags|escape|truncate(300) }}</p>
                                            <span class="hide-span"></span>
                                        </div>
                                    </div>
                                    <div class="event-list-btn clearfix">
                                        <div class="status-btn clearfix">
                                            {% if event.event_status == 1 %}
                                                <button class="btn btn-block unpublishEvent" id="{{ event.id }}">
                                                    <span class="btn-text">unpublish</span>
                                                </button>
                                            {% else %}
                                                <button class="btn btn-block publishEvent" id="{{ event.id }}">
                                                    <span class="btn-text">publish</span>
                                                </button>
                                            {% endif %}
                                            <button class="btn btn-block editEvent" id="{{ event.id }}"><span class="btn-text">edit</span></button>
                                            <button class="btn btn-block deleteEvent" id="{{ event.id }}"><span class="btn-text">archive</span></button>
                                        </div>
                                    </div>
                            	</div>
                                </div>
                            	<hr/>
                            </div>
                        	{% endfor %}
                    {% else %}
                        <p>You didn't create events yet.</p>
                    {% endif %}
                </div>
                <div class="padd_30"></div>
            </div>
        </div>
    </div>

{% endblock %}