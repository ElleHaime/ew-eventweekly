<form action="/dublin-ie/today" method="post" class="form-horizontal" id="topSearchForm">

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
						        {{ searchForm.render('searchTitle', {'class':'filters-form__input', 'placeholder':'', 'value': searchTitle }) }}
							</div>
							
							<!-- search by location -->
							<div class="filters-form__item filters-form__item--input-with-icon">
								<i class="fa fa-map-marker"></i>
						        {% if userSearch is defined %}
						        	{% if userSearch['searchLocationCity'] is defined %}
							        	{% set searchLocation = userSearch['searchLocationCity'] %}
							            {% set searchLocationCityPlaceholder = userSearch['searchLocationCity'] %}
							            {% set searchLocationCountryPlaceholder = userSearch['searchLocationCountry'] %}
							        {% else %}
							        	{% set searchLocation = '' %}
						        		{% set searchLocationCityPlaceholder = '' %}
						        		{% set searchLocationCountryPlaceholder = '' %}
						        	{% endif %}
						        {% else %}
						        	{% if location is defined %}
						        		{% set searchLocation = location.city %}
						        		{% set searchLocationCityPlaceholder = location.city %}
						        		{% set searchLocationCountryPlaceholder = location.country %}
						        	{% else %}
						        		{% set searchLocation = '' %}
						        		{% set searchLocationCityPlaceholder = '' %}
						        		{% set searchLocationCountryPlaceholder = '' %}
						        	{% endif %}
						        {% endif %}
						        <input type="text" data-location-chosen="false" id="searchLocationField" name="searchLocationField" class="filters-form__input" placeholder="{{ searchLocationCityPlaceholder }}, {{ searchLocationCountryPlaceholder }}" value="{{ searchLocationCityPlaceholder }}, {{ searchLocationCountryPlaceholder }}"/>
						        
						        {% if  userSearch is defined and userSearch['searchLocationFormattedAddress'] is defined %}
						            {% set searchLocationFormattedAddress = userSearch['searchLocationFormattedAddress'] %}
						        {% else %}
						            {% set searchLocationFormattedAddress = '' %}
						        {% endif %}
						        {{ searchForm.render('searchLocationFormattedAddress', {'value': searchLocationFormattedAddress}) }}
							</div>
							
							<!-- events dropdown -->
							{% if userSearch is defined %}
								{% set searchGridName = searchGrids[userSearch['searchGrid']] %}
								{% set searchGridVal = userSearch['searchGrid'] %}
							{% else %}
								{% set searchGridName = 'Events' %}
								{% set searchGridVal = 'event' %}
						    {% endif %}
							<div class="filters-form__item">
								<div class="dropdown">
									<!-- button -->
									<a class="filters-form__dropdown" data-toggle="dropdown">
										<i class="fa fa-check"></i><span id="searchGridElem" data-grid-id="{{ searchGridVal }}" data-grid-name="{{ searchGridName }}"> {{ searchGridName }}</span> 
										<span class="caret"></span>
									</a>
									<!-- dropdown -->
									<ul class="dropdown-menu" role="menu" aria-labelledby="js-selectEventType">
										{% for gridVal, gridName in searchGrids %}
											{% if gridVal != searchGridVal %}
												<li>
													<a role="menuitem" tabindex="-1" style="cursor:pointer;" class="searchGridMenuTab" data-grid-id="{{ gridVal }}" data-grid-name="{{ gridName }}">
														<span style="padding-left:15px;">{{ gridName }}</span>
													</a>	
												</li>
											{% endif %}
										{% endfor %}
									</ul>
									
									{{ searchForm.render('searchGrid', {'value': searchGridVal}) }}
								</div>
							</div>
							
							
							
							<!-- datetime dropdown -->
							<div class="filters-form__item" id="startDate-main">
								<div class="dropdown">
								  <a class="filters-form__dropdown" id="js-selectDateTimeStart">
								  	<i class="fa fa-calendar"></i>
								  	 {% if  userSearch is defined and userSearch['searchStartDate'] is defined %}
						                {% set searchStartDate = userSearch['searchStartDate'] %}
						            {% else %}
						                {% set searchStartDate = getDefaultStartDate() %}
						            {% endif %}
						            <span id="searchPanel-startDate" name="start_date">{{ searchStartDate }}</span>
								  	{{ searchForm.render('searchStartDate', {'value': searchStartDate}) }}
								  </a>
								</div>
							</div>
							
							<div class="filters-form__item" style="display:none;" id="startDate-reserve">
								<div class="dropdown">
								  <a class="filters-form__dropdown" style=" cursor:arrow;color:rgb(153, 153, 153);">
								  	<i class="fa fa-calendar"></i>
						            <span id="searchPanel-startDate" name="start_date">{{ searchStartDate }}</span>
								  </a>
								</div>
							</div>
							
							<div class="filters-form__item" id="endDate-main">
								<div class="dropdown">
								  <a class="filters-form__dropdown" id="js-selectDateTimeEnd">
								  	<i class="fa fa-calendar"></i>
								  	 {% if  userSearch is defined and userSearch['searchEndDate'] is defined %}
						                {% set searchEndDate = userSearch['searchEndDate'] %}
						            {% else %}
						                {% set searchEndDate = getDefaultEndDate() %}
						            {% endif %}
						            <span id="searchPanel-endDate" name="end_date">{{ searchEndDate }}</span>
								  	{{ searchForm.render('searchEndDate', {'value': searchEndDate}) }}
								  </a>
								</div>
							</div>
							
							<div class="filters-form__item" style="display:none;" id="endDate-reserve">
								<div class="dropdown">
								  <a class="filters-form__dropdown" style=" cursor:arrow;color:rgb(153, 153, 153);">
								  	<i class="fa fa-calendar"></i>
						            <span id="searchPanel-endDate" name="end_date">{{ searchStartDate }}</span>
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
										<i class="fa fa-check"></i>
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

							{# <div class="filters-form__item filters-form__divider"></div> #}
							
							{#														
							<div class="filters-form__item">
								<div class="form-checkbox">
									<input type="checkbox" class="userFilter-category" name="searchGrid[event]" id="searchGrid-event"
										{% if userSearch is defined and userSearch['searchGrid]['event'] id defined %} checked{% endif %}>
									<label for="t1"><span><span></span></span>Event</label>
								
									<input type="checkbox" class="userFilter-category" name="searchGrid[venue]"  id="searchGrid-venue"
										{% if userSearch is defined and userSearch['searchGrid]['venue'] id defined %} checked{% endif %}>
									<label for="t1"><span><span></span></span>Venue</label>
								</div>
							</div>
							#}							 

							<div class="filters-form__item">
								<button type="submit" id="searchSubmit" class="filters-form__button">Show results</button>
							</div>

						</div>

				</div>
			</div>
			
</form>