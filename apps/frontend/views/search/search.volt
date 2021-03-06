{% extends "layouts/base_new.volt" %}

{% block content %}

    <div class="container" id="content_noBorder">
        <div class="padd_30"></div>

        {#<div class="row-fluid">
            <div class="span12">
                <form action="/search" method="post">

                    <label>By Title</label>
                    {{ form.render('title') }}

                    <label>By Location</label>
                    {{ form.render('locationSearch') }}

                    <label>From Date</label>
                    {{ form.render('start_dateSearch') }}

                    <label>To Date</label>
                    {{ form.render('end_dateSearch') }}

                    {% for index, node in categories %}
                        <label for="cat{{ index }}">
                            {{ check_field('category[]', 'value': node['id'], 'id': 'cat'~index) }} - {{ node['name'] }}
                        </label>
                    {% endfor %}

                    <input type="submit" value="Search"/>
                </form>
            </div>
        </div>#}

        <div class="row-fluid">
            <div class="span12">
                <div class="active-events">
                    <div class="row-fluid">
                        <div class="span12">
                            <h3 class="title-page">{{ listTitle }} | Total - {{ eventsTotal }}</h3>
                            {#<div class="events-result">
                                search result:
                                <span>123</span>
                                from
                                <span>2 334</span>
                            </div>#}
                        </div>
                    </div>
                    {% for event in list %}

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
											<a href="/{{ toSlugUri(event.name) }}-{{ event.id }}">
												{% if event.logo is defined %}
													<img src="{{ checkLogo(event) }}" alt="{{ event.name }}" class="lazy" data-original="{{ checkLogo(event) }}">
												{% else %}
													<img src="/img/logo200.png" alt="{{ event.name }}" class="lazy" data-original="/img/logo200.png">
												{% endif %}
											</a>
                                        </div>

                                        <div class="event-one-text">
                                            <a href="/event/{{ event.id }}" class="name-link">{{ event.name|striptags|escape|truncate(160) }}</a>

                                            <div class="date-list">
                                                <i class="icon-time"></i>
                                                {% if event.start_date != '0000-00-00' %}
                                                    <i class="icon-time"></i>
                                                    <span class="date-start">{{ dateToFormat(event.start_date, '%d %b %Y') }}</span>
                                                    {% if dateToFormat(event.start_date, '%R') != '00:00' %}
                                                        starts at
                                                        <span class="date-time">{{ dateToFormat(event.start_date, '%R') }}</span>
                                                    {% endif %}
                                                {% endif %}
                                            </div>
                                            <p>
                                                {{ event.description|striptags|escape|truncate(350) }}
                                            </p>

                                            <div class="plans-box clearfix">
                                                <button class="btn eventLikeBtn" data-status="1" data-id="{{ event.id }}">Like</button>
                                                <button class="btn eventDislikeBtn" data-status="0" data-id="{{ event.id }}">Don't like</button>
                                            </div>
                                        </div>
                                        <div class="event-list-btn clearfix">
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    {% else %}
                        <div  class="no-list"><i>No events found</i></div>
                    {% endfor %}
                </div>
            </div>
        </div>

        {% if pagination is defined %}
            <div class="row-fluid">
                <div class="span12">
                    <div class="pagination pull-right">
                        <ul>
                            {% if pagination.current > 1 %}
                                <li><a href="/search/list?{{ urlParamsPaginate }}&page={{ pagination.first }}">First</a></li>
                                <li><a href="/search/list?{{ urlParamsPaginate }}&page={{ pagination.current-1 }}">Prev</a></li>
                            {% endif %}
                            {% if pagination.current < pagination.total_pages %}
                                <li><a href="/search/list?{{ urlParamsPaginate }}&page={{ pagination.current+1 }}">Next</a></li>
                                <li><a href="/search/list?{{ urlParamsPaginate }}&page={{ pagination.total_pages }}">Last</a></li>
                            {% endif %}
                        </ul>
                    </div>
                </div>
            </div>
        {% endif %}
    </div>


{% endblock %}
