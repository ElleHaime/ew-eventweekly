{% extends "layouts/base.volt" %}

{% block content %}

    <div class="container" id="content_noBorder">
        <div class="padd_30"></div>

        <div class="row-fluid">
            <div class="span12">
                <div class="active-events">
                    <div class="row-fluid">
                        <div class="span12">
                            <h3 class="title-page">{{ listTitle|default('Event list') }}</h3>
                            {#<div class="events-result">
                                search result:
                                <span>123</span>
                                from
                                <span>2 334</span>
                            </div>#}
                        </div>
                    </div>
                    {% if list is defined %}
                        {% for event in list %}

                            {% set disabled = '' %}
                            {% if likedEventsIds is defined %}
                                {% for likedEventsId in likedEventsIds %}
                                    {% if likedEventsId == event.id %}
                                        {% set disabled = 'disabled' %}
                                    {% endif %}
                                {% endfor %}
                            {% endif %}

                            {% set catLight = 'other' %}
                            {% if primaryCategory is defined %}
                                {% for index, node in event.category %}
                                    {% if node.id == primaryCategory %}
                                        {% set catLight = node.key %}
                                    {% endif %}
                                {% endfor %}
                            {% else %}
                                {% set catLight = event.category.getFirst().key %}
                            {% endif %}
                            <div class="events-list  {{ catLight }}-category signleEventListElement" event-id="{{ event.id }}">
                                <div class="row-fluid ">
                                    <div class="span12">
                                        <div class="event-one clearfix">
                                            <div class="event-one-img">
                                                <a href="/event/{{ event.id }}-{{ toSlugUri(event.name) }}">
                                                    {% if event.logo is defined %}
                                                        {% if event.logo is empty %}
                                                            {% set pic = defaultEventLogo %}
                                                        {% else %}
                                                            {% set pic = '/upload/img/event/'~event.id~'/'~event.logo %}
                                                        {% endif %}
                                                        <img src="{{ pic }}" >
                                                    {% else %}
                                                        <img src="{{ event.pic_big }}">
                                                    {% endif %}
                                                </a>
                                            </div>

                                            <div class="event-one-text">
                                                <a href="/event/{{ event.id }}-{{ toSlugUri(event.name) }}" class="name-link">{{ event.name|striptags|escape|truncate(160) }}</a>

                                                <div class="date-list">
                                                    {% if event.start_date != '0000-00-00' %}
                                                        <i class="icon-time"></i>
                                                        <span class="date-start">{{ dateToFormat(event.start_date, '%d %b %Y') }}</span>
                                                        {% if dateToFormat(event.start_date, '%R') != '00:00' %}
                                                            start at
                                                            <span class="date-time">{{ dateToFormat(event.start_date, '%R') }}</span>
                                                        {% endif %}
                                                    {% endif %}
                                                    {#{% if event.start_date_nice is defined %}
                                                        <i class="icon-time"></i>
                                                        <span class="date-start">{{ event.start_date_nice }}</span> 
                                                        {% if event.start_time is defined %}
                                                            starts at
                                                            <span class="date-time">{{ event.start_time }}</span>
                                                        {% endif %}
                                                    {% endif %}#}
                                                </div>
                                                <p>
                                                    {{ event.description|striptags|escape|truncate(350) }}
                                                </p>

                                                {% if eventListCreatorFlag %}
                                                {% else %}
                                                <div class="plans-box clearfix">
                                                    <button class="btn eventLikeBtn" data-status="1" data-id="{{ event.id }}" {{ disabled }}>Like{% if disabled == 'disabled' %}d{% endif %}</button>
                                                    <button class="btn eventDislikeBtn" data-status="0" data-id="{{ event.id }}">Don't like</button>
                                                </div>
                                                {% endif %}

                                            <div class="event-list-btn clearfix">
                                                {% if eventListCreatorFlag %}
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
                                                        <button class="btn btn-block editEvent" id="{{ event.id }}">
                                                            <span class="btn-text">Edit</span>
                                                        </button>
                                                        <button class="btn btn-block deleteEvent" id="{{ event.id }}">
                                                            <span class="btn-text">Archive</span>
                                                        </button>
                                                    </div>
                                                {% else %}
                                                    {% set eVenue = 'Undefined place' %}
                                                    {% if event.venue.address is empty %}
                                                        {% if event.location.city is empty %}
                                                            {% set eVenue = 'Undefined place' %}
                                                        {% else %}
                                                            {% set eVenue = event.location.city %}
                                                        {% endif %}
                                                    {% else %}
                                                        {% if event.location.city %}
                                                            {% set eVenue = event.location.city~', '~event.venue.name~', '~event.venue.address %}
                                                        {% else %}
                                                            {% set eVenue = event.venue.name~' '~event.venue.address %}
                                                        {% endif %}
                                                    {% endif %}
                                                    <div class=" place-address tooltip-text"  data-original-title="{{ eVenue }}" title="" rel="tooltip">
                                                    <span>
                                                        {{ eVenue }}
                                                    </span>
                                                    </div>
                                                    {% if event.site.url is defined %}
                                                        <div class="event-site clearfix">
                                                            <p>web-site : <a href="{{ event.site.url }}">{{ event.site.url }}</a></p>
                                                        </div>
                                                    {% endif %}
                                                {% endif %}
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {% else %}
                            <div class="no-list"><i>{{ noListResult|default('No events found') }}</i></div>
                        {% endfor %}
                    {% else %}
                        <div   class="no-list"><i>{{ noListResult|default('No events found') }}</i></div>
                    {% endif %}
                </div>
            </div>
        </div>

        {% if pagination is defined %}
            <div class="row-fluid">
                <div class="span12">
                    <div class="pagination pull-right">
                        <ul>
                            {% if pagination.current > 1 %}
                                <li><a href="?page={{ pagination.first }}">First</a></li>
                                <li><a href="?page={{ pagination.current-1 }}">Prev</a></li>
                            {% endif %}
                            {% if pagination.current < pagination.total_pages %}
                                <li><a href="?page={{ pagination.current+1 }}">Next</a></li>
                                <li><a href="?page={{ pagination.total_pages }}">Last</a></li>
                            {% endif %}
                        </ul>
                    </div>
                </div>
            </div>
        {% endif %}
    </div>
{% endblock %}
