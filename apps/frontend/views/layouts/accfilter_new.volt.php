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

				<?php $v84289028554918118871iterator = $userFilters; $v84289028554918118871incr = 0; $v84289028554918118871loop = new stdClass(); $v84289028554918118871loop->length = count($v84289028554918118871iterator); $v84289028554918118871loop->index = 1; $v84289028554918118871loop->index0 = 1; $v84289028554918118871loop->revindex = $v84289028554918118871loop->length; $v84289028554918118871loop->revindex0 = $v84289028554918118871loop->length - 1; ?><?php foreach ($v84289028554918118871iterator as $filter => $category) { ?><?php $v84289028554918118871loop->first = ($v84289028554918118871incr == 0); $v84289028554918118871loop->index = $v84289028554918118871incr + 1; $v84289028554918118871loop->index0 = $v84289028554918118871incr; $v84289028554918118871loop->revindex = $v84289028554918118871loop->length - $v84289028554918118871incr; $v84289028554918118871loop->revindex0 = $v84289028554918118871loop->length - ($v84289028554918118871incr + 1); $v84289028554918118871loop->last = ($v84289028554918118871incr == ($v84289028554918118871loop->length - 1)); ?>

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
										<?php $v84289028554918118872iterator = $category['tags']; $v84289028554918118872incr = 0; $v84289028554918118872loop = new stdClass(); $v84289028554918118872loop->length = count($v84289028554918118872iterator); $v84289028554918118872loop->index = 1; $v84289028554918118872loop->index0 = 1; $v84289028554918118872loop->revindex = $v84289028554918118872loop->length; $v84289028554918118872loop->revindex0 = $v84289028554918118872loop->length - 1; ?><?php foreach ($v84289028554918118872iterator as $subfilter => $tag) { ?><?php $v84289028554918118872loop->first = ($v84289028554918118872incr == 0); $v84289028554918118872loop->index = $v84289028554918118872incr + 1; $v84289028554918118872loop->index0 = $v84289028554918118872incr; $v84289028554918118872loop->revindex = $v84289028554918118872loop->length - $v84289028554918118872incr; $v84289028554918118872loop->revindex0 = $v84289028554918118872loop->length - ($v84289028554918118872incr + 1); $v84289028554918118872loop->last = ($v84289028554918118872incr == ($v84289028554918118872loop->length - 1)); ?>									
											<div class="form-checkbox pure-u-1-2">
												<input type="checkbox" id="tag-<?php echo $tag['id']; ?>" data-category-id="<?php echo $tag['category_id']; ?>" class="userFilter-tag" checked> 
												<label for="t1" title="<?php echo $tag['name']; ?>"><span><span></span></span><?php echo $tag['name']; ?></label>
											</div>
										<?php $v84289028554918118872incr++; } ?>
									</div>
							<?php } ?>
						</div>
						
					<?php $v84289028554918118871incr++; } ?>

				</div>
			</div>
		</aside>
		
		<div class="overlay" style="display:none;" id="filter-panel-overlay"></div>