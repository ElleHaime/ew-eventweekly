{% extends "layouts/base.volt" %}

{% block content %}
    <div class="padd_70"></div>

    <div class="container content_noBorder">
        <div class="row-fluid">
            <div class="span12">
                <form action="/search" method="post">

                    <label>By Title</label>
                    {{ form.render('title') }}

                    <label>By Location</label>
                    {{ form.render('location') }}

                    <label>From Date</label>
                    {{ form.render('start_date') }}

                    <label>To Date</label>
                    {{ form.render('end_date') }}

                    {% for index, node in categories %}
                        <label for="cat{{ index }}">
                            {{ check_field('category[]', 'value': node['id'], 'id': 'cat'~index) }} - {{ node['name'] }}
                        </label>
                    {% endfor %}

                    <input type="submit" value="Search"/>
                </form>
            </div>
        </div>

        <div class="row-fluid">
            {% if result is defined %}
                <div class="span12">
                    <h3 class="title-page">Search results:</h3>

                    {% for index, node in result %}
                        <div class="row-fluid events-list">
                            <div class="span7">
                                <div class="event-one clearfix">
                                    <div class="event-one-img">
                                        <div class="img-box">
                                            <a href="#">
                                                {% if node['event']['logo'] is defined %}
                                                    <img src="/upload/img/event/{{ node['event']['logo'] }}">

                                                {% else %}
                                                    <img src="{{ node['event']['pic_square'] }}">
                                                {% endif %}
                                            </a>
                                        </div>

                                        <div class="like-box clearfix">
                                            <span class=""><img src="img/demo/like.png" alt="like" title="like"> </span>
                                            <span class=""><img src="img/demo/dislike.png" alt="dislike" title="dislike"> </span>
                                        </div>
                                    </div>
                                    <div class="event-one-text">
                                        <a href="event/show/{{ node['event']['id'] }}"  class="name-link">{{ node['event']['name']|striptags|escape|truncate(160) }}</a>
                                        <div class="date-list">
                                            <i class="icon-time"></i>
                                            {% if node['event']['start_time'] is defined %}
                                                <span class="date-start">{{ node['event']['start_time'] }}</span> start at
                                            {% endif %}
                                            {% if node['event']['end_time'] is defined %}
                                                <span class="date-time">{{ node['event']['end_time'] }}</span> <span class="day-title"> - tomorrow</span>
                                            {% endif %}
                                        </div>
                                        <p>{{ node['event']['description']|striptags|escape|truncate(350) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="span5">
                                <div class="event-list-btn clearfix">
                                    {% if node['venue'] is defined %}
                                        <div class="map-place">
                                            <span class="small-text">show on map</span>
                                            <div class=" place-address">
                                                <p class="tooltip-text" rel="tooltip" title="{{ node['venue']|striptags|escape }}">{{ node['venue']|striptags|escape }}</p>
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
                                    <button class="btn btn-more" onclick="javascript: window.location.href = 'event/show/{{ node['event']['id'] }}';">More</button>
                                </div>
                            </div>
                        </div>
                    {% else %}
                        <div style="margin-left: 50px"><i>No results for your request...</i></div>
                    {% endfor %}
                </div>
            {% endif %}
        </div>
    </div>

{% endblock %}