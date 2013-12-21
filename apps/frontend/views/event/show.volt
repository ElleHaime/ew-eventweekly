{% extends "layouts/base.volt" %}

{% block content %}
{% if event.category|length %}
	{% for cat in event.category %}
	    <div class="top-line {{ cat.key }}-color">
	        <div class="container-fluid">
	            <div class="row-fluid">
	                <div class="span12">
	                    <div class="event-title ">
	                        <span>{{ cat.name }}</span>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	   {% break %}
	{% endfor %}
{% endif %}

<div class="container" id="content_noBorder">

    <div class="row-fluid ">
        <div class="span12">
            <div class="event-list_i">
                        <div class="row-fluid ">
                            <div class="span12">
                                <div class="padd_30"></div>
                                <div class="event-one clearfix">
                                    <div class="event-one-img" id="current_event_id" event="{{ event.id }}">
                                        {% if event.logo is defined %}
                                            <a href="/event/show/{{ event.id }}">
                                                <img src="/upload/img/event/{{ event.logo }}">
                                            </a>
                                        {% endif %}
                                    </div>

                                    <div class="event-one-text">

                                        <h4 class="name-link">{{ event.name }}</h4>

                                        <div class="date-list">
                                            <i class="icon-time"></i>
                                            <span class="date-start">{{ event.start_date }}</span> start at
                                            <span class="date-time">{{ event.start_time }}</span> <span class="day-title"></span>
                                        </div>

                                        <p>{{ event.description|nl2br }}</p>


                                        <div class="btn-hide clearfix">

                                            {% if not (event.memberpart|length) %}
                                                <span>So, whats your plan?</span>
                                            {% endif %}
                                            <div class="event-site clearfix">
                                                {% if not (event.memberpart|length) %}
                                                    <button class="btn" id="event-join">I`m going!</button>
                                                    <button class="btn" id="event-maybe">I`m interested!</button>
                                                    <button class="btn" id="event-decline">Don`t like</button>
                                                {% else %}
                                                    {% if event.memberpart == 1 %}
                                                        <button class="btn" id="event-join" disabled = true>I`m going!</button>
                                                    {% endif %}

                                                    {% if event.memberpart == 2 %}
                                                        <button class="btn" id="event-maybe" disabled = true>I`m interested!</button>
                                                    {% endif %}
                                                {% endif %}
                                            </div>
                                        </div>

                                    </div>
                                    <div class="event-list-btn clearfix">
                                        {% if event.venue is defined %}
                                            <div class=" place-address">
                                                <span>{{ event.venue.name|striptags|escape }}</span>
                                            </div>
                                        {% else %}
                                            {% if event.location is defined %}
                                                <div class=" place-address">
                                                    <span>{{ event.location.alias|striptags|escape }}</span>
                                                </div>
                                            {% endif %}
                                        {% endif %}
                                        
                                        <button class="btn btn-block btn_invite" type="button" id="fb-invite">
                                            <img alt="" src="/img/demo/btn-m.png">
                                            Invite friends
                                        </button>
                                        <div id="friendsBlock"></div>
                                        {% if event.site|length %}
                                            <div class="event-site clearfix">
                                                {% for site in event.site %}
                                                    <p>web-site : <a href="val" target="_blank">{{ site.url }}</a></p>
                                                {% endfor %}
                                            </div>
                                        {% endif %}
                                        
                                        <div class="event-list-category">

	                                        {% if event.category|length %}
	                                         	<div class="event-list-category">
		                    						{% for cat in event.category %}
		                    						    <span class="category-title">{{ cat.name }}</span>
		                    						{% endfor %}
	                    						</div>
	                    					{% else %}
												<span class="btn uncategorized_label">Uncategorized</span>

						                        <span class="btn" id="suggestCategoryBtn" title="Suggest Category">?</span>
						                        <ul id="suggestCategoriesBlock"  class="select-category">
						                        {% for index, node in categories %}
						                            <li><a href="/suggest-event-category/{{ event.id }}/{{ node['id'] }}" style="color: #ffffff; display: block">{{ node['name'] }}</a></li>
						                        {% endfor %}
						                        </ul>	                    					
	                    					{% endif %}
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>

                            {% include 'layouts/sharebar.volt' %}

                            <div class="row-fluid">
                                <div class="span12">
                                    <div class="comment-box">
                                        <h2>Leave comments</h2>
                                        <fb:comments href="http://events.apppicker.com/event/show/{{ event.id }}"></fb:comments>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
        </div>
    </div>
</div>

{% endblock %}


