<!-- <div class="page" style="padding:0;"> -->
	<div class="page__wrapper">
		<section id="content" class="container page-search">

			<div class="page__sort"></div>

				<div class="page-search__wrapper">
				{% if list is defined %}
					
				<!-- start container with events -->
					<div class="b-list-of-events-g">

						{% for event in list %}
							<!-- item -->
							<div class="b-list-of-events-g__item pure-u-1-3 event-list-event" data-event-id={{ event.id }}>
								<div class="b-list-of-events-g__wrapper">
									<div class="b-list-of-events-g__picture">
										<a href="/{{ toSlugUri(event.name) }}-{{ event.id }}">
											{% if event.logo is defined %}
												<img src="{{ checkLogo(event) }}" alt="{{ event.name }}" class="lazy" data-original="{{ checkLogo(event) }}">
											{% elseif event.cover is defined %}
												<img src="{{ checkCover(event.cover) }}" alt="{{ event.name }}" class="lazy" data-original="{{ checkCover(event.cover) }}">
											{% else %}
												<img src="/img/logo200.png" alt="{{ event.name }}" class="lazy" data-original="/img/logo200.png">
											{% endif %}
										</a>
									</div>

									<div class="b-list-of-events-g__info">
										<h2 class="b-list-of-events-g__title">
											<a href="/{{ toSlugUri(event.name) }}-{{ event.id }}">{{ event.name }}</a>
										</h2>
										
										<div class="b-list-of-events-g__date">
											{% if event.location is defined %}
												<p class="b-list-of-events-g__date-venue">
													<i class="fa fa-map-marker"></i> {{ event.location }}
													{% if event.address is defined %}
														, {{ event.address }}
													{% endif %}
												</p>
											{% endif %}
											{% if event.start_date != '0000-00-00' %}
												<time>{{ dateToFormat(event.start_date, '%d %b') }}
	                                                {% if event.end_date is defined and event.end_date != '0000-00-00' and dateToFormat(event.end_date, '%d %b %Y') != dateToFormat(event.start_date, '%d %b %Y') %}
	                                                 	- {{ dateToFormat(event.end_date, '%d %b') }}
	                                                 {% endif %}
	                                            </time>
                                            {% endif %}
										</div>
										
										<div class="b-list-of-events-g__description">
											<p>{{ event.description|striptags|escape|truncate(250) }}</p>
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
										
									</div>	
								</div>
								
								<div class="b-list-of-events-g__link-detail" href="#">
									<div class="like-buttons">
										<div class="like-buttons__item eventLikeBtn" data-id="{{ event.id }}" data-status="1">
											<a style="cursor:pointer;" 
												{% if event.disabled is defined %}class="ew-button-dis"{% else %}class="ew-button"{% endif %} 
												title="Like" ><i class="fa fa-thumbs-up"></i></a>
										</div>
		
										<div class="like-buttons__item eventDislikeBtn" data-id="{{ event.id }}" data-status="0">
											<a style="cursor:pointer;" class="ew-button" title="Dislike">
												<i class="fa fa-thumbs-down"></i>
											</a>
										</div>
										
										<div class="like-buttons__item-share">
											<a class="ew-button share-event" title="Share"
												style="cursor:pointer;" 
											   data-event-source="/{{ toSlugUri(event.name) }}-{{ event.id }}"
											   data-image-source="{{ checkLogo(event) }}">
											   <i class="fa fa-share-alt"></i>Share
											</a>
										</div>
									</div>
									<div class="read-more">
										<a href="/{{ toSlugUri(event.name) }}-{{ event.id }}" class="read-more-link">Read more &#x2192;</a>
									</div>
								</div>
								
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
		
	</div>
	<div class="page__wrapper_ajax_search"></div>
<!-- </div> -->
