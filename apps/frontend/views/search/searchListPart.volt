<!-- <div class="page" style="padding:0;"> -->
	<div class="page__wrapper">
		<section id="content" class="container page-search">

			<div class="page__sort"></div>

				<div class="page-search__wrapper">
				{% if list is defined %}
					{% if searchGrid == 'event' %}
						{% set objectLink = '' %}
					{% elseif userSearch['searchGrid'] == 'venue' %}
						{% set objectLink = '/venue' %}
					{% endif %}
					
				<!-- start container with events -->
					<div class="b-list-of-events-g">

						{% for object in list %}
							<!-- item -->
							<div class="b-list-of-events-g__item pure-u-1-3 event-list-event" data-event-id={{ event.id }}>
								<div class="b-list-of-events-g__wrapper">
									<div class="b-list-of-events-g__picture">
										<a href="{{ objectLink }}/{{ toSlugUri(object.name) }}-{{ object.id }}">
											{% if object.logo is defined %}
												<img src="{{ checkLogo(object, searchGrid) }}" alt="{{ object.name }}" class="lazy" data-original="{{ checkLogo(object, searchGrid) }}">
											{% elseif object.cover is defined %}
												<img src="{{ checkCover(object.cojver, searchGrid) }}" alt="{{ object.name }}" class="lazy" data-original="{{ checkCover(object.cover, searchGrid) }}">
											{% else %}
												<img src="/img/logo200.png" alt="{{ object.name }}" class="lazy" data-original="/img/logo200.png">
											{% endif %}
										</a>
									</div>

									<div class="b-list-of-events-g__info">
										<h2 class="b-list-of-events-g__title">
											<a href="{{ objectLink }}/{{ toSlugUri(event.name) }}-{{ object.id }}">{{ object.name }}</a>
										</h2>
										
										<div class="b-list-of-events-g__date">
											{% if object.location is defined %}
												<p class="b-list-of-events-g__date-venue">
													<i class="fa fa-map-marker"></i> {{ object.location }}
													{% if object.address is defined %}
														, {{ object.address }}
													{% endif %}
												</p>
											{% endif %}
											{% if searchGrid == 'event' %}
												{% if object.start_date != '0000-00-00' %}
													<time>{{ dateToFormat(object.start_date, '%d %b') }}
		                                                {% if object.end_date is defined and object.end_date != '0000-00-00' and dateToFormat(object.end_date, '%d %b %Y') != dateToFormat(object.start_date, '%d %b %Y') %}
		                                                 	- {{ dateToFormat(object.end_date, '%d %b') }}
		                                                 {% endif %}
		                                            </time>
	                                            {% endif %}
	                                        {% endif %}
										</div>
										
										<div class="b-list-of-events-g__description">
											<p>{{ object.description|striptags|escape|truncate(250) }}</p>
										</div>
										
										{% if object.category|length %}
											<div class="b-list-of-events-g__category">
												<i class="fa fa-tag"></i>
												{% for cat in object.category %}
													{{ cat }}
													{% if !loop.last %}, {% endif %}
												{% endfor %}
											</div>
										{% endif %}
										
									</div>	
								</div>
								
								<div class="b-list-of-events-g__link-detail" href="#">
									<div class="like-buttons">
										{% if searchGrid == 'event' %}
											<div class="like-buttons__item eventLikeBtn" data-id="{{ object.id }}" data-status="1">
												<a style="cursor:pointer;" 
													{% if object.disabled is defined %}class="ew-button-dis"{% else %}class="ew-button"{% endif %} 
													title="Like" ><i class="fa fa-thumbs-up"></i></a>
											</div>
			
											<div class="like-buttons__item eventDislikeBtn" data-id="{{ object.id }}" data-status="0">
												<a style="cursor:pointer;" class="ew-button" title="Dislike">
													<i class="fa fa-thumbs-down"></i>
												</a>
											</div>
										{% endif %}
										
										<div class="like-buttons__item-share">
											<a class="ew-button share-event" title="Share"
												style="cursor:pointer;" 
											   data-event-source="{{ objectLink }}/{{ toSlugUri(object.name) }}-{{ object.id }}"
											   data-image-source="{{ checkLogo(object, searchGrid) }}">
											   <i class="fa fa-share-alt"></i>Share
											</a>
										</div>
									</div>
									<div class="read-more">
										<a href="{{ objectLink }}/{{ toSlugUri(object.name) }}-{{ object.id }}" class="read-more-link">Read more &#x2192;</a>
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
