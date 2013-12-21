{% extends "layouts/base.volt" %}

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
                            <h3 class="title-page">{{ listTitle }}</h3>
                            {#<div class="events-result">
                                search result:
                                <span>123</span>
                                from
                                <span>2 334</span>
                            </div>#}
                        </div>
                    </div>
                    {% for node in result %}

                        <div class="events-list  music-category signleEventListElement" event-id="{{ node.event.id }}">
                            <div class="row-fluid ">
                                <div class="span12">
                                    <div class="event-one clearfix">
                                        <div class="event-one-img">
                                            <a href="event/show/{{ node.event.id }}"  class="name-link">
                                                {% if node.event.logo is defined %}
                                                    <img src="/upload/img/event/{{ node.event.logo }}" width="132px" height ="132px">
                                                {% else %}
                                                    <img src="{{ node.event.pic_square }}">
                                                {% endif %}
                                            </a>
                                        </div>

                                        <div class="event-one-text">
                                            <a href="event/show/{{ node.event.id }}" class="name-link">{{ node.event.name|striptags|escape|truncate(160) }}</a>

                                            <div class="date-list">
                                                <i class="icon-time"></i>
                                                <span class="date-start">{{ node.event.start_date_nice }}</span> start at
                                                <span class="date-time">{{ node.event.start_time }}</span>
                                            </div>
                                            <p>
                                                {{ node.event.description|striptags|escape|truncate(350) }}
                                            </p>

                                            <div class="plans-box clearfix">
                                                <button class="btn eventLikeBtn" data-status="1" data-id="{{ node.event.id }}">Like</button>
                                                <button class="btn eventDislikeBtn" data-status="0" data-id="{{ node.event.id }}">Don`t like</button>
                                            </div>
                                        </div>
                                        <div class="event-list-btn clearfix">
                                            <div class=" place-address">
                                                        <span>
                                                            {% if node.venue.address is empty %}
                                                                {% if node.location.city is empty %}
                                                                    Undefined place
                                                                {% else %}
                                                                    {{ node.location.city }}
                                                                {% endif %}
                                                            {% else %}
                                                                {% if node.location.city %}
                                                                    {{ node.location.city }}, {{ node.venue.name }}, {{ node.venue.address }}
                                                                {% else %}
                                                                    {{ node.venue.name }} {{ node.venue.address }}
                                                                {% endif %}
                                                            {% endif %}
                                                        </span>
                                            </div>
                                            {% if node.site.url %}
                                                <div class="event-site clearfix">
                                                    <p>web-site : <a href="{{ node.event.site.url }}">{{ node.event.site.url }}</a></p>
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


{% endblock %}
