{% extends "layouts/base.volt" %}

{% block content %}
    <div class="padd_70"></div>

    <div class="container content_noBorder">
        <div class="row-fluid">
            <div class="span12">
                <h3 class="title-page">Events for you</h3>

                {% for event in events %}
                    <div class="row-fluid events-list">
                        <div class="span7">
                            <div class="event-one clearfix">
                                <div class="event-one-img">
                                    <div class="img-box">
                                        <a href="event/show/{{ event['id'] }}"  class="name-link">
                                            {% if event['logo'] is defined %}
                                                <img src="/upload/img/event/{{ event['logo'] }}">
                                            {% else %}
                                                <img src="{{ event['pic_square'] }}">
                                            {% endif %}
                                            </a>
                                    </div>
                                    <div class="like-box clearfix">
                                        <span class="eventLikeBtn" data-status="1" data-id="{{ event['id'] }}"><img src="img/demo/like.png" alt="like" title="like"></span>
                                        <span class="eventLikeBtn" data-status="0" data-id="{{ event['id'] }}"><img src="img/demo/dislike.png" alt="dislike" title="dislike"></span>
                                    </div>
                                </div>
                                <div class="event-one-text">
                                    {#{{ link_to ('event/show/' ~ event['id'], event['name']) }}#}
                                    <a href="event/show/{{ event['id'] }}"  class="name-link">{{ event['name']|striptags|escape|truncate(160) }}</a>
                                    <div class="date-list">
                                        <i class="icon-time"></i>
                                        {% if event['start_time'] is defined %}
                                            <span class="date-start">{{ date('d M Y', time(event['start_time'])) }}</span> start at
                                            <span class="date-time">{{ date('H:i', time(event['start_time'])) }}</span> {#<span class="day-title"> - tomorrow</span>#}
                                        {% endif %}
                                        {#{% if event['end_time'] is defined %}
                                            <span class="date-time">{{ event['end_time'] }}</span> <span class="day-title"> - tomorrow</span>
                                        {% endif %}#}
                                    </div>
                                    <p>{{ event['anon']|striptags|escape|truncate(350) }}</p>
                                    {#<p>web-site: <a href="#"> http://www.dpdp.com</a></p>#}
                                </div>
                            </div>
                        </div>
                        <div class="span5">
                            <div class="event-list-btn clearfix">
                                {% if event['location'] is defined %}
                                <div class="map-place">
                                    <span class="small-text">show on map</span>

                                    <div class="place-address">
                                        <p class="tooltip-text" rel="tooltip" title="{{ event['location']|striptags|escape }}">{{ event['location']|striptags|escape|truncate(20) }}</p>
                                        <button class="btn btn-primary">
                                            <i class="icon-map-marker"></i>
                                        </button>
                                    </div>
                                </div>
                                {% endif %}
                                <div class="rating clearfix">
                                    <span class="small-text">Ratting</span>
                                    <span class="rating-icon"></span>
                                    <span class="rating-text">34</span>
                                </div>
                                <button class="btn btn-more" onclick="javascript: window.location.href = 'event/show/{{ event['id'] }}';">More</button>
                            </div>
                        </div>
                    </div>
                {% else %}
                    <div style="margin-left: 50px"><i>Your friends don't have events!</i></div>
                {% endfor %}

            </div>
        </div>

    </div>

{% endblock %}