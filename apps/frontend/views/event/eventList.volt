{% extends "layouts/base_new.volt" %}

{% block content %}

<div class="page">
	<div class="page__wrapper">
		<section id="content" class="container page-search">

			<h1 class="page__title">{{ listTitle|default('Event list') }}</h1>
			<div class="page__sort"></div>

				<div class="page-search__wrapper">
				{% if list is defined %}
					
				<!-- start container with events -->
					<div class="b-list-of-events-g">

						{% for event in list %}

							<!-- item -->
							<div class="b-list-of-events-g__item pure-u-1-3">
								<div class="b-list-of-events-g__wrapper">
									<div class="b-list-of-events-g__picture">
										<a href="/{{ toSlugUri(event.name) }}-{{ event.id }}">
											<img src="{{ checkLogo(event) }}" alt="{{ event.name }}">
										</a>

										<div class="like-buttons">
											<div class="pure-u-1-2 like-buttons__item">
												<a href="#" class="ew-button" title="Like">
													<i class="fa fa-thumbs-up"></i>
												</a>
											</div>

											<div class="pure-u-1-2 like-buttons__item">
												<a href="#" class="ew-button" title="Dislike">
													<i class="fa fa-thumbs-down"></i>
												</a>
											</div>
										</div>
									</div>

									<div class="b-list-of-events-g__info">
										<h2 class="b-list-of-events-g__title">
											<a href="/{{ toSlugUri(event.name) }}-{{ event.id }}">{{ event.name }}</a>
										</h2>
										
										<div class="b-list-of-events-g__date">
											{% if event.start_date != '0000-00-00' %}
                                                {{ dateToFormat(event.start_date, '%d %b %Y') }}
                                                {% if dateToFormat(event.start_date, '%R') != '00:00' %}, {{ dateToFormat(event.start_date, '%R') }}{% endif %}
                                            {% endif %}
										</div>

										<div class="b-list-of-events-g__category">
											<i class="fa fa-music"></i>
											Rockabilly, Dance, Rock
										</div>

										<div class="b-list-of-events-g__description">
											<p>{{ event.description|striptags|escape|truncate(350) }}</p>
										</div>


										<div class="footer">
											<div class="footer__item">
												<i class="fa fa-ticket"></i> Tickets: $100-$200
											</div>
											<div class="footer__item"><i class="fa fa-retweet"></i> Weekly event</div>
										</div>

										<div class="actions">
											<a href="#" class="ew-button">
												<i class="fa fa-calendar"></i> Add to calendar
											</a>
											<a href="#" class="ew-button">
												<i class="fa fa-share-alt"></i> Share
											</a>
										</div>
									</div>
								</div>

								<a class="b-list-of-events-g__link-detail" href="/{{ toSlugUri(event.name) }}-{{ event.id }}">Read More →</a>
							</div>
							
							<!-- item -->
							
							{% if loop.index == 3 %}
								<div class="clearfix"></div>
							{% endif %}
							
						{% endfor %}							
							
					</div>
				{% endif %}
														
				</div>
		</section>
	</div>
</div>

<div class="ew-filter-link">
	<a href="#" class="Show Filter">Show Filter</a>	
</div>
{% endblock %}