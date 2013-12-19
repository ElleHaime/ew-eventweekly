{% extends "layouts/base.volt" %}

{% block content %}

<div class="container" id="content_noBorder">
    <div class="padd_30"></div>

            <div class="row-fluid">
                <div class="span12">
                    <h3 class="title-page">{{ listName }}</h3>
                    <div class="events-result">
                        search result:
                        <span>123</span>
                        from
                        <span>2 334</span>
                    </div>
                </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class=" active-events">
                           {% for index, node in events %}

                                <div class="events-list music-category signleEventListElement" event-id="{{ node['event']['id'] }}">
                                    <div class="row-fluid ">
                                        <div class="span12">
                                            <div class="event-one clearfix">
                                                <div class="event-one-img">
                                                    <a href="event/show/{{ node['event']['id'] }}"  class="name-link">
                                                        {% if node['event']['logo'] is defined %}
		                                                    <img src="/upload/img/event/{{ node['event']['logo'] }}">
		
		                                                {% else %}
		                                                    <img src="{{ node['event']['pic_square'] }}">
		                                                {% endif %}
                                                    </a>
                                                </div>

                                                <div class="event-one-text">
                                                    <a href="/event/show/{{ node['event']['id'] }}" class="name-link">{{ node['event']['name']|striptags|escape|truncate(160) }}</a>

                                                    <div class="date-list">
                                                    	<i class="icon-time"></i>
                                                    	{% if node['event']['start_date'] != '0000-00-00 00:00:00' %}
	                                                        <span class="date-start">{{ node['event']['start_date'] }}</span>
	                                                    {% else %}
	                                                        <span class="date-start">start not defined</span>	                                                    	
	                                                    {% endif %}
                                                    </div>
                                                    <p>
                                                        {{ node['event']['description']|striptags|escape|truncate(350) }}
                                                    </p>

                                                    <div class="plans-box clearfix">
                                                    	{% if list_type != 'like' %}
                                                        	<button class="btn eventLikeBtn" data-status="1" data-id="{{ node['event']['id'] }}">Like</button> 
                                                        {% endif %}
                                                        <button class="btn eventLikeBtn" data-status="0" data-id="{{ node['event']['id'] }}">Don`t like</button>
                                                    </div>
                                                </div>
                                                
												{% if node['venue'] is defined %}                                                
	                                                <div class="event-list-btn clearfix">
	                                                    <div class=" place-address">
	                                                        <span>
	                                                            {{ node['venue']|striptags|escape }}
	                                                        </span>
	                                                    </div>
	                                                </div>
                                                {% else %}
                                                	<div class="event-list-btn clearfix">
                                                		<div class=" place-address">
                                                        	<span>
                                                            	{{ node['location']['alias'] }}
                                                        	</span>
                                                    	</div>
                                                    </div>
												{% endif %}                                                
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
            </div>
        </div>
	</div>
{% endblock %}