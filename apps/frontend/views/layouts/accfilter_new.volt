		<div class="ew-filter-link" id="swithFilterPanel">
			<a href="#" class="Show Filter">Show Filter</a>	
		</div>
		
		<aside id="filters" class="b-filters sidebar-filters" style="display:none">
			<div class="b-filters__wrapper">
				<div class="b-filters__buttons">
					<a href="#" class="ew-button"><i class="fa fa-check-square-o"></i> Check all</a>
					<a href="#" class="ew-button"><i class="fa fa-square-o"></i> Uncheck all</a>
					<a href="#" class="ew-button"><i class="fa fa-star-o"></i> Default</a>
				</div>

				<div class="categories-accordion">

				{% for filter, category in userFilters %}

					<!-- accordion item -->
						<div class="categories-accordion__item">
							<div class="categories-accordion__head">
								<div class="categories-accordion__line"></div>
	
								<div class="form-checkbox">
									<input type="checkbox" id="tag-{{ category['id']}}" class="userFilter-category" checked> 
									<label for="t1"><span><span></span></span>{{ category['name'] }}</label>
								</div>
	
								<a href="#" class="categories-accordion__arrow" id="blockfilter-{{ category['id'] }}">
									<i class="icon"></i> Expand
								</a>
							</div>
	
							{% if category['tags'] is not empty %}
							<!-- list of checkboxes -->
									<div class="categories-accordion__body" class="userTag-subfilters" id="subfilter-{{ category['id'] }}" style="display:none;">
										{% for subfilter, tag in category['tags'] %}									
											<div class="form-checkbox pure-u-1-2">
												<input type="checkbox" id="tag-{{ tag['id']}}" data-category-id="{{ tag['category_id'] }}" class="userFilter-tag" checked> 
												<label for="t1" title="{{ tag['name'] }}"><span><span></span></span>{{ tag['name']}}</label>
											</div>
										{% endfor %}
									</div>
							{% endif %}
						</div>
						
					{% endfor %}

				</div>
			</div>
		</aside>
		
		<div class="overlay" style="display:none;" id="filter-panel-overlay"></div>