{% extends "layouts/base_new.volt" %}

{% block content %}
		<div style="display:none; visibility:hidden;" id="current_event_id"  event="{{ event.id }}"></div>

		<section id="content" class="container page page-event col-2">

			<!-- breadcrumbs -->
			<!-- https://support.google.com/webmasters/answer/185417 -->
			

			<h1 class="page__title">{{ event.name }}</h1>
			{% if event.address is defined %}
				<div style="display:none;visibility:hidden;" id="map_info" info="{{ event.address }}"></div>
			{% else %}
				<div style="display:none;visibility:hidden;" id="map_info" info="{{ event.name }}"></div>
			{% endif %}

			<div class="page-event__wrapper">
				<div class="layout__left page-event__layout-left pure-u-3-8">
					<div class="short-info">
					<!-- pic -->
						<div class="short-info__picture">
							{% if eventPreview is defined %}
								{% if eventPreviewLogo is defined %}
									<img src="{{ checkTmpLogo(event) }}" alt="{{ event.name }}">
								{% else %}
									<img src="{{ checkLogo(event) }}" alt="{{ event.name }}">
								{% endif %}
							{% else %}
								{% if cover is defined %}
									<img src="{{ checkCover(cover) }}" alt="{{ event.name }}">
								{% elseif event.logo is defined %}
									<img src="{{ checkLogo(event) }}" alt="{{ event.name }}">
								{% else %}
									<img src="/img/logo200.png" alt="{{ event.name }}" class="lazy" data-original="/upload/img/logo200.png">
								{% endif %}
							{% endif %}
						</div>
					
					<!-- category -->
					{% if event.category|length %}
						<div class="short-info__item">
							<i class="fa fa-tag"></i> 
								{% for cat in event.category %}
									{{ cat.name }} 
								{% endfor %}
								{% if eventTags|length %}
									{% for name in eventTags %}
	                                    , {{ name }} 
	                                {% endfor %}
	                            {% endif %}
						</div>
					{% endif %}
					
					
					<!-- date -->
					{% if event.start_date != '0000-00-00' %}
						<div class="short-info__item">
							<i class="fa fa-calendar"></i> 
							<time datetime="2014-09-21T22:00+00:00">{{ dateToFormat(event.start_date, '%d %b %Y') }}
							{% if dateToFormat(event.start_date, '%R') != '00:00' %}, {{ dateToFormat(event.start_date, '%R') }}{% endif %}</time>
							
							{% if event.end_date != '0000-00-00' %}
                                - <time datetime="2014-09-21T22:00+00:00">{{ dateToFormat(event.end_date, '%d %b %Y') }}
                                {% if dateToFormat(event.end_date, '%R') != '00:00' %}, {{ dateToFormat(event.end_date, '%R') }}{% endif %}</time>
                            {% endif %}
						</div>
					{% endif %}
					
				
					<!-- map -->
						<div class="short-info__item">

						
							<ul class="contact-info">
								{% if event.venue.name is defined %}							
									<li class="contact-info__item">
										<i class="fa fa-map-marker"></i>
										<div class="contact-info__text">
											<p>{{ event.venue.name|striptags }}, {{ event.location.alias|striptags }}</p>
											{% if event.venue.address is defined %}
												<p>{{ event.venue.address|striptags }}</p>
											{% endif %}
											
										</div>
									</li>
								{% elseif event.location.alias is defined %}
									<li class="contact-info__item">
										<i class="fa fa-map-marker"></i>
										<div class="contact-info__text">
											<p>{{ event.location.alias|striptags }}</p>
										</div>
									</li>
								{% elseif event.address is defined %}
									<li class="contact-info__item">
										<i class="fa fa-map-marker"></i>
										<div class="contact-info__text">
											<p>{{ event.address|striptags }}</p>
										</div>
									</li>
								{% endif %}
								
								{% if event.tickets_url != '' %}
									<li class="contact-info__item">
										<i class="fa fa-ticket"></i>
										{% if event.fb_uid is defined  and event.fb_uid != '' %}
											<div class="contact-info__text"><a href="{{ event.tickets_url }}" target="_blank">Buy tickets</a></div>
										{% else %}
											<div class="contact-info__text">Buy tickets {{ event.tickets_url }}</div>
										{% endif %}
									</li>
                                {% endif %}
                                
                                {% if sites|length > 0 %}
                                	<i class="fa fa-external-link"></i>
                                	{% for site in sites %}
                                		<div class="contact-info__text"><a href="{{ site.url }}" target="_blank">{{ site.url }}</a></div>
                                	{% endfor %}
                                {% endif %}
                                
								{% if event.fb_uid is defined  and event.fb_uid != '' %}
									<li class="contact-info__item">
										<i class="fa fa-facebook"></i>
										<div class="contact-info__text">
											{{ event.name }} <a target="_blank" href="https://www.facebook.com/events/{{ event.fb_uid }}"> on facebook</a>
										</div>
									</li>
								{% elseif event.eb_url is defined and event.eb_url != '' %}
									<li class="contact-info__item">
										<i class="fa fa-globe"></i>
										<div class="contact-info__text">
											{{ event.name }} <a target="_blank" href="{{ event.eb_url }}"> on eventbrite</a>
										</div>
									</li>
								{% endif %}
							</ul>
			 
							{% if event.latitude is defined %}
								<div class="map">
									<div style="height:249px" id="map_canvas" latitude="{{ event.latitude }}" longitude="{{ event.longitude }}"></div>
								</div>
							{% endif %}

							<div class="clearfix"></div>					
						</div>

					<!-- location -->
					{% if event.venue is defined and event.venue.name != '' %}
						<div class="short-info__item">
							<p>EVENT HOST: {{ event.venue.name|striptags }}</p>
						</div>
					{% endif %}
					
					{#% if event.memberStatus|length or likedEventStatus is defined %}
						<div class="short-info__item" id="member_attending">
                    		<p>
                        		<div id="status-bar-like" {% if likedEventStatus != 1 %}style="display:none;"{% endif%}>
                        			<i class="fa fa-angellist"></i>
                        			You like this event
                        		</div>
                    		</p>
						
							{% if event.memberStatus|length %} 
								{% if event.memberStatus == 1 or event.memberStatus == 2 %}
									<p><div id="status-bar-join">
										<i class="fa fa-users"></i>
	                                	You're going to this event
	                                </div></p>
	                            {% endif %}
                            {% endif %}
						</div>
					{% endif %#}

					
					<!-- actions buttons  -->
						<div class="actions">
							<div class="actions__button pure-u-1-2" id='event-decline' {% if not (event.memberStatus|length) or event.memberStatus == 3 %} style="display:none;"{% endif %}>
								<a class="ew-button">
									<i class="fa fa-minus-circle"></i> Will not go
								</a>
							</div>
							
							<div class="actions__button pure-u-1-2" id='event-join' data-event-source="/{{ toSlugUri(event.name) }}-{{ event.id }}"
											   data-image-source="{{ checkLogo(event) }}"
								{% if event.memberStatus|length and (event.memberStatus == 1 or event.memberStatus == 2) %} style="display:none;"{% endif %}>
								<a class="ew-button">
									<i class="fa fa-arrow-circle-right"></i> Join
								</a>
							</div>


							<div class="actions__button pure-u-1-2" id='event-dislike-btn' data-status="0" data-id="{{ event.id }}" 
								{% if (likedEventStatus is not defined) or ((likedEventStatus is empty) and (likedEventStatus != 1)) %}style="display:none;"{% endif %}>
								<a class="ew-button">
									<i class="fa fa-thumbs-o-down"></i> Don't like
								</a>
							</div>
							<div class="actions__button pure-u-1-2" id='event-like-btn' data-status="1" data-id="{{ event.id }}" 
								{% if (likedEventStatus is defined) and (likedEventStatus == 1) %}style="display:none;"{% endif %}>
								<a class="ew-button">
									<i class="fa fa-thumbs-o-up"></i> Like
								</a>
							</div>

						</div>
					</div>
				</div>
				<div class="layout__right page-event__layout-right pure-u-5-8">

					<div class="page-event__content">

					{% if poster is defined or flyer is defined %}
						<div class="b-gallery">
			                <!-- slider navigation arrows -->
			                <a class="b-gallery__arrow b-gallery__arrow--prev js-b-gallery-arrow-prev">
			                	<i class="fa fa-chevron-left"></i>
			                </a>
			                <a class="b-gallery__arrow b-gallery__arrow--next js-b-gallery-arrow-next">
			                	<i class="fa fa-chevron-right"></i>
			                </a>

			                <!-- slides container -->
							<div class="js-b-gallery-slider">
								{% if poster is defined %}
									{% for pimg in poster %}
										<div class="b-gallery__item js-b-gallery-slider-slide">
											<img src="/upload/img/event/{{ event.id }}/poster/{{ pimg.image }}" alt="{{ event.name }}">
										</div>
									{% endfor %}
								{% endif %}
								{% if flyer is defined %}
									{% for fimg in flyer %}
										<div class="b-gallery__item js-b-gallery-slider-slide">
											<img src="/upload/img/event/{{ event.id }}/flyer/{{ fimg.image }}" alt="{{ event.name }}">
										</div>
									{% endfor %}
								{% endif %}
							</div>
						</div>
					{% elseif eventPreviewPoster is defined or eventPreviewFlyer is defined%}
						<div class="b-gallery">
			                <a class="b-gallery__arrow b-gallery__arrow--prev js-b-gallery-arrow-prev">
			                	<i class="fa fa-chevron-left"></i>
			                </a>
			                <a class="b-gallery__arrow b-gallery__arrow--next js-b-gallery-arrow-next">
			                	<i class="fa fa-chevron-right"></i>
			                </a>

							<div class="js-b-gallery-slider">
								{% if eventPreviewPoster is defined %}
									<div class="b-gallery__item js-b-gallery-slider-slide">
										<img src="/upload/img/event/tmp/{{ previewPoster }}" alt="{{ event.name }}">
									</div>
								{% endif %}
								{% if eventPreviewFlyer is defined %}
									<div class="b-gallery__item js-b-gallery-slider-slide">
										<img src="/upload/img/event/tmp/{{ previewFlyer }}" alt="{{ event.name }}">
									</div>
								{% endif %}
							</div>
						</div>
					{% elseif eventPreviewPosterReal is defined or eventPreviewFlyerReal is defined%}
						<div class="b-gallery">
			                <a class="b-gallery__arrow b-gallery__arrow--prev js-b-gallery-arrow-prev">
			                	<i class="fa fa-chevron-left"></i>
			                </a>
			                <a class="b-gallery__arrow b-gallery__arrow--next js-b-gallery-arrow-next">
			                	<i class="fa fa-chevron-right"></i>
			                </a>

							<div class="js-b-gallery-slider">
								{% if eventPreviewPosterReal is defined %}
									<div class="b-gallery__item js-b-gallery-slider-slide">
										<img src="/upload/img/event/{{ event.id }}/poster/{{ eventPreviewPosterReal }}" alt="{{ event.name }}">
									</div>
								{% endif %}
								{% if eventPreviewFlyerReal is defined %}
									<div class="b-gallery__item js-b-gallery-slider-slide">
										<img src="/upload/img/event/{{ event.id }}/flyer/{{ eventPreviewFlyerReal }}" alt="{{ event.name }}">
									</div>
								{% endif %}
							</div>
						</div>
					{% endif %}
						<div class="page-event__description">
							<p>{{ event.description|nl2br }}</p>
						</div>
					</div>
				</div>


				<div class="clearfix"></div>
				
			</div>

			<!-- div class="b-user-widgets-container">

				<div class="b-user-widget pure-u-1-3">
					<div class="b-user-widget__wrapper">
						<h3 class="b-user-widget__title">2 events invitations</h3>
						<div class="b-user-widget__text">
							<ul class="b-user-widget__list b-user-widget__list--invitations-list">
								<li><a href="#">International comedy club</a></li>
								<li><a href="#">Oxygen</a></li>
								<li><a href="#">Lorem ipsum dolor</a></li>
								<li><a href="#">Sit amet dictum enim est</a></li>
								<li><a href="#">Lorem ipsum dolor</a></li>
								<li><a href="#">Sit amet dictum enim est</a></li>
							</ul>
						</div>
					</div>
				</div>

				<div class="b-user-widget pure-u-1-3">
					<div class="b-user-widget__wrapper">
						<h3 class="b-user-widget__title">Most popular events from friends</h3>
						<div class="b-user-widget__text">
							<ul class="b-user-widget__list b-user-widget__list--most-popular">
								<li><a href="#">International comedy club</a></li>
								<li><a href="#">Oxygen</a></li>
								<li><a href="#">Lorem ipsum dolor</a></li>
								<li><a href="#">Sit amet dictum enim est</a></li>
								<li><a href="#">Lorem ipsum dolor</a></li>
								<li><a href="#">Sit amet dictum enim est</a></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="b-user-widget pure-u-1-3">
					<div class="b-user-widget__wrapper">
						<h3 class="b-user-widget__title">Most popular events from friends</h3>
						<div class="b-user-widget__text">
							<ul class="b-user-widget__list b-user-widget__list--most-popular">
								<li><a href="#">International comedy club</a></li>
								<li><a href="#">Oxygen</a></li>
								<li><a href="#">Lorem ipsum dolor</a></li>
								<li><a href="#">Sit amet dictum enim est</a></li>
								<li><a href="#">Lorem ipsum dolor</a></li>
								<li><a href="#">Sit amet dictum enim est</a></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div -->
		</section>
		<aside>
			
		</aside>
		
{% endblock %}
