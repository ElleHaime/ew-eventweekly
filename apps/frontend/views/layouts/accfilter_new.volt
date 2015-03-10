<input id="tagIds" name="tagIds" type="hidden" value="{{ tagIds }}" />
<input id="personalPresetActive" name="personalPresetActive" type="hidden" value="{{ personalPresetActive }}" />

<form action="" id="form2">
		<div class="ew-filter-link" id="swithFilterPanel">
			<a class="Show Filter" style="cursor:pointer;">Show Filter</a>	
		</div>
		
		<aside id="filters" class="b-filters sidebar-filters" style="display:none">
			<div class="b-filters__wrapper">
				<div class="b-filters__buttons">
					<a id="check-all" class="ew-button"><i class="fa fa-check-square-o"></i> Check all</a>
					<a id="uncheck-all" class="ew-button"><i class="fa fa-square-o"></i> Uncheck all</a>
					<a id="default-choise" class="ew-button"><i class="fa fa-star-o"></i> Default</a>
				</div>

				<div class="categories-accordion">

					{% for index, category in userFilters %}
						<!-- accordeon item -->
							<div class="categories-accordion__item">
								<div class="categories-accordion__head">
									<div class="categories-accordion__line"></div>
		
									<div class="form-checkbox">
										<input type="checkbox" id="cattag-{{ category['id']}}" class="userFilter-category" 
											{% if category['fullCategorySelect'] is defined %} checked {% endif %}> 
										<label for="t1"><span><span></span></span>{{ category['name'] }}</label>
									</div>
									<a class="categories-accordion__arrow categories-accordion__arrow--is-expanded" id="blockfilter-{{ category['id'] }}">
										<i class="icon"></i> Expand
									</a>
								</div>
								
								<div class="categories-accordion__body userTag-subfilters" id="subfilter-{{ category['id'] }}">
									{% for item, tag in category['tags'] %}
										<div class="form-checkbox pure-u-1-2">
											<input type="checkbox" id="tag-{{ tag['id']}}" name="searchTags[{{ tag['id']}}]" data-category-id="{{ tag['category_id'] }}" class="userFilter-tag" 
												{% if tag['inPreset'] is defined %}checked{% endif %}> 
											<label for="tag-{{ tag['id']}}" title="{{ tag['name'] }}"><span><span></span></span>{{ tag['name']}}</label>
										</div>
									{% endfor %}
								</div>
							</div>
						<!-- accordeon item -->
					{% endfor %}
					
				</div>
			</div>
		</aside>
		
		<div class="overlay" style="display:none;" id="filter-panel-overlay"></div>
</form>