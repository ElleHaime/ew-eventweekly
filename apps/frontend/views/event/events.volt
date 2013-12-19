{% extends "layouts/base.volt" %}

{% block content %}

<div class="container" id="content_noBorder">
    <div class="padd_30"></div>



                    <div class="row-fluid">
                        <div class="span12">
                            <div class="active-events">
                                <div class="row-fluid">
                                    <div class="span12">
                                        <h3 class="title-page">New events</h3>
                                        <div class="events-result">
                                            search result:
                                            <span>123</span>
                                            from
                                            <span>2 334</span>
                                        </div>
                                    </div>
                                </div>
                            {% for event in events %}

                                <div class="events-list  music-category signleEventListElement" event-id="{{ event['id'] }}">
                                    <div class="row-fluid ">
                                        <div class="span12">
                                            <div class="event-one clearfix">
                                                <div class="event-one-img">
                                                    <a href="event/show/{{ event['id'] }}"  class="name-link">
                                                        {% if event['logo'] is defined %}
                                                            <img src="/upload/img/event/{{ event['logo'] }}" width="132px" height ="132px">
                                                        {% else %}
                                                            <img src="{{ event['pic_square'] }}">
                                                        {% endif %}
                                                    </a>
                                                </div>

                                                <div class="event-one-text">
                                                    <a href="event/show/{{ event['id'] }}" class="name-link">{{ event['name']|striptags|escape|truncate(160) }}</a>

                                                    <div class="date-list">
                                                        <i class="icon-time"></i>
                                                        <span class="date-start">{{ event['start_time'] }}</span> start at
                                                        <span class="date-time">{{ event['start_time'] }}</span>
                                                    </div>
                                                    <p>
                                                        {{ event['anon']|striptags|escape|truncate(350) }}
                                                    </p>

                                                    <div class="plans-box clearfix">
                                                        <button class="btn eventLikeBtn" data-status="1" data-id="{{ event['id'] }}">Like</button>
                                                        <button class="btn eventDislikeBtn" data-status="0" data-id="{{ event['id'] }}">Don`t like</button>
                                                    </div>
                                                </div>
                                                <div class="event-list-btn clearfix">
                                                    <div class=" place-address">
                                                        <span>
                                                            {% if event['venue']['street'] is empty %}
                                                                {% if event['location'] is empty %}
                                                                    Undefined place
                                                                {% else %}
                                                                    {{ event['location'] }}
                                                                {% endif %}
                                                            {% else %}
                                                                {{ event['venue']['street'] }}
                                                            {% endif %}
                                                        </span>
                                                    </div>
                                                    {% if event.site is defined %}
	                                                    <div class="event-site clearfix">
	                                                        <p>web-site : <a href="#">http://www.dpdp.com</a></p>
	                                                    </div>
                                                    {% endif %}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            {% else %}

                                <div style="margin-left: 50px"><i>No events found</i></div>

                            {% endfor %}
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>


{% endblock %}
