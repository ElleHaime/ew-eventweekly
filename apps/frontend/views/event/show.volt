{% extends "layouts/base_new.volt" %}

{% block content %}
		<section id="content" class="container page page-event col-2">

			<!-- breadcrumbs -->
			<!-- https://support.google.com/webmasters/answer/185417 -->
			<nav class="breadcrumbs">
				<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
					<a href="http://www.example.com/" itemprop="url" rel="index">
						<span itemprop="title">Home</span>
					</a>
				</span>

				<span class="breadcrumbs__divider">::</span>

				<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
					<a href="http://www.example.com/books" 
					itemprop="url"><span itemprop="title">Books</span></a>
				</span>

				<span class="breadcrumbs__divider">::</span>

				<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
					<a href="http://www.example.com/books/authors" 
					itemprop="url"><span itemprop="title">Authors</span></a>
				</span>

				<span class="breadcrumbs__divider">::</span>

				<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
					<a href="http://www.example.com/books/authors/stephen-king" 
					itemprop="url"><span itemprop="title">Stephen King</span></a>
				</span>

				<span class="breadcrumbs__divider">::</span>

				<strong>The Shining</strong>
			</nav>

			<h1 class="page__title">{{ event.name }}</h1>

			<div class="page-event__wrapper">
				<div class="layout__left page-event__layout-left pure-u-3-8">
					<div class="short-info">
					<!-- pic -->
						<div class="short-info__picture">
							<img src="{{ checkLogo(event) }}" alt="{{ event.name }}">
						</div>
					
					<!-- category -->
					{% if event.category|length %}
						<div class="short-info__item">
							<i class="fa fa-microphone"></i> 
								{% for cat in event.category %}
									{{ cat.name }} 
								{% endfor %}
						</div>
					{% endif %}
					
					
					<!-- date -->
					{% if event.start_date != '0000-00-00' %}
						<div class="short-info__item">
							<i class="fa fa-calendar"></i> 
							<time datetime="2014-09-21T22:00+00:00">{{ dateToFormat(event.start_date, '%d %b %Y') }}
							{% if dateToFormat(event.start_date, '%R') != '00:00' %}, {{ dateToFormat(event.start_date, '%R') }}{% endif %}</time>
						</div>
					{% endif %}
					
				
					<!-- map -->
						<div class="short-info__item">

						{% if event.venue.name is defined %}
							<ul class="contact-info">
								<li class="contact-info__item">
									<i class="fa fa-map-marker"></i>
									<div class="contact-info__text">
										<p>{{ event.venue.name|striptags }}</p>
										{% if event.venue.address is defined %}
											<p>{{ event.venue.address|striptags }}</p>
										{% endif %}
										
									</div>
								</li>
								<li class="contact-info__item">
									<i class="fa fa-envelope"></i>
									<div class="contact-info__text">dolorsitamet@lorem.com</div>
								</li>
								<li class="contact-info__item">
									<i class="fa fa-globe"></i>
									<div class="contact-info__text"><a href="#">loremipsum.com</a></div>
								</li>
							</ul>
						{% endif %}							 

							<div class="map">
								<div class="map__picture">
									<a href="#"><img src="content/Map.png" alt="Map"></a>
								</div>
								<div class="actions">
									<a href="#" class="layout__left actions__link-view">View large map</a>
									<a href="#" class="layout__right actions__link-report">Report wrong location</a>
								</div>
							</div>

							<div class="clearfix"></div>					
						</div>

					<!-- location -->
					{% if event.location.alias is defined %}
						<div class="short-info__item">
							<p>{{ event.location.alias|striptags }}</p>
						</div>
					{% endif %}
					
					<!-- actions buttons  -->
						<div class="actions">
							<div class="actions__button pure-u-1-2">
								<a href="#" class="ew-button">
									<i class="fa fa-arrow-circle-right"></i> Join
								</a>
							</div>
							<div class="actions__button pure-u-1-2">
								<a href="#" class="ew-button">
									<i class="fa fa-thumbs-o-up"></i> Like
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="layout__right page-event__layout-right pure-u-5-8">

					<div class="page-event__content">

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
								<div class="b-gallery__item js-b-gallery-slider-slide">
									<img src="content/Copan-Stairs-Front-Bar.jpg" alt="Photo 1">
								</div>
								<div class="b-gallery__item js-b-gallery-slider-slide">
									<img src="content/i7adlk.jpg" alt="Photo 2">
								</div>
								<div class="b-gallery__item js-b-gallery-slider-slide">
									<img src="content/tumblr_ld9upkqHDq1qbz91u.png" alt="Photo 3">
								</div>
							</div>
						</div>

						<div class="page-event__description">
							<p>{{ event.description|nl2br }}</p>
						</div>
					</div>

					<div class="upcoming-events">
						<h3 class="upcoming-events__title">Upcoming Events</h3>
						<!-- item -->
						<div class="b-list-of-events-l__item">
							<div class="b-list-of-events-l__picture pure-u-1-3">
								<a href="#">
									<img src="content/fb_572652532782607.jpg" alt="Big Late Fancy Ny Party">
								</a>
							</div>				
							<div class="b-list-of-events-l__info pure-u-2-3">
								<h2 class="b-list-of-events-l__title">
									<a href="#">Big Late Fancy Ny Party Big Late Fancy Ny Party Big Late Fancy Ny Party
									Big Late Fancy Ny PartyBig Late Fancy Ny Party</a>
								</h2>

								<div class="b-list-of-events-l__date">
									<time datetime="2014-09-19">September 19, 2014</time>
								</div>

								<div class="b-list-of-events-l__description">
									<p>We are hosting a monthly Love, Harmony, World Peace Meditation, 
									on the third of Saturday of each month, 24 hours of FULL DAY. 
									Visualize and prayer across the world; Meditation helps to deepen
									</p>
								</div>

								<div class="footer">
									<div class="footer__item"><i class="fa fa-ticket"></i> Tickets: $100-$200</div>
									<div class="footer__item"><i class="fa fa-retweet"></i> Weekly event</div>
								</div>

								<div class="actions">
									<a href="#" class="ew-button"><i class="fa fa-ticket"></i> Buy ticket</a>
									<a href="#" class="ew-button"><i class="fa fa-calendar"></i> Add to calendar</a>
									<a href="#" class="ew-button"><i class="fa fa-share-alt"></i> Share</a>
								</div>
							</div>

						</div>
						<!-- item end -->
						<div class="clearfix"></div>



						<!-- item -->
						<div class="b-list-of-events-l__item">
							<div class="b-list-of-events-l__picture pure-u-1-3">
								<a href="#">
									<img src="content/fb_572652532782607.jpg" alt="Big Late Fancy Ny Party">
								</a>
							</div>				
							<div class="b-list-of-events-l__info pure-u-2-3">
								<h2 class="b-list-of-events-l__title">
									<a href="#">Big Late Fancy Ny Party Big Late Fancy Ny Party Big Late Fancy Ny Party
									Big Late Fancy Ny PartyBig Late Fancy Ny Party</a>
								</h2>

								<div class="b-list-of-events-l__date">
									<time datetime="2014-09-19">September 19, 2014</time>
								</div>

								<div class="b-list-of-events-l__description">
									<p>We are hosting a monthly Love, Harmony, World Peace Meditation, 
									on the third of Saturday of each month, 24 hours of FULL DAY. 
									Visualize and prayer across the world; Meditation helps to deepen
									</p>
								</div>

								<div class="footer">
									<div class="footer__item footer__item--non-active">
										<i class="fa fa-ticket"></i> Free entry
									</div>
									<div class="footer__item footer__item--non-active">
										<i class="fa fa-retweet"></i> Non-recurring event
									</div>
								</div>

								<div class="actions">
									<a href="#" class="ew-button"><i class="fa fa-ticket"></i> Buy ticket</a>
									<a href="#" class="ew-button"><i class="fa fa-calendar"></i> Add to calendar</a>
									<a href="#" class="ew-button"><i class="fa fa-share-alt"></i> Share</a>
								</div>
							</div>

						</div>
						<!-- item end -->
						<div class="clearfix"></div>


					</div>
				</div>

				<div class="clearfix"></div>
			</div>

			<div class="b-user-widgets-container">

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

			</div>
		</section>

		<aside>
			
		</aside>
		
{% endblock %}