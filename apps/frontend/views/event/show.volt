{% extends "layouts/base.volt" %}

{% block content %}
{% if event['categories']|length %}
    <div class="top-line {{ event['categories'][0]['key'] }}-color">
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span12">
                    <div class="event-title ">
                        <span>{{ event['categories'][0]['name'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endif %}

<div class="container" id="content_noBorder">

    <div class="row-fluid ">
        <div class="span12">
            <div class="event-list_i">
                <div class="events-list-content">
                    <div class="padd_30"></div>
                    <div class="events-list events-list_nbr {% if event['categories']|length%} {{ event['categories'][0]['key'] }}-category" {% endif %}>
                        <div class="row-fluid ">
                            <div class="span12">
                                <div class="event-one clearfix">
                                    <div class="event-one-img" id="current_event_id" event="{{ event['id'] }}">
                                        {% if event['logo'] is defined %}
                                            <a href="/event/show/{{ event['id'] }}">
                                                <img src="/upload/img/event/{{ event['logo'] }}">
                                            </a>
                                        {% endif %}
                                    </div>

                                    <div class="event-one-text">

                                        <h4 class="name-link">{{ event['name'] }}</h4>

                                        <div class="date-list">
                                            <i class="icon-time"></i>
                                            <span class="date-start">20 Aug 2013</span> start at
                                            <span class="date-time">20:43</span> <span class="day-title"></span>
                                        </div>

                                        {{ event['description']|nl2br }}

                                        {% if event['answer'] is defined %}  
                                            {% if event['answer'] != 3 %} 
                                                <div class="plans-box clearfix">
                                                    {% if not (event['answer'] is defined) %}
                                                        <span>So, whats your plan?</span>
                                                    {% endif %}
                                                    <div class="btn-hide clearfix">
                                                        <div class="event-site clearfix">
                                                            {% if not (event['answer'] is defined) %}
                                                                <button class="btn" id="event-join">I`m going!</button>
                                                                <button class="btn" id="event-maybe">I`m interested!</button>
                                                                <button class="btn" id="event-decline">Don`t like</button>
                                                            {% else %}
                                                                {% if event['answer'] == 1 %}
                                                                    <button class="btn" id="event-join" disabled = true>I`m going!</button>
                                                                {% endif %}

                                                                {% if event['answer'] == 2 %}
                                                                    <button class="btn" id="event-maybe" disabled = true>I`m interested!</button>
                                                                {% endif %}
                                                            {% endif %}
                                                        </div>
                                                    </div>
                                                </div>
                                            {% endif %}
                                        {% endif %}
                                    </div>
                                    <div class="event-list-btn clearfix">
                                        {% if event['venue'] is defined %}
                                            <div class=" place-address">
                                                <span>{{ event['venue']|striptags|escape }}</span>
                                            </div>
                                        {% else %}
                                            {% if event['location'] is defined %}
                                                <div class=" place-address">
                                                    <span>{{ event['location']|striptags|escape }}</span>
                                                </div>
                                            {% endif %}
                                        {% endif %}
                                        
                                        <button class=" btn btn-block btn_invite" type="button">
                                            <img alt="" src="/img/demo/btn-m.png">
                                            Invite friends
                                        </button>
                                        {% if event['site'] is defined %}
                                            <div class="event-site clearfix">
                                                {% for key, val in event['site'] %}
                                                    <p>web-site : <a href="val" target="_blank">{{ val }}</a></p>
                                                {% endfor %}
                                            </div>
                                        {% endif %}
                                        <div class="event-list-category">

	                                        {% if event['categories']|length %}
	                    						{% for index, node in event['categories'] %}
	                    						    <span class="category-title">{{ node['name'] }}</span>
	                    						{% endfor %}
	                    					{% else %}
												 <span class="btn uncategorized_label" style="padding: 5px 47px; min-height: 0;">Uncategorized</span>
						                        <span class="btn" id="suggestCategoryBtn" style="padding: 5px 10px; min-height: 0;" title="Suggest Category">?</span>
						                        <ul id="suggestCategoriesBlock"  class="select-category">
						                        {% for index, node in categories %}
						                            <li><a href="/suggest-event-category/{{ event['id'] }}/{{ node['id'] }}" style="color: #ffffff; display: block">{{ node['name'] }}</a></li>
						                        {% endfor %}
						                        </ul>	                    					
	                    					{% endif %}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {% include 'layouts/sharebar.volt' %}

                    <div class="row-fluid">
                        <div class="span12">
                            <div class="comment-box">
                                <h2>Leave comments</h2>
                                <fb:comments href="http://events.apppicker.com/event/show/{{ event['id'] }}"></fb:comments>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{% endblock %}
