<form action="/search" method="get" class="form-horizontal" id="topSearchForm">

		<div class="filters">
				<div class="container">
						<div class="filters-form">
						
							<!-- search by event name -->
							<div class="filters-form__item filters-form__item--input-with-icon">
								<i class="fa fa-search"></i>
						        {% if userSearch is defined and userSearch['searchTitle'] is defined %}
						            {% set searchTitle = userSearch['searchTitle'] %}
						        {% else %}
						            {% set searchTitle = '' %}
						        {% endif %}
						        {{ searchForm.render('searchTitle', {'class':'filters-form__input', 'placeholder':'Event or venue...', 'value': searchTitle}) }}
							</div>
							
							<!-- search by location -->
							<div class="filters-form__item filters-form__item--input-with-icon">
								<i class="fa fa-map-marker"></i>
								{% if userSearch is defined and userSearch['searchLocation'] is defined %}
						            {% set searchLocation = userSearch['searchLocation'] %}
						            {% set searchLocationPlaceholder = userSearch['searchLocation'] %}
						        {% else %}
						            {% set searchLocation = '' %}
						            {% set searchLocationPlaceholder = 'Dublin' %}
						        {% endif %}
						        <input type="text" data-location-chosen="false" id="searchLocationField" name="searchLocationField" class="filters-form__input" placeholder="{{ searchLocationPlaceholder }}" value="{{ location.city }}"/>
						        
				                {% if  userSearch is defined and userSearch['searchLocationLatMin'] is defined %}
						            {% set searchLocationLatMin = userSearch['searchLocationLatMin'] %}
						        {% else %}
						            {% set searchLocationLatMin = '' %}
						        {% endif %}
						        {{ searchForm.render('searchLocationLatMin', {'value': searchLocationLatMin}) }}
						
						        {% if  userSearch is defined and userSearch['searchLocationLngMin'] is defined %}
						            {% set searchLocationLngMin = userSearch['searchLocationLngMin'] %}
						        {% else %}
						            {% set searchLocationLngMin = '' %}
						        {% endif %}
						        {{ searchForm.render('searchLocationLngMin', {'value': searchLocationLngMin}) }}
						
						        {% if  userSearch is defined and userSearch['searchLocationLatMax'] is defined %}
						            {% set searchLocationLatMax = userSearch['searchLocationLatMax'] %}
						        {% else %}
						            {% set searchLocationLatMax = '' %}
						        {% endif %}
						        {{ searchForm.render('searchLocationLatMax', {'value': searchLocationLatMax}) }}
						
						        {% if  userSearch is defined and userSearch['searchLocationLngMax'] is defined %}
						            {% set searchLocationLngMax = userSearch['searchLocationLngMax'] %}
						        {% else %}
						            {% set searchLocationLngMax = '' %}
						        {% endif %}
						        {{ searchForm.render('searchLocationLngMax', {'value': searchLocationLngMax}) }}
							</div>
							
							<!-- events dropdown -->
							<div class="filters-form__item">
								<div class="dropdown">
									<!-- button -->
									<a class="filters-form__dropdown" id="js-selectEventType" data-toggle="dropdown">
										<i class="fa fa-glass"></i>
										Events <span class="caret"></span>
									</a>
									
									<!-- dropdown -->
									<ul class="dropdown-menu" role="menu" aria-labelledby="js-selectEventType">
										<li>
											<a role="menuitem"  tabindex="-1" href="#">Event</a>
										</li>
										<!-- li>
											<a role="menuitem" tabindex="-1" href="#">Venues</a>
										</li -->
									</ul>
								</div>
							</div>
							
							<!-- datetime dropdown -->
							<div class="filters-form__item">
								<div class="dropdown">
								  <a class="filters-form__dropdown" id="js-selectDateTime">
								  	<i class="fa fa-calendar"></i>
								  	 {% if  userSearch is defined and userSearch['searchStartDate'] is defined %}
						                {% set searchStartDate = userSearch['searchStartDate'] %}
						            {% else %}
						                {% set searchStartDate = date('Y-m-d') %}
						            {% endif %}
						            <span id="searchPanel-startDate" name="start_date">{{ searchStartDate }}</span>
								  	{{ searchForm.render('searchStartDate', {'value': searchStartDate}) }}
								  </a>
								</div>
							</div>
							
							<!-- map dropdown -->
							<div class="filters-form__item">
							 	{% if userSearch['searchTypeResult'] is defined %}
						            {% set searchTypeResult = userSearch['searchTypeResult'] %}
						        {% else %}
						            {% set searchTypeResult = 'List' %}
						        {% endif %}
								<div class="dropdown">
									<!-- button -->
									<a class="filters-form__dropdown" id="js-selectEventType" data-toggle="dropdown">
										<i class="fa fa-globe"></i>
										<span id="searchTypeResultCurrent">{{ searchTypeResult }}</span>
										<span class="caret"></span>
									</a>
									
									<!-- dropdown -->
									<ul class="dropdown-menu" role="menu" aria-labelledby="js-selectEventType" id="searchTypeResultMenu">
										{% for index, type in searchTypes%}
											{% if type != searchTypeResult %}
												<li>
													<a role="menuitem" tabindex="-1" data-value="{{ type }}">{{ type }}</a>
												</li>
											{% endif %}
										{% endfor %}
									</ul>
									{{ searchForm.render('searchTypeResult', {'value': searchTypeResult }) }}
								</div>
							</div>

							<div class="filters-form__item filters-form__divider"></div>

							<div class="filters-form__item">
								<button type="submit" id="searchSubmit" class="filters-form__button">Show results</button>
							</div>

						</div>

				</div>
			</div>
			
</form>