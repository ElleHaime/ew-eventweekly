{% extends "layouts/base.volt" %}

{% block content %}

<div class="container" id="content_noBorder">
    <div class="padd_30"></div>
        <div class="profile-body">
            <div class="row-fluid">
                <div class="span12">
                    <h3 class="title-page">New events</h3>

                    <div class="row-fluid active-events">
                        <div class="span12">

                            {% for event in events %}

                                <div class="events-list  music-category">
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
                                                        {#<span class="day-title"> - tomorrow</span>#}
                                                    </div>
                                                    <p>
                                                        {{ event['anon']|striptags|escape|truncate(350) }}
                                                    </p>

                                                    <div class="plans-box clearfix">
                                                        <button class="btn eventLikeBtn" data-status="1" data-id="{{ event['id'] }}">Like</button>
                                                        <button class="btn eventLikeBtn" data-status="0" data-id="{{ event['id'] }}">Don`t like</button>
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

                                <div style="margin-left: 50px"><i>Your friends don't have events!</i></div>

                            {% endfor %}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{% endblock %}
