<div class="categories-accordion" data-grid="{{ indexGrid }}" id="categories-accordion-{{ searchGrid }}" {% if indexGrid != searchGrid %}style="display:none;"{% endif %}>
	{% for index, category in userSearchFilters['userFilters'] %}
		<!-- accordeon item -->
			<div class="categories-accordion__item">
				<div class="categories-accordion__head">
					<div class="categories-accordion__line"></div>

					<div class="form-checkbox">
						<input type="checkbox" data-grid={{ indexGrid }} name="searchCategories[{{ category['id'] }}]" id="cattag-{{ category['id']}}" class="userFilter-category {{ indexGrid }}" 
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
							<input type="checkbox" data-grid="{{ indexGrid }}" id="tag-{{ tag['id']}}" name="searchTags[{{ tag['id']}}]" data-category-id="{{ tag['category_id'] }}" class="userFilter-tag {{ indexGrid }}" 
								{% if tag['inPreset'] is defined %}checked{% endif %}> 
							<label for="tag-{{ tag['id']}}" title="{{ tag['name'] }} {{ indexGrid }}s"><span><span></span></span>{{ tag['name']}}</label>
						</div>
					{% endfor %}
				</div>
			</div>
		<!-- accordeon item -->
	{% endfor %}
</div>