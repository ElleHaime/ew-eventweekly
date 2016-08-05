<input id="tagIds" name="tagIds" type="hidden" value="{{ tagIds }}" />
<input id="categoryIds" name="categoryIds" type="hidden" value="{{ categoryIds }}" />

<form action="" id="form2">
		<input id="personalPresetActive" name="personalPresetActive" type="hidden" value="{{ userSearch['personalPresetActive'] }}" />
		<input id="currentActiveGrid" name="currentActiveGrid" type="hidden" value="{{ searchGrid }}" />
		
		<div class="ew-filter-link" id="swithFilterPanel" {% if searchPage is not defined %}style="visibility:hidden;"{% endif %}>
			<a class="Show Filter" style="cursor:pointer;">Show Filter</a>	
		</div>
		
		<aside id="filters" class="b-filters sidebar-filters" style="display:none">
			<div class="b-filters__wrapper">
				<div class="b-filters__buttons">
					{% for switchIndex, switchGrid in searchGridsAll %}
						<a class="ew-button switchGridButton" id="switchGrid-{{ switchGrid }}" data-grid="{{ switchGrid }}">
							{% if switchGrid == searchGrid %}
								<i class="fa fa-check-square-o"></i>
							{% else %}
								<i class="fa fa-square-o"></i>
							{% endif %} 
							{{ switchGrid|capitalize }}s</a>
					{% endfor %}
				</div>
				<div class="b-filters__buttons">
					<a id="check-all" class="ew-button"><i class="fa fa-check-square-o"></i> Check all</a>
					<a id="uncheck-all" class="ew-button"><i class="fa fa-square-o"></i> Uncheck all</a>
					<a id="default-choise" class="ew-button"><i class="fa fa-star-o"></i> Default</a>
				</div>


				<!-- active user search (searchGrid value) -->
				{% set indexGrid = searchGrid %}
				{% set userSearchFilters = userSearch %}
				{# {% set userSearchFilters = userSearch['userFilters'] %} #}
				{% include 'layouts/accfilter_filter.volt' %}
				
				<!-- non-active user search -->
				{% if userSearchInactive is not empty %}
					{% for indexGrid, userSearchFilters in userSearchInactive %}
						{% include 'layouts/accfilter_filter.volt' %}
					{% endfor %}					 
				{% endif %} 
				
			</div>
		</aside>
		
		<div class="overlay" style="display:none;" id="filter-panel-overlay"></div>
</form>