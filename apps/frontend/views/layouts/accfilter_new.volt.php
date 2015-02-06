		<div class="ew-filter-link" id="swithFilterPanel">
			<a href="#" class="Show Filter">Show Filter</a>	
		</div>
		
		<aside id="filters" class="b-filters sidebar-filters" style="display:none">
			<div class="b-filters__wrapper">
				<div class="b-filters__buttons">
					<a href="#" id="check-all" class="ew-button"><i class="fa fa-check-square-o"></i> Check all</a>
					<a href="#" id="uncheck-all" class="ew-button"><i class="fa fa-square-o"></i> Uncheck all</a>
					<a href="#" id="default-choise" class="ew-button"><i class="fa fa-star-o"></i> Default</a>
				</div>

				<div class="categories-accordion">

				<?php $v99498419612432958321iterator = $userFilters; $v99498419612432958321incr = 0; $v99498419612432958321loop = new stdClass(); $v99498419612432958321loop->length = count($v99498419612432958321iterator); $v99498419612432958321loop->index = 1; $v99498419612432958321loop->index0 = 1; $v99498419612432958321loop->revindex = $v99498419612432958321loop->length; $v99498419612432958321loop->revindex0 = $v99498419612432958321loop->length - 1; ?><?php foreach ($v99498419612432958321iterator as $filter => $category) { ?><?php $v99498419612432958321loop->first = ($v99498419612432958321incr == 0); $v99498419612432958321loop->index = $v99498419612432958321incr + 1; $v99498419612432958321loop->index0 = $v99498419612432958321incr; $v99498419612432958321loop->revindex = $v99498419612432958321loop->length - $v99498419612432958321incr; $v99498419612432958321loop->revindex0 = $v99498419612432958321loop->length - ($v99498419612432958321incr + 1); $v99498419612432958321loop->last = ($v99498419612432958321incr == ($v99498419612432958321loop->length - 1)); ?>

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
									<div class="categories-accordion__body userTag-subfilters" id="subfilter-<?php echo $category['id']; ?>">
										<?php $v99498419612432958322iterator = $category['tags']; $v99498419612432958322incr = 0; $v99498419612432958322loop = new stdClass(); $v99498419612432958322loop->length = count($v99498419612432958322iterator); $v99498419612432958322loop->index = 1; $v99498419612432958322loop->index0 = 1; $v99498419612432958322loop->revindex = $v99498419612432958322loop->length; $v99498419612432958322loop->revindex0 = $v99498419612432958322loop->length - 1; ?><?php foreach ($v99498419612432958322iterator as $subfilter => $tag) { ?><?php $v99498419612432958322loop->first = ($v99498419612432958322incr == 0); $v99498419612432958322loop->index = $v99498419612432958322incr + 1; $v99498419612432958322loop->index0 = $v99498419612432958322incr; $v99498419612432958322loop->revindex = $v99498419612432958322loop->length - $v99498419612432958322incr; $v99498419612432958322loop->revindex0 = $v99498419612432958322loop->length - ($v99498419612432958322incr + 1); $v99498419612432958322loop->last = ($v99498419612432958322incr == ($v99498419612432958322loop->length - 1)); ?>									
											<div class="form-checkbox pure-u-1-2">
												<input type="checkbox" id="tag-<?php echo $tag['id']; ?>" data-category-id="<?php echo $tag['category_id']; ?>" class="userFilter-tag" checked> 
												<label for="t1" title="<?php echo $tag['name']; ?>"><span><span></span></span><?php echo $tag['name']; ?></label>
											</div>
										<?php $v99498419612432958322incr++; } ?>
									</div>
							<?php } ?>
						</div>
						
					<?php $v99498419612432958321incr++; } ?>

				</div>
			</div>
		</aside>
		
		<div class="overlay" style="display:none;" id="filter-panel-overlay"></div>