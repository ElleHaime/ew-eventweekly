<!DOCTYPE html>
<html>
<head>
    <title>EventWeekly</title>
    <meta charset="utf-8"/>
    <meta name="keywords" content=""/>
    <meta name="title" content="EventWeekly Personalised Whats On Event Listings"/>
    <meta name="description" content="Never miss an event again with our whats on event guide. Promoters list your event for free. Thousands of local and international events listed weekly."/>
	<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">  

    <link type="image/ico" href="/img/128.ico" rel="icon">

    {% if eventMetaData is defined %}
        {% if logo is defined %}
            <meta property="og:image" content="{{ logo }}"/>
        {% endif %}
            <meta property="og:title" content="{{ eventMetaData.name|escape }}"/>
            <meta property="og:description" content="{{ eventMetaData.description|escape|striptags }}"/>
    {% else %}
        {% if logo is defined %}
            <meta property="og:image" content="{{ logo }}"/>
            <meta property="og:title" content="EventWeekly"/>
        {% endif %}
    {% endif %}


    {{ stylesheet_link('/css/normalBootstrapDateTimepicker.min.css') }}

    {{ stylesheet_link('/_new-layout-eventweekly/css/style.css') }}
    {{ stylesheet_link('/_new-layout-eventweekly/libs/idangerous.swiper/idangerous.swiper.min.css') }}
    {{ stylesheet_link('/_new-layout-eventweekly/libs/bootstrap/bootstrap.css') }}
    {{ stylesheet_link('/_new-layout-eventweekly/libs/bootstrap/bootstrap-theme.css') }}

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>

    <script data-main="/js/config" src="/js/requirePlugins/require.js"></script>
</head>

<body>
{% if eventPreview is defined %}
    <div class="preview_overlay" style="height: 100%;width: 100%;z-index: 10000;top:0;left:0;position:fixed;"></div>
{% endif %}

{% include 'layouts/stuff.volt' %}
<!-- Header -->
		<header id="header">
		{% if member.id is defined %}
			<div class="top-line">
				<div class="container">

					<div class="user-bar">
						<div class="dropdown">
							<!-- button -->
							<a id="js-userBarDropDown" data-toggle="dropdown">
								<img 
									{% if member.logo != '' %}
                                        src="{{ member.logo }}"
                                    {% else %}
                                        src='/img/demo/h_back_1.jpg'
                                    {% endif %} 
								class="user-bar__avatar" alt="Member logo" />

								<span class="user-bar__username">
									{% if member.name|length %}
                                        {{ member.name }}
                                    {% else %}
                                        {{ member.email }}
                                    {% endif %}
								</span> 
								<span class="caret"></span>
							</a>
							
							<!-- dropdown -->
							<ul class="dropdown-menu" role="menu" aria-labelledby="js-userBarDropDown">
								
                                <li>
                                	<a role="menuitem" tabindex="-1" href="/member/profile">Profile settings</a>
                                </li>
                                <!-- li>
                                	<a role="menuitem" tabindex="-1" href="/campaign/list">Manage campaigns</a>
                                </li -->
                                <li>
                                	<a role="menuitem" tabindex="-1" href="/event/list">
                                        <span class="btn-text">My events</span>
                                	</a>
                                </li>
                                <li>
                                	<a role="menuitem" tabindex="-1" href="/event/joined">
                                        <span class="btn-text">Events I’m attending</span>
                                	</a>
                                </li>
                                <li>
                                	<a role="menuitem" tabindex="-1" href="/event/liked">
                                        <span class="btn-text">Events I like</span>
                                	</a>
                                </li>
                                <li>
                                	<a role="menuitem" tabindex="-1" href="/event/friends">
                                        <span class="btn-text">Friends’ events</span>
                                	</a>
                                </li>

								<li>
									<a role="menuitem" tabindex="-1" href="/auth/logout">Logout</a>
								</li>
							</ul>
						</div>
					</div>

					<a href="#" class="user-menu-button-open js-user-menu-button-open-trigger" onclick="location.href='/event/edit'">Open Menu</a>

					<div class="clearfix"></div>

					<div class="user-menu-collapsed js-user-menu-collapsed">
						<div class="top-line__item">
							<p class="top-line__counter" >
							{% if eventsTotal is defined %}
							<span class="location-count"
	                              data-placement="bottom" 
	                              title=""
	                              id="events_count"
	                              data-original-title="All events {{ eventsTotal }}">{{ eventsTotal }}
                        	</span>
							{% else %}
							<span class="location-count"
	                              data-placement="bottom" 
	                              title=""
	                              id="events_count"
	                              data-original-title="0">Go find</span>
							{% endif %}
							 events
							</p>
						</div>

						 

						<div class="top-line__item">
							<a href="#" class="top-line__button" onclick="location.href='/event/edit'">
								<i class="fa fa-plus"></i> Add event
							</a>
						</div>
					</div>

				</div>
			</div>
		{% else %}
			<div class="top-line">
				<div class="container">
					
					<div class="top-line__item top-line__item--text">Never miss <strong>events</strong> in Dublin!</div>
					<div class="top-line__item">
						<a href="/auth/signup" class="top-line__button">
							<i class="fa fa-sign-in"></i> Sign Up today
						</a>
					</div>
					<div class="top-line__item top-line__item--divider">
						<a href="#" class="top-line__link fb-login-popup"  onclick="return false;">Sign In</a>
					</div>
					
                    
				</div>
			</div>
		{% endif %}
		
		
			<div class="middle-bg">
				<div class="middle-bg-wrp">
					<h1 class="header-logo">
						<a class="logo" href="/"><strong>Event</strong>Weekly</a>
					</h1>
					<h2 class="is-landing-page-header-title">Whats On In London</h2>

					<!-- div class="title-search-form">
						<form action="#">
							<input type="search"
								   placeholder="Search event or venue..."
								   class="title-search-form-input" />
							<a href=""><i class="title-search-form-icon fa fa-search"></i></a>
						</form>
					</div -->
				</div>

			</div>

			{% include 'layouts/searchform_new.volt' %}

		</header>

			<section id="content" class="container page page-main">

			 {% if paidEvents is defined %}
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
						<h2 class="is-landing-page-title is-align-center">
													Our goal is to bring all events into one place. We all have different taste in events we like and there are a huge amount events happening every week all over the world form Cake Sales to Rock Festivals. Our software gathers events from many different sources and using Artificial Intelligence sorts them into categories. This allows you to browse and personalise the listings you want to see.<br />
EventWeekly.com plan to release a mobile app in early 2016 making finding great events locally or world wide a lot easer. We already have thousands of events listed form all over the world and are expanding every day.<br />
There are over 1,000 events happing every week in London alone and with eventweekly's what's on in London page you are sure to find something you like.<br />
As a promoter you can sign up with your Facebook account and have all your Facebook events added automatically. You can also add events straight to our site including recurring events.<br />
We are also open to suggestions for upcoming features,<br />
Would you like a weekly email personalised to your tastes?<br />
Would you like a weekly email suggesting events you may like?<br />
Would you like a weekly email of events advertising to you?<br />
There are other features we are considering including showing who is going, what the % Male to Female attending, a scale displaying how much chatter the event is getting, ticket link to purchase tickets, Rep button so you can earn points for promoting the events and reward system that rewards you for promoting and attending events. These are just some of the features we are looking into for future development and are open to your Ideas.<br /><br />
We are looking forward to the next 12 months and adding more and more events and features to our site.<br /><br />
Welcome to EventWeekly.com and our what's on in London page enjoy finding great events to attend.<br />
						</h2>
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
					<!-- h2 class="is-landing-page-title is-align-center">
						Here we add discription of what the site is about using keywords etc
					</h2 -->
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
		
		<!--div class="ew-filter-link">
			<a href="#" class="Show Filter">Show Filter</a>	
		</div -->
		<!-- Footer -->
			<footer id="footer">
				<div class="container">

						<div class="footer_item footer_item_left">
							<a href="/ew/about">About us</a>
						</div>

						<div class="footer_item">
							<a href="/ew/contact">Contact</a>
						</div>
						
				</div>
				<div class="clearfix"></div>
			</footer>
</body>
</html>			