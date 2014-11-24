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
						        {{ searchForm.render('searchTitle', {'class':'filters-form__input', 'placeholder':'Title', 'value': searchTitle}) }}
							</div>
							
							<!-- search by location -->
							<div class="filters-form__item filters-form__item--input-with-icon">
								<i class="fa fa-map-marker"></i>
								{% if userSearch is defined and userSearch['searchLocation'] is defined %}
						            {% set searchLocation = userSearch['searchLocation'] %}
						        {% else %}
						            {% set searchLocation = '' %}
						        {% endif %}
						        <input type="text" data-location-chosen="false" id="searchLocationField" name="searchLocationField" class="filters-form__input" placeholder="Location. Current value is your location" value="{{ searchLocation }}"/>
						        
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
										<li>
											<a role="menuitem" tabindex="-1" href="#">Venues</a>
										</li>
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
						                {% set searchStartDate = '' %}
						            {% endif %}
								  	22 Sep
								  </a>
								</div>
							</div>
							
							<!-- map dropdown -->
							<div class="filters-form__item">
								<div class="dropdown">
									<!-- button -->
									<a class="filters-form__dropdown" id="js-selectEventType" data-toggle="dropdown">
										<i class="fa fa-globe"></i>
										List <span class="caret"></span>
									</a>
									
									<!-- dropdown -->
									<ul class="dropdown-menu" role="menu" aria-labelledby="js-selectEventType">
										<li>
											<a role="menuitem"  tabindex="-1" href="#">Map</a>
										</li>
										<li>
											<a role="menuitem" tabindex="-1" href="#">List</a>
										</li>
									</ul>
								</div>
							</div>

							<div class="filters-form__item filters-form__divider"></div>

							<div class="filters-form__item">
								<button href="#" class="filters-form__button">Show results</button>
							</div>

						</div>

				</div>
			</div>
			
</form>