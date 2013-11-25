{% extends "layouts/base.volt" %}

{% block content %}
    <div class="padd_70"></div>


    <div class="container content_noBorder">
        <div class="row-fluid">
            <div class="span12">
                <h3 class="title-page">Events for you</h3>

                {% for event in userEvents %}
                    <div class="row-fluid events-list">
                        <div class="span7">
                            <div class="event-one clearfix">
                                <div class="event-one-img">
                                     <div class="img-box">
                                          <a href="#">
                                           {% if event['logo'] is defined %}
                                              <img src="{{ event['logo'] }}">

                                            {% else %}
                                                <img src="{{ event['pic_square'] }}">
                                            {% endif %}
                                        </a>
                                    </div>

                                    <div class="like-box clearfix">
                                        <span class=""><img src="img/demo/like.png" alt="like" title="like"> </span>
                                        <span class=""><img src="img/demo/dislike.png" alt="dislike" title="dislike"> </span>
                                    </div>
                                </div>
                                <div class="event-one-text">
                                    {#{{ link_to ('event/show/' ~ event['id'], event['name']) }}#}
                                    <a href="event/show/{{ event['id'] }}"  class="name-link">{{ event['name']|striptags|escape|truncate(160) }}</a>
                                    <div class="date-list">
                                        <i class="icon-time"></i>
                                        {% if event['start_time'] is defined %}
                                            <span class="date-start">{{ event['start_time'] }}</span> start at
                                        {% endif %}
                                        {% if event['end_time'] is defined %}
                                            <span class="date-time">{{ event['end_time'] }}</span> <span class="day-title"> - tomorrow</span>
                                        {% endif %}
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
                                    <div class=" place-address">
                                        <p class="tooltip-text" rel="tooltip" title="{{ event['location']|striptags|escape }}">{{ event['location'] }}|striptags|escape</p>
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
                                <button class="btn btn-more">More</button>
                            </div>
                        </div>
                    </div>
                {% else %}
                    <div style="margin-left: 50px"><i>You don't have events!</i></div>
                {% endfor %}

            </div>
        </div>
        <hr/>

        {#<div class="row-fluid active-events">
            <div class="span12">
                <h3 class="title-page">Liked events</h3>

                <div class="row-fluid events-list">
                    <div class="span7">
                        <div class="event-one clearfix">
                            <div class="event-one-img">
                                <a href="event/show/{{ event['id'] }}"  class="name-link">										
                                	{% if event['logo'] is defined %}
										<img src="/upload/img/event/{{ event['logo'] }}">
									{% else %}
										<img src="{{ event['pic_square'] }}">
									{% endif %}</a>
                            </div>
                            <div class="event-one-text">
                                <a href="#"  class="name-link">Name text</a>
                                <div class="date-list">
                                    <i class="icon-time"></i>
                                    <span class="date-start">20 Aug 2013</span> start at
                                    <span class="date-time">20:43</span> <span class="day-title"> - tomorrow</span>
                                </div>
                                <p>
                                    Make the most of your time in Dublin, get out and explore the city. Dublin is rich in history and culture and prides itself on a long tradition in music, theatre and literature. Where else could you find a UNESCO Museum, or get the low down on great places to eat over a pint with the locals?
                                </p>
                                <p>web-site: <a href="#"> http://www.dpdp.com</a></p>
                                <a href="#" class="btn btn-orange"><i class="icon-man"></i> This is your public event</a>

                                <a href="#" class="btn btn-orange"><i class="icon-general"></i> This is your private event</a>
                            </div>
                        </div>
                    </div>
                    <div class="span5">
                        <div class="event-list-btn clearfix">
                            <div class="map-place">
                                <span class="small-text">show on map</span>
                                <div class=" place-address">
                                    Smock Alley Theatre
                                    <button class="btn btn-primary">
                                        <i class="icon-map-marker"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="rating clearfix">
                                <span class="small-text">Ratting</span>
                                <span class="rating-icon"></span>
                                <span class="rating-text">34</span>
                            </div>
                            <button class="btn btn-more">
                                More
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>#}
        <hr/>

        <div class="row-fluid">
            <div class="span12">
                <h3 class="title-page">Friend events</h3>

                {% for event in friendEvents %}
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
                                        <span class=""><img src="img/demo/like.png" alt="like" title="like"> </span>
                                        <span class=""><img src="img/demo/dislike.png" alt="dislike" title="dislike"> </span>
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


    {#<div class="container content_noBorder">
        <h3 style="color: #2a2a2a; font-weight:bold; padding-left:15px;">Your events:</h3>
        <div class="row">
            <div class="span12">
                {% for event in userEvents %}
                    <div class="list-event clearfix">
                        <div class="list-event-img_more">
                            <a href="#"><img src="{{ event['pic_square'] }}"></a>
                        </div>
                        <div class="list-event-text_more">
                            {{ link_to ('event/show/' ~ event['id'], event['name']) }}
                            <p>{{ event['anon']}}</p>
                            <div class="date-list">
                            {% if event['start_time'] is defined %}
                                <i class="icon-time"></i>start <span class="date-start">{{ event['start_time'] }}</span>
                            {% endif %}
                            {% if event['end_time'] is defined %}
                                finish <span class="date-finish">{{ event['end_time'] }}</span>
                            {% endif %}
                            </div>
                        </div>
                    </div>
                    <hr/>
                {% endfor %}

            </div>
        </div>
        <h3 style="color: #2a2a2a; font-weight:bold; padding-left:15px;">Friend events:</h3>
        <div class="row">
            <div class="span12">
                {% for event in friendEvents %}
                    <div class="list-event clearfix">
                        <div class="list-event-img_more">
                            <a href="#"><img src="{{ event['pic_square'] }}"></a>
                        </div>
                        <div class="list-event-text_more">
                            {{ link_to ('event/show/' ~ event['id'], event['name']) }}
                            <p>{{ event['anon']}}</p>
                            <div class="date-list">
                                {% if event['start_time'] is defined %}
                                    <i class="icon-time"></i>start <span class="date-start">{{ event['start_time'] }}</span>
                                {% endif %}
                                {% if event['end_time'] is defined %}
                                    finish <span class="date-finish">{{ event['end_time'] }}</span>
                                {% endif %}
                            </div>
                        </div>
                    </div>
                    <hr/>
                {% endfor %}
            </div>
        </div>
    </div>#}

{% endblock %}