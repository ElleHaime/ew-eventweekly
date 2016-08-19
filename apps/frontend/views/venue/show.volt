{% extends "layouts/base_new.volt" %}

{% block content %}
<section id="content" class="container venue-page">
	<h2 class="venue-page-title">{{ venue.name }}</h2>
	{% if venue.address is defined %}
		<div style="display:none;visibility:hidden;" id="map_info" info="{{ venue.address }}"></div>
	{% else %}
		<div style="display:none;visibility:hidden;" id="map_info" info="{{ venue.name }}"></div>
	{% endif %}
	
	<div class="venue-page-place">
		{% if cover is defined %}
        	<img src="{{ checkCover(venue, cover.image, 'venue') }}" alt="{{ venue.name }}" class="venue-page-place-img">
        {% else %}
        	<img src="/img/logo200.png" alt="{{ venue.name }}" class="venue-page-place-img">
        {% endif %}
	</div>

	<div class="venue-page-location">
		<div class="venue-page-location-address col-lg-8 col-md-9 col-sm-12 col-xs-12">

			<div class="venue-page-location-address-contact">
				<div class="venue-page-location-address-contact-img-wrp">
					<img src="{{ checkLogo(venue, 'venue') }}" alt="{{ venue.name }}" class="venue-page-location-address-contact-img">
				</div>
				
				<div class="venue-page-location-address-contact-text">
					<div class="venue-page-location-address-contact-text-item">
						{% if venue.location is defined %}
							<i class="fa fa-map-marker"></i>{{ venue.location.alias|striptags }}
							{% if venue.address is defined %}
								, {{ venue.address }}
							{% endif %}	
						{% endif %}
					</div>
					
					<div class="venue-page-location-address-contact-text-item is-middle">
						{% if venue.category|length %}
							<i class="fa fa-tag"></i>
							{% for cat in venue.category %}
									<a href="">{{ cat.name }}</a> 
							{% endfor %}
							
							{% if venue.tag|length %}
								{% for tagItem in venue.tag %} , {{ tagItem.name }}{% endfor %}
							{% endif %}
						{% endif %}
					</div>
					
					<div class="venue-page-location-address-contact-text-item">
						{% if venue.site is defined %}
							<i class="fa fa-globe"></i>
	                        Official web-site: <a href="http://{{ venue.site }}">{{ venue.site }}</a><br>
						{% endif %}
					</div>
					
					{#<div class="venue-page-location-address-contact-text-item">
						{% if venue.fb_url is defined %}
							<i class="fa fa-globe"></i>
	                        Facebook page: <a href="{{ venue.fb_url }}">{{ venue.fb_url }}</a>
						{% endif %}
					</div> #}
				</div>
			</div>

			<div class="venue-page-location-address-social">
				<a href="" class="venue-page-location-address-social-link fa fa-envelope">Subscribe</a>
				<a href="" class="venue-page-location-address-social-link fa fa-share-alt">Share</a>
				<a href="" class="venue-page-location-address-social-link fa fa-star">Add to favorites</a>
			</div>
		</div>

		<div class="venue-page-location-map col-lg-4 col-md-3 col-sm-12 col-xs-12">
			{% if venue.latitude is defined and venue.longitude is defined %}
				<div class="map">
					<div style="height:200px;width:390px;" id="map_canvas" latitude="{{ venue.latitude }}" longitude="{{ venue.longitude }}"></div>
				</div>
			{% endif %}
		</div>
	</div>
	
	<div class="venue-page-tabs">
		<ul class="nav nav-tabs venue-page-tabs-list" role="tablist" id="myTab">
			{% if gallery is defined %}
				<li class="{% if activeTab == 'gallery'%}active{% endif %} venue-page-tabs-list-item">
					<a href="#home"
	                       class="active venue-page-tabs-list-item-link"
	                       role="tab"
	                       data-toggle="tab">Gallery</a>
				</li>
			{% endif %}
			
			{%  if events is defined %}
				<li class="{% if activeTab == 'events'%}active{% endif %} venue-page-tabs-list-item">
					<a href="#profile"
	                       class="venue-page-tabs-list-item-link"
	                       role="tab"
	                       data-toggle="tab">Upcoming events</a>
				</li>
			{% endif %}
			
			<li class="{% if activeTab == 'profile'%}active{% endif %} venue-page-tabs-list-item">
				<a href="#messages"
                       class="venue-page-tabs-list-item-link"
                       role="tab"
                       data-toggle="tab">Venue features</a>
			</li>
		</ul>
		
		
		<div class="tab-content venue-page-tabs-content">
			{% if gallery is defined %}
				<div class="tab-pane {% if activeTab == 'gallery'%}active{% endif %} venue-page-tabs-content-item" id="home">
					{% for item in gallery %}
						<img src="/upload/img/venue/{{ venue.location_id }}/{{ venue.id }}/gallery/{{ item.image }}" alt="{{ venue.name }}" class="venue-page-tabs-content-item-img">
					{% endfor %}	
				</div>
			{% endif %}
			
			{% if events is defined %}
				<div class="tab-pane {% if activeTab == 'events'%}active{% endif %} venue-page-tabs-content-item" id="profile">
					<div class="upcoming-events">
					
						{% for item in events %}
							<div class="b-list-of-events-l__item">
							
								<div class="b-list-of-events-l__picture pure-u-1-4">
									<a href="/{{ toSlugUri(item.name) }}-{{ item.id }}">
										{% if item.logo is defined %}
											<img src="{{ checkLogo(item, 'event') }}" alt="{{ item.name }}" class="lazy" data-original="{{ checkLogo(item, 'event') }}">
										{% else %}
											<img src="/img/logo200.png" alt="{{ item.name }}" class="lazy" data-original="/img/logo200.png">
										{% endif %} 
									</a>
                            	</div>
                            	
                            	<div class="b-list-of-events-l__info pure-u-3-4">
	                                <h2 class="b-list-of-events-l__title">
	                                    <a href="/{{ toSlugUri(item.name) }}-{{ item.id }}">{{ item.name }}</a>
	                                </h2>
	
	                                <div class="b-list-of-events-l__date">
	                                    {% if item.start_date != '0000-00-00' %}
											<time>{{ dateToFormat(item.start_date, '%e %B %Y') }}
												{% if item.end_date is defined and item.end_date != '0000-00-00' and dateToFormat(item.end_date, '%d %b %Y') != dateToFormat(item.start_date, '%d %b %Y') %}
		                                                 	- {{ dateToFormat(item.end_date, '%e %B %Y') }}
												{% endif %}
											</time>
										{% endif %}
	                                </div>
	
	                                <div class="b-list-of-events-l__description">
	                                    <p>{{ item.description|striptags|escape|truncate(250) }}</p>
	                                </div>
	
	                                <div class="footer">
	                                	{# <div class="footer__item"><i class="fa fa-ticket"></i> Tickets: $100-$200</div> #}
	                                    {# <div class="footer__item"><i class="fa fa-retweet"></i> Weekly event</div> #}
	                                </div>
	
	                                <div class="actions">
	                                	{% if event.tickets_url != '' %}
                                    		<a href="{{ event.tickets_url }}" class="ew-button"><i class="fa fa-ticket"></i> Buy ticket</a>
										{% endif %}	                                    
	                                    <a href="#" class="ew-button"><i class="fa fa-calendar"></i> Add to calendar</a>
	                                    <a href="#" class="ew-button"><i class="fa fa-share-alt"></i> Share</a>
	                                </div>
	                            </div>
	                            
							</div>
						{% endfor %}
						
					</div>
				</div>
			{% endif %}
			
			<div class="tab-pane {% if activeTab == 'profile'%}active{% endif %} venue-page-tabs-content-item" id="messages">
				<div class="venue-page-tabs-content-item-venue-features">
					<div class="venue-page-tabs-content-item-venue-features-list">
                        <div class="venue-page-tabs-content-item-venue-features-list-item row">
                            <p class="venue-page-tabs-content-item-venue-features-list-item-caption col-md-2 col-sm-2 col-xs-5">
                                Venue features:
                            </p>
                            <div class="venue-page-tabs-content-item-venue-features-list-item-icon col-md-10 col-sm-10 col-xs-7">
                            	
                            </div>
                        </div>                            
					</div>
					
					<div class="venue-page-tabs-content-item-venue-features-list row">
						{% for key, item in profileBlockLeft %}
							{% if !(venue|getAttribute(item) is empty) %}
								<div class="col-md-6 col-xs-12">
									<div class="venue-page-tabs-content-item-venue-features-list-item row">
										<p class="venue-page-tabs-content-item-venue-features-list-item-caption col-md-4 col-sm-2 col-xs-5">{{ item|capitalize }}</p>
										<p class="col-md-8 col-sm-10 col-xs-7">
											{% if key == 'worktime ' %}
												 {% for dayVal, timeVal in venue|getAttribute(item) %}
												 	{{ dayVal }}: {{ timeVal }}<br />
												 {% endfor %}
											{% else %}
												{% if checkArray(venue|getAttribute(item)) %}
													{% for val in venue|getAttribute(item) %}
														{{ val }}{% if not loop.last %}, {% endif %}
													{% endfor %}
												{% else %}
													{{ venue|getAttribute(item) }}
												{% endif %} 
											{% endif %}
										</p>
									</div>
								</div>
							{% endif %}
						{% endfor %}
					</div>
					
					<div class="venue-page-tabs-content-item-venue-features-list">
						<div class="venue-page-tabs-content-item-venue-features-list-item row">
							{% if !(venue.intro is empty) %}
								<p class="venue-page-tabs-content-item-venue-features-list-item-caption col-md-2 col-sm-2 col-xs-5">
                                    About:
                                </p>
                                <p class="col-md-10 col-sm-10 col-xs-7">{{ venue.intro }}</p>
							{% endif %}
							
							{% if !(venue.description is empty) %}
								<p class="venue-page-tabs-content-item-venue-features-list-item-caption col-md-2 col-sm-2 col-xs-5"></p>
                                <p class="col-md-10 col-sm-10 col-xs-7">{{ venue.description }} </p>
                                {% if venue.fb_url is defined %}
                                	<p class="venue-page-tabs-content-item-venue-features-list-item-caption col-md-2 col-sm-2 col-xs-5">
										<i class="fa fa-facebook"></i>
				                    </p>
				                	<p class="col-md-10 col-sm-10 col-xs-7"> <a href="{{ venue.fb_url }}">{{ venue.fb_url }}</a></p>    
								{% endif %}
							{% endif %}
						</div>
					</div>
					
				</div>
			</div>
		</div>
		
	</div>
</section>
{% endblock %}