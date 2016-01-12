{% extends "layouts/base_new.volt" %}

{% block content %}
			<section id="content" class="container page page-main">

			 {% if paidEvents is defined %}
			 		<div class="header">
						<h2 class="header__title">
							<strong>What’s on in {{ location.city }}</strong>
							<span class="divider"></span> 
							<a style="text-decoration:none;">Featured events</a>
						</h2>
	
						<a href="/{{ location.city|lower }}" class="header__link-show-more">Show more what’s on in {{ location.city }}</a>
					</div>
	
					<div class="clearfix"></div>
					
					<div class="b-popular-events-slider">
						<div class="js-main-popular-events-slider">
							{% for index, event in paidEvents %}
								<!-- start slider item -->
								<div class="b-popular-events-slider__slide b-slide js-main-popular-events-slider-slide">
		
									<div class="b-slide__picture">
										{% if event.cover is defined %}
											<img src="{{ checkCover(event.cover) }}" alt="{{ event.name }}">
										{% else %}
											<img src="{{ checkLogo(event) }}" alt="{{ event.name }}">
										{% endif%}
									</div>
									
									<div class="b-slide__info">
										<h2 class="b-slide__title">
											<a href="/{{ toSlugUri(event.name) }}-{{ event.id }}">{{ event.name }}</a>
										</h2>
		
										<div class="b-slide__description">
											<p>
												{% if event.start_date != '0000-00-00' %}
													<time datetime="2014-09-21T22:00+00:00">{{ dateToFormat(event.start_date, '%d %b %Y') }}
													{% if dateToFormat(event.start_date, '%R') != '00:00' %}, {{ dateToFormat(event.start_date, '%R') }}{% endif %}</time>
												{% endif %}
												{% if event.venue.name is defined %}
													- {{ event.venue.name|striptags }}
												{% endif %}
											</p>
										</div>
										
										<a href="/{{ toSlugUri(event.name) }}-{{ event.id }}" class="b-slide__button-detail">Details →</a>
		
									</div>
		
								</div>
								<!-- end slider item -->
							{% endfor %}						
						</div>
	
						<!-- swiper dots -->
						<div class="b-popular-events-slider__dots js-main-popular-events-slider-dots"></div>
	
					</div>
				</div>
			  {% endif %}

			  {% if featuredEvents is defined %}
				<div class="list-of-events col-3 container">
	
					<div class="header">
						<h2 class="header__title">
							<strong>What’s on in {{ location.city }}</strong>
							<span class="divider"></span> 
							<a style="text-decoration:none;">Featured events</a>
						</h2>
	
						<a href="/{{ location.city|lower }}" class="header__link-show-more">Show more what’s on in {{ location.city }}</a>
					</div>
	
					<div class="clearfix"></div>
	
					<div class="list-of-events__container featured" id="list-of-events-featured">
						<a class="b-gallery__arrow b-gallery__arrow--prev js-b-gallery-arrow-prev" id="list-of-events-featured-prev">
		                	<i class="fa fa-chevron-left"></i>
		                </a>
		                <a class="b-gallery__arrow b-gallery__arrow--next js-b-gallery-arrow-next" id="list-of-events-featured-next">
		                	<i class="fa fa-chevron-right"></i>
		                </a>
					{% for index, event in featuredEvents %}
						<!-- item start -->
							<div class="list-of-events__item pure-u-1-3">
								<div class="list-of-events__picture">
									<a href="/{{ toSlugUri(event.name) }}-{{ event.id }}">
										{% if event.logo is defined %}
											<img src="{{ checkLogo(event) }}" alt="{{ event.name }}" class="lazy" data-original="{{ checkLogo(event) }}">
										{% elseif event.cover is defined %}
											<img src="{{ checkCover(event.cover) }}" alt="{{ event.name }}" class="lazy" data-original="{{ checkCover(event.cover) }}">
										{% else %}
											<img src="/img/logo200.png" alt="{{ event.name }}" class="lazy" data-original="/upload/img/logo200.png">
										{% endif %}
									</a>
								</div>				
		
								<div class="list-of-events__info">
									<h3 class="list-of-events__title">
										<a href="/{{ toSlugUri(event.name) }}-{{ event.id }}">{{ event.name }}</a>
									</h3>
		
									<div class="list-of-events__description">
										<p>
											{% if event.start_date != '0000-00-00' %}
												<time datetime="2014-09-21T22:00+00:00">{{ dateToFormat(event.start_date, '%d %b %Y') }}
												{% if dateToFormat(event.start_date, '%R') != '00:00' %}, {{ dateToFormat(event.start_date, '%R') }}{% endif %}</time>
											{% endif %}
											{% if event.venue.name is defined %}
												- {{ event.venue.name|striptags }}
											{% endif %}
										</p>
									</div>
								</div>
							</div>
						<!-- item end -->
					{% endfor %}
					</div>
					<div class="clearfix"></div>
	
				</div>
			{% endif %}



		  {% if trendingEvents is defined %}
			<div class="list-of-events col-4 container">

				<div class="header">
					<h2 class="header__title">
						<strong>What’s on in {{ location.city }}</strong>
						<span class="divider"></span> 
						<a style="text-decoration:none;">Trending events</a> 
					</h2>

					<a href="/{{ location.city|lower }}/trending" class="header__link-show-more">Trending events in {{ location.city }}</a>
				</div>

				<div class="clearfix"></div>

				<div class="list-of-events__container trending" id="list-of-events-trending">
					<a class="b-gallery__arrow b-gallery__arrow--prev js-b-gallery-arrow-prev" id="list-of-events-trending-prev">
	                	<i class="fa fa-chevron-left"></i>
	                </a>
	                <a class="b-gallery__arrow b-gallery__arrow--next js-b-gallery-arrow-next" id="list-of-events-trending-next">
	                	<i class="fa fa-chevron-right"></i>
	                </a>
	                
	                {% for index, event in trendingEvents %}
						<!-- item start -->
							<div class="list-of-events__item pure-u-1-4">
								<div class="list-of-events__picture">
									<a href="/{{ toSlugUri(event.name) }}-{{ event.id }}">
										{% if event.logo is defined %}
											<img src="{{ checkLogo(event) }}" alt="{{ event.name }}" class="lazy" data-original="{{ checkLogo(event) }}">
										{% elseif event.cover is defined %}
											<img src="{{ checkCover(event.cover) }}" alt="{{ event.name }}" class="lazy" data-original="{{ checkCover(event.cover) }}">
										{% else %}
											<img src="/img/logo200.png" alt="{{ event.name }}" class="lazy" data-original="/upload/img/logo200.png">
										{% endif %}
									</a>
								</div>				
		
								<div class="list-of-events__info">
									<h3 class="list-of-events__title">
										<a href="/{{ toSlugUri(event.name) }}-{{ event.id }}">{{ event.name }}</a>
									</h3>
		
									<div class="list-of-events__description">
										<p>
											{% if event.start_date != '0000-00-00' %}
												<time datetime="2014-09-21T22:00+00:00">{{ dateToFormat(event.start_date, '%d %b %Y') }}
												{% if dateToFormat(event.start_date, '%R') != '00:00' %}, {{ dateToFormat(event.start_date, '%R') }}{% endif %}</time>
											{% endif %}
											{% if event.venue.name is defined %}
												- {{ event.venue.name|striptags }}
											{% endif %}
										</p>
									</div>
								</div>
							</div>
						<!-- item end -->
					{% endfor %}
				</div>
			{% endif %}

			<div class="clearfix"></div>

		</section>

	    <input type="hidden" id="trending_events" value="{% if trendingEvents is defined %}1{% else %}0{%endif%}">
		<input type="hidden" id="featured_simple_events" value="{% if featuredEvents is defined %}1{% else %}0{%endif%}">
		<input type="hidden" id="featured_paid_events" value="{% if paidEvents is defined %}1{% else %}0{%endif%}">
		
		<!-- <div class="ew-filter-link">
			<a href="#" class="Show Filter">Show Filter</a>	
		</div> -->
{% endblock %}