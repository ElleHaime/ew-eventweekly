{% extends "layouts/base_new.volt" %}

{% block content %}
			<section id="content" class="container page page-main">

			 {% if featuredEvents is defined %}
			 	{% if featuredEvents[0] is defined %}
					<div class="b-popular-events-slider">
						<div class="js-main-popular-events-slider">
							{% for index, event in featuredEvents[0] %}
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

			  {% if featuredEvents[1] is defined %}
				<div class="list-of-events col-3 container">
	
					<div class="header">
						<h2 class="header__title">
							<strong>What’s on in {{ location.city }}</strong>
							<span class="divider"></span> 
							<a style="text-decoration:none;">Featured events</a>
						</h2>
	
						<a href="/list" class="header__link-show-more">Show more What’s on in {{ location.city }}</a>
					</div>
	
					<div class="clearfix"></div>
	
					<div class="list-of-events__container featured" id="list-of-events-featured">
						<a class="b-gallery__arrow b-gallery__arrow--prev js-b-gallery-arrow-prev" id="list-of-events-featured-prev">
		                	<i class="fa fa-chevron-left"></i>
		                </a>
		                <a class="b-gallery__arrow b-gallery__arrow--next js-b-gallery-arrow-next" id="list-of-events-featured-next">
		                	<i class="fa fa-chevron-right"></i>
		                </a>
					{% for index, event in featuredEvents[1] %}
						<!-- item start -->
							<div class="list-of-events__item pure-u-1-3">
								<div class="list-of-events__picture">
									<img src="{{ checkLogo(event) }}" alt="{{ event.name }}">
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
		  {% endif %}


		  {% if trendingEvents is defined %}
			<div class="list-of-events col-4 container">

				<div class="header">
					<h2 class="header__title">
						<strong>What’s on in {{ location.city }}</strong>
						<span class="divider"></span> 
						<a style="text-decoration:none;">Trending events</a> 
					</h2>

					<!-- a href="/list" class="header__link-show-more"> {{ location.city }}</a -->
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
									<img src="{{ checkLogo(event) }}" alt="{{ event.name }}">
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


		<!-- <div class="ew-filter-link">
			<a href="#" class="Show Filter">Show Filter</a>	
		</div> -->
{% endblock %}