{% extends "layouts/base.volt" %}

{% block content %}
    <div class="container" id="content_noBorder">

        <div class="active-events">
            <div class="padd_30"></div>
            <h3 class="title-page">Created events</h3>
                    {% if object is defined %}
                    
                        {% for event in object %}
                            <div class="row-fluid ">
                            <div class="eventListing events-list music-category clearfix" id="element_{{ event.id }}">
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
                                                    <span class="btn-text">Unpublish</span>
                                                </button>
                                            {% else %}
                                                <button class="btn btn-block publishEvent" id="{{ event.id }}">
                                                    <span class="btn-text">Publish</span>
                                                </button>
                                            {% endif %}
                                            <button class="btn btn-block editEvent" id="{{ event.id }}"><span class="btn-text">Edit</span></button>
                                            <button class="btn btn-block deleteEvent" id="{{ event.id }}"><span class="btn-text">Archive</span></button>
                                        </div>
                                    </div>
                            	</div>
                            </div>
                            </div>
                        	{% endfor %}


                    {% else %}
                        <p>You didn't create events yet.</p>
                    {% endif %}
                </div>
    </div>


{% endblock %}