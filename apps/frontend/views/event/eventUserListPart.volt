<div class="page__wrapper">
		<section id="content" class="container page-search" >

			<div class="page__sort"></div>

				<div class="page-search__wrapper">
				{% if list is defined %}
					
				<!-- start container with events -->
					<div class="b-list-of-events-g">

						{% for event in list %}
							<!-- item -->
							<div class="b-list-of-events-g__item pure-u-1-3 event-list-event" data-event-id={{ event.id}}>
								<div class="b-list-of-events-g__wrapper">
									<div class="b-list-of-events-g__picture">
										<a href="/{{ toSlugUri(event.name) }}-{{ event.id }}">
											<img src="{{ checkLogo(event) }}" alt="{{ event.name }}" class="lazy" data-original="{{ checkLogo(event) }}">
										</a>

										<div class="like-buttons">  
											{% if eventListCreatorFlag %}
												{% if event.event_status == 1 %}
                                                    <div class="pure-u-1-2 unpublishEvent" id="{{ event.id }}">
														<a class="ew-button" title="Unpublish">Unpublish</a>
                                                    </div>
                                                {% else %}
                                                    <div class="pure-u-1-2 publishEvent" id="{{ event.id }}">
														<a class="ew-button" title="Publish">Unpublish</a>
                                                    </div>
												{% endif %}
												<div class="pure-u-1-2 like-buttons__item editEvent" id="{{ event.id }}">
													<a href="/event/edit/{{event.id}}" class="ew-button" title="Edit">Edit</a>
												</div>
												<div class="pure-u-1-2 like-buttons__item deleteEvent" id="{{ event.id }}">
													<a href="/" class="ew-button" title="Archive">Archive</a>
												</div>
											{% else %}
												<div class="pure-u-1-2 like-buttons__item eventLikeBtn" data-id="{{ event.id }}" data-status="1">
													<a href="#" 
														{% if event.disabled is defined %}class="ew-button-dis"{% else %}class="ew-button"{% endif %} 
														title="Like" ><i class="fa fa-thumbs-up"></i></a>
												</div>
												<div class="pure-u-1-2 like-buttons__item eventDislikeBtn" data-id="{{ event.id }}" data-status="0">
													<a href="#" class="ew-button" title="Dislike">
														<i class="fa fa-thumbs-down"></i>
													</a>
												</div>
											{% endif %}
										</div>
									</div>

									<div class="b-list-of-events-g__info">
										<h2 class="b-list-of-events-g__title">
											<a href="/{{ toSlugUri(event.name) }}-{{ event.id }}">{{ event.name }}</a>
										</h2>
										
										<div class="b-list-of-events-g__date">
											{% if event.start_date != '0000-00-00' %}
                                                {{ dateToFormat(event.start_date, '%d %b %Y') }}
                                                {% if event.end_date is defined and event.end_date != '0000-00-00' %}
                                                 	- {{ dateToFormat(event.end_date, '%d %b %Y') }}
                                                 {% endif %}
                                            {% endif %}
										</div>
										{% if event.category|length %}
											<div class="b-list-of-events-g__category">
												<i class="fa fa-tag"></i>
												{% for cat in event.category %}
													{{ cat }}
													{% if !loop.last %}, {% endif %}
												{% endfor %}
											</div>
										{% endif %}
										{% if event.location.city is defined %}
											<div class="b-list-of-events-g__category">
												<i class="fa fa-map-marker"></i>
												{{ event.location.city }}, {{ event.location.country }}
											</div>
										{% endif %}
										<div class="b-list-of-events-g__description" id="555">
											<p>{{ event.description|striptags|escape|truncate(250) }}</p>
										</div>

										<div class="footer">
											<!-- div class="footer__item">
												<i class="fa fa-ticket"></i> Tickets: $100-$200
											</div -->
											{% if event.recurring == 7 %}
												<div class="footer__item"><i class="fa fa-retweet"></i> Weekly event</div>
											{% elseif event.recurring == 1 %}
												<div class="footer__item"><i class="fa fa-retweet"></i> Daily event</div>
											{% elseif event.recurring == 30 %}
												<div class="footer__item"><i class="fa fa-retweet"></i> Monthly event</div>
											{% endif %}
										</div>

										<div class="actions">
											<!-- a class="ew-button">
												<i class="fa fa-calendar"></i> Add to calendar
											</a -->
											<a class="ew-button share-event" 
											   style="cursor:pointer;" 
											   data-event-source="/{{ toSlugUri(event.name) }}-{{ event.id }}"
											   data-image-source="{{ checkLogo(event) }}"><i class="fa fa-share-alt"></i> Share
											</a>
										</div>
									</div>
								</div>

								<a class="b-list-of-events-g__link-detail" href="/{{ toSlugUri(event.name) }}-{{ event.id }}">Read More →</a>
							</div>
							
							<!-- item -->
							
							<!-- {% if loop.index == 3 %}
								<div class="clearfix"></div>
							{% endif %} -->
							
						{% endfor %}							
							
					</div>

				{% endif %}

						

				</div>
		</section>

<div class="page__wrapper_ajax_search"></div>
