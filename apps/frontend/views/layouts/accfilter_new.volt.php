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

				<?php $v17434829398458005311iterator = $userFilters; $v17434829398458005311incr = 0; $v17434829398458005311loop = new stdClass(); $v17434829398458005311loop->length = count($v17434829398458005311iterator); $v17434829398458005311loop->index = 1; $v17434829398458005311loop->index0 = 1; $v17434829398458005311loop->revindex = $v17434829398458005311loop->length; $v17434829398458005311loop->revindex0 = $v17434829398458005311loop->length - 1; ?><?php foreach ($v17434829398458005311iterator as $filter => $category) { ?><?php $v17434829398458005311loop->first = ($v17434829398458005311incr == 0); $v17434829398458005311loop->index = $v17434829398458005311incr + 1; $v17434829398458005311loop->index0 = $v17434829398458005311incr; $v17434829398458005311loop->revindex = $v17434829398458005311loop->length - $v17434829398458005311incr; $v17434829398458005311loop->revindex0 = $v17434829398458005311loop->length - ($v17434829398458005311incr + 1); $v17434829398458005311loop->last = ($v17434829398458005311incr == ($v17434829398458005311loop->length - 1)); ?>

					<!-- accordion item -->
						<div class="categories-accordion__item">
							<div class="categories-accordion__head">
								<div class="categories-accordion__line"></div>
	
								<div class="form-checkbox">
									<input type="checkbox" id="tag-<?php echo $category['id']; ?>" class="userFilter-category" checked> 
									<label for="t1"><span><span></span></span><?php echo $category['name']; ?></label>
								</div>
	
								<a href="#" class="categories-accordion__arrow categories-accordion__arrow--is-expanded" id="blockfilter-<?php echo $category['id']; ?>">
									<i class="icon"></i> Expand
								</a>
							</div>
	
							<?php if ($category['tags'] == !$empty) { ?>
							<!-- list of checkboxes -->
									<div class="categories-accordion__body" class="userTag-subfilters" id="subfilter-<?php echo $category['id']; ?>">
										<?php $v17434829398458005312iterator = $category['tags']; $v17434829398458005312incr = 0; $v17434829398458005312loop = new stdClass(); $v17434829398458005312loop->length = count($v17434829398458005312iterator); $v17434829398458005312loop->index = 1; $v17434829398458005312loop->index0 = 1; $v17434829398458005312loop->revindex = $v17434829398458005312loop->length; $v17434829398458005312loop->revindex0 = $v17434829398458005312loop->length - 1; ?><?php foreach ($v17434829398458005312iterator as $subfilter => $tag) { ?><?php $v17434829398458005312loop->first = ($v17434829398458005312incr == 0); $v17434829398458005312loop->index = $v17434829398458005312incr + 1; $v17434829398458005312loop->index0 = $v17434829398458005312incr; $v17434829398458005312loop->revindex = $v17434829398458005312loop->length - $v17434829398458005312incr; $v17434829398458005312loop->revindex0 = $v17434829398458005312loop->length - ($v17434829398458005312incr + 1); $v17434829398458005312loop->last = ($v17434829398458005312incr == ($v17434829398458005312loop->length - 1)); ?>									
											<div class="form-checkbox pure-u-1-2">
												<input type="checkbox" id="tag-<?php echo $tag['id']; ?>" data-category-id="<?php echo $tag['category_id']; ?>" class="userFilter-tag" checked> 
												<label for="t1" title="<?php echo $tag['name']; ?>"><span><span></span></span><?php echo $tag['name']; ?></label>
											</div>
										<?php $v17434829398458005312incr++; } ?>
									</div>
							<?php } ?>
						</div>
						
					<?php $v17434829398458005311incr++; } ?>

				</div>
			</div>
		</aside>
		
		<div class="overlay" style="display:none;" id="filter-panel-overlay"></div>