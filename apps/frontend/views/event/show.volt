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
            <div class="event-one-box">
                        <div class="row-fluid ">
                            <div class="span12">
                                <div class="padd_30"> </div>
                                <div id="content_right">
                                    <div id="content_right_inner">
                                        <div id="content_center">
                                            <div id="content-box" style="padding-right: 20px">
                                                <h4 class="name-link">{{ event.name }}</h4>

                                                <div class="date-list">
                                                    {% if event.start_date_nice is defined  %}
                                                        <i class="icon-time"></i>
                                                        <span class="date-start">{{ event.start_date_nice }}</  span>
                                                        {% if event.start_time is defined %}
                                                            start at
                                                            <span class="date-time">{{ event.start_time }}</span> <span class="day-title"></span>
                                                        {% endif %}
                                                    {% endif %}
                                                </div>

                                                <p style="word-wrap: break-word;">{{ event.description|nl2br }}</p>


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
                                        </div>

                                        <div class="sidebar-box">
                                            <div class="event-list-btn">
                                                <div class=" clearfix">
                                                    {% if event.venue.name is defined %}
                                                        <div class=" place-address">
                                                            <span>{{ event.venue.name|striptags }}</span>
                                                        </div>
                                                    {% else %}
                                                        {% if event.location.alias is defined %}
                                                            <div class=" place-address">
                                                                <span>{{ event.location.alias|striptags }}</span>
                                                            </div>
                                                        {% endif %}
                                                    {% endif %}

                                                    <button class="btn btn-block btn_invite" type="button" id="fb-invite">
                                                        <img alt="" src="/img/demo/btn-m.png">
                                                        Invite friends
                                                    </button>
                                                    <div id="friendsBlock"></div>
                                                    <input type="button" value="Invite All" id="fb-invite-all" style="display: none"/>
                                                    {% if event.site|length %}
                                                        <div class="event-site clearfix">
                                                            {% for site in event.site %}
                                                                <p>web-site : <a href="val" target="_blank">{{ site.url }}</a></p>
                                                            {% endfor %}
                                                        </div>
                                                    {% endif %}

                                                    <div class="event-list-category">

                                                        {% if event.category|length %}

                                                            {% for cat in event.category %}
                                                                <span class=" category-title {{ cat.key }}-title {% if cat.key == 'other' %}uncategorized_label{% endif %}">{{ cat.name }}</span>
                                                            {% endfor %}

                                                            {#--------- comment tags

                                                            <div class="sub_category clearfix">
                                                               <div>
                                                                   <a href="#"><span>pop rock</span></a>
                                                               </div>
                                                               <div>
                                                                   <a href="#"><span>new album</span></a>
                                                               </div>
                                                               <div>
                                                                   <a href="#"><span>event of the year 2014</span></a>
                                                               </div>
                                                            </div>
                                                            <a href="#" class="show-all">show all tags</a>
                                                            #}

                                                            {% if event.category.getFirst().key == 'other' %}
                                                                <span class="btn btn-block suggest-btn" id="suggestCategoryBtn"title="Suggest Category">Suggest Category</span>
                                                                <ul id="suggestCategoriesBlock"  class="select-category">
                                                                    {% for index, node in categories %}
                                                                        <li><a data-catkey="{{ node['key'] }}" href="/suggest-event-category/{{ event.id }}/{{ node['id'] }}" style="color: #ffffff; display: block">{{ node['name'] }}</a></li>
                                                                    {% endfor %}
                                                                </ul>
                                                            {% endif %}
                                                        {% else %}
                                                            <span class="btn btn-block suggest-btn uncategorized_label" id="suggestCategoryBtn" >Suggest category</span>

                                                            {#<span class="btn" id="suggestCategoryBtn" title="Suggest Category">?</span>#}
                                                            <ul id="suggestCategoriesBlock"  class="select-category">
                                                                {% for index, node in categories %}
                                                                    <li><a href="/suggest-event-category/{{ event.id }}/{{ node['id'] }}" style="color: #ffffff; display: block">{{ node['name'] }}</a></li>
                                                                {% endfor %}
                                                            </ul>
                                                        {% endif %}
                                                    </div>

                                                    {% if event.fb_uid is defined %}
                                                        <div class="event-site clearfix">
                                                            <a target="_blank" href="https://www.facebook.com/events/{{ event.fb_uid }}">Facebook link</a>
                                                        </div>
                                                    {% endif %}

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="add-img">
                                    <div class="event-one-img" id="current_event_id" event="{{ event.id }}">
                                        {% if event.logo is defined %}
                                            {% set image = '/upload/img/event/' ~ event.logo %}
                                            {% if event.logo is empty %}
                                                {% set image = '/img/logo200.png' %}
                                            {% endif %}
                                        {% endif %}

                                        <a href="/event/show/{{ event.id }}">
                                            <img src="{{ image }}">
                                        </a>
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
    <fb:ref href="http://events.apppicker.com/event/show/{{ event.id }}" />
{% endblock %}


