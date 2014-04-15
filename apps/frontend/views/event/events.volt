{% extends "layouts/base.volt" %}

{% block content %}

    <div class="container" id="content_noBorder">
        <div class="row-fluid">
            <div class="span12">
                <div class="padd_30"></div>
                <div class="active-events">
                    <div class="row-fluid">
                        <div class="span12">
                            <h3 class="title-page">New events</h3>
                        </div>
                    </div>
                    {% if events is defined %}
                        {% for event in events %}
                            {% set disabled = '' %}
                            {% if likedEventsIds is defined %}
                                {% for likedEventsId in likedEventsIds %}
                                    {% if likedEventsId == event['id'] %}
                                        {% set disabled = 'disabled' %}
                                    {% endif %}
                                {% endfor %}
                            {% endif %}
                            <div class="events-list {{ event['category'][0]['key'] }}-category signleEventListElement" event-id="{{ event['id'] }}">
                                <div class="row-fluid ">
                                    <div class="span12">
                                        <div class="event-one clearfix">
                                            <div class="event-one-img">
                                                <a href="/{{ toSlugUri(event['name']) }}-{{ event['id'] }}">
                                                    {% if event['logo'] is defined %}
                                                        {% if event['logo'] is empty %}
                                                            {% set pic = defaultEventLogo %}
                                                        {% else %}
                                                            {% set pic = '/upload/img/event/'~event['id']~'/'~event['logo'] %}
                                                        {% endif %}
                                                        <img src="{{ pic }}" >
                                                    {% else %}
                                                        <img src="{{ event['pic_big'] }}">
                                                    {% endif %}
                                                </a>
                                            </div>

                                            <div class="event-one-text">
                                                <a href="/{{ toSlugUri(event['name']) }}-{{ event['id'] }}" class="name-link">{{ event['name']|striptags|escape|truncate(160) }}</a>

                                                <div class="date-list">
                                                    {% if event['start_date'] != '0000-00-00' %}
                                                        <i class="icon-time"></i>
                                                        <span class="date-start">{{ dateToFormat(event['start_date'], '%d %b %Y') }}</span>
                                                        {% if dateToFormat(event['start_date'], '%R') != '00:00' %}
                                                            starts at
                                                            <span class="date-time">{{ dateToFormat(event['start_date'], '%R') }}</span>
                                                        {% endif %}
                                                    {% endif %}
                                                    {#{% if event['start_date_nice'] != '0000-00-00' %}
                                                        <i class="icon-time"></i>
                                                        <span class="date-start">{{ event['start_date_nice'] }}</span>
                                                        {% if event['start_time'] != '00:00' %}
                                                            starts at
                                                            <span class="date-time">{{ event['start_time'] }}</span>
                                                        {% endif %}

                                                    {% endif %}#}
                                                </div>
                                                <p>
                                                    {{ event['description']|striptags|escape|truncate(350) }}
                                                    <a href="/{{ toSlugUri(event['name']) }}-{{ event['id'] }}">Read more</a>
                                                </p>

                                                <div class="plans-box clearfix">
                                                    <button class="btn eventLikeBtn" data-status="1" data-id="{{ event['id'] }}" {{ disabled }}>Like{% if disabled == 'disabled' %}d{% endif %}</button>
                                                    <button class="btn eventDislikeBtn" data-status="0" data-id="{{ event['id'] }}">Don't like</button>
                                                </div>

                                                {% set eVenue = 'Undefined place' %}
                                                {% if event['venue']['address'] is empty %}
                                                    {% if event['location']['city'] is empty %}
                                                        {% set eVenue = 'Undefined place' %}
                                                    {% else %}
                                                        {% set eVenue = event['location']['city'] %}
                                                    {% endif %}
                                                {% else %}
                                                    {% if event['location']['city'] is defined %}
                                                        {% set eVenue = event['location']['city']~', '~event['venue']['name']~', '~event['venue']['address'] %}
                                                    {% else %}
                                                        {% set eVenue = event['venue']['name']~' '~event['venue']['address'] %}
                                                    {% endif %}
                                                {% endif %}
                                                <div class="event-list-btn clearfix">
                                                    <div class=" place-address tooltip-text"  data-original-title="{{ eVenue }}" title="" rel="tooltip">
                                                        <span>
                                                            {% if event['venue']['address'] is empty %}
                                                                {% if event['location']['alias'] is empty %}
                                                                    Undefined place
                                                                {% else %}
                                                                    {{ event['location']['alias'] }}
                                                                {% endif %}
                                                            {% else %}
                                                                {{ event['venue']['address'] }}
                                                            {% endif %}
                                                        </span>
                                                    </div>
                                                    {% if event.site is defined %}
                                                        <div class="event-site clearfix">
                                                            <p>web-site : <a href="#">http://www.dpdp.com</a></p>
                                                        </div>
                                                    {% endif %}

                                                    {% if event['eid'] is defined %}
                                                        <div class="event-site clearfix">
                                                            <a target="_blank" href="https://www.facebook.com/events/{{ event['eid'] }}">Facebook link</a>
                                                        </div>
                                                    {% endif %}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {% endfor %}
                    {% else %}

                        <div   class="no-list"><i>No events found</i></div>

                    {% endif %}
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>

{% endblock %}

