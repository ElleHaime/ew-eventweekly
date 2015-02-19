<!-- new -->
<input id="tagIds" name="tagIds" type="hidden" value="{{ tagIds }}" />
<?php 
//var_dump($_GET);die;
?>


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

				{% for filter, category in userFilters %}

					<!-- accordion item -->
						<div class="categories-accordion__item">
							<div class="categories-accordion__head">
								<div class="categories-accordion__line"></div>
	
								<div class="form-checkbox">
									<input type="checkbox" id="cattag-{{ category['id']}}" class="userFilter-category" checked> 
									<!-- cattag -->
									<label for="t1"><span><span></span></span>{{ category['name'] }}</label>
								</div>

								<?php //var_dump($category);die; ?>
	
								<a class="categories-accordion__arrow categories-accordion__arrow--is-expanded" id="blockfilter-{{ category['id'] }}">
									<i class="icon"></i> Expand
								</a>
							</div>


							{% for name, value in _GET %}
							  <?php $keys_of_GET[] = $name; ?>
							{% endfor %}
							<?php
								//check if tags were set in get array
								$tags_in_GET = false;
								$str_keys_of_GET = implode("",$keys_of_GET);
								if ( strpos($str_keys_of_GET, "tag") ) {
									$tags_in_GET = true;
								}
							?>

							<?php //var_dump($member_categories['tag']['value']);die;?>
							
							<div class="categories-accordion__body userTag-subfilters" id="subfilter-{{ category['id'] }}">
							{% for tag in tags %}

                                {% if category['id'] == tag['category_id'] %}

                                    {% set checked = true %}
                                    {% if member_categories['tag']['value'] is defined %}
                                        {% for tagId in member_categories['tag']['value'] %}

                                            {% if tagId == tag['id'] %}
                                                {% set checked = false %}
                                            {% endif %}
                                        {% endfor %}
                                    {% endif %}
                                    <!-- if current tag is in GET -->
									<?php
										$checked_in_get = false;
										if (in_array( ("tag-" . $tag['id']), $keys_of_GET) ) {
											$checked_in_get = true;
										}

									?>
                                    
	                                    <div class="form-checkbox pure-u-1-2">
	                                    <!-- if tags set in get use them, else use user defined tags-->
											{% if tags_in_GET %}
												<input type="checkbox" id="tag-{{ tag['id']}}" name="tag-{{ tag['id']}}" data-category-id="{{ tag['category_id'] }}" class="userFilter-tag" {% if checked_in_get %}checked{% endif %}> 
											{% else %}
												<input type="checkbox" id="tag-{{ tag['id']}}" name="tag-{{ tag['id']}}" data-category-id="{{ tag['category_id'] }}" class="userFilter-tag" {% if checked %}checked{% endif %}> 
											{% endif %}
											
											<label for="tag-{{ tag['id']}}" title="{{ tag['name'] }}"><span><span></span></span>{{ tag['name']}}</label>
										</div>
									
									
																			
											<!-- <div class="form-checkbox pure-u-1-2">
												<input type="checkbox" id="tag-{{ tag['id']}}" data-category-id="{{ tag['category_id'] }}" class="userFilter-tag" checked> 
												<label for="t1" title="{{ tag['name'] }}"><span><span></span></span>{{ tag['name']}}</label>
											</div> -->
										
									
									

                                {% endif %}
                            {% endfor %}
                            </div>






						</div>
						
					{% endfor %}

				</div>
			</div>
		</aside>
		
		<div class="overlay" style="display:none;" id="filter-panel-overlay"></div>
</form>