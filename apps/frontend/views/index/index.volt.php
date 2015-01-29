<!DOCTYPE html>
<html>
<head>
    <title>EventWeekly</title>
    <meta charset="utf-8"/>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
	<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">  

    <link type="image/ico" href="/img/128.ico" rel="icon">

    <?php if (isset($eventMetaData)) { ?>
        <?php if (isset($logo)) { ?>
            <meta property="og:image" content="<?php echo $logo; ?>"/>
        <?php } ?>
            <meta property="og:title" content="<?php echo $this->escaper->escapeHtml($eventMetaData->name); ?>"/>
            <meta property="og:description" content="<?php echo strip_tags($this->escaper->escapeHtml($eventMetaData->description)); ?>"/>
    <?php } else { ?>
        <?php if (isset($logo)) { ?>
            <meta property="og:image" content="<?php echo $logo; ?>"/>
            <meta property="og:title" content="EventWeekly"/>
        <?php } ?>
    <?php } ?>

	<?php if (isset($searchResult)) { ?>
		<?php if (isset($list)) { ?>
			<script type="text/javascript">
		        window.searchResults = <?php echo $list; ?>;
		    </script>
		<?php } ?>
	<?php } ?>

    <?php echo $this->tag->stylesheetLink('/_new-layout-eventweekly/css/style.css'); ?>
    <?php echo $this->tag->stylesheetLink('/_new-layout-eventweekly/libs/idangerous.swiper/idangerous.swiper.min.css'); ?>
    <?php echo $this->tag->stylesheetLink('/_new-layout-eventweekly/libs/bootstrap/bootstrap.css'); ?>
    <?php echo $this->tag->stylesheetLink('/_new-layout-eventweekly/libs/bootstrap/bootstrap-theme.css'); ?>

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>

    <script data-main="/js/config" src="/js/requirePlugins/require.js"></script>
</head>

<body>
<?php if (isset($eventPreview)) { ?>
    <div class="preview_overlay" style="height: 100%;width: 100%;z-index: 10000;top:0;left:0;position:fixed;"></div>
<?php } ?>

<div id="fb-root" display="none;"></div>

<div style="display:none;" id="current_location" latitude="<?php echo $location->latitude; ?>" longitude="<?php echo $location->longitude; ?>"></div>

<input type="hidden" id="popupRedirect" value="">

<?php if (isset($flashMsgText)) { ?>
	<div style="display:none;" id="splash_messages" flashMsgText="<?php echo $flashMsgText; ?>" flashMsgType="<?php echo $flashMsgType; ?>"></div>
<?php } ?>

<?php if (isset($location_conflict)) { ?>
	<div style="display:none;" id="conflict_location" location_conflict="<?php echo $location_conflict; ?>"></div>
<?php } ?>

<?php if (isset($external_logged)) { ?>
    <div id="external_logged" extname="<?php echo $external_logged; ?>" display="none;"></div>
<?php } ?>

<?php if (isset($permission_base)) { ?>
	<input type="hidden" id="permission_base" value="<?php echo $permission_base; ?>">
<?php } else { ?>
	<input type="hidden" id="permission_base" values = "0">
<?php } ?>

<?php if (isset($permission_publish)) { ?>
    <input type="hidden" id="permission_publish" value="<?php echo $permission_publish; ?>">
<?php } else { ?>
	<input type="hidden" id="permission_publish" value="0">
<?php } ?>

<?php if (isset($permission_manage)) { ?>
    <input type="hidden" id="permission_manage" value="<?php echo $permission_manage; ?>">
<?php } else { ?>
	<input type="hidden" id="permission_manage" value="0">
<?php } ?>

<?php if (isset($acc_external)) { ?>
    <input type="hidden" id="member_ext_uid" value="<?php echo $acc_external->account_uid; ?>">
<?php } ?>

<?php if (isset($acc_synced)) { ?>
    <input type="hidden" id="acc_synced" value="1">
<?php } ?>

<?php if (isset($member->id)) { ?>
    <input id="isLogged" type="hidden" value="1" />
<?php } else { ?>
    <input id="isLogged" type="hidden" value="0" />
<?php } ?>

<?php if (isset($isMobile)) { ?>
    <input id="isMobile" type="hidden" value="<?php echo $isMobile; ?>" />
<?php } ?>

<?php if (isset($fbAppId)) { ?>
    <input id="fbAppId" type="hidden" value="<?php echo $fbAppId; ?>" />
<?php } ?>

<?php if (isset($fbAppSecret)) { ?>
    <input id="fbAppSecret" type="hidden" value="<?php echo $fbAppSecret; ?>" />
<?php } ?>
<!-- Header -->
		<header id="header">
		
		<?php if (isset($member->id)) { ?>
			<div class="top-line">
				<div class="container">
					
					<div class="top-line__item top-line__item--text">Never miss <strong>events</strong> in Dublin!</div>
					<div class="top-line__item">
						<a href="#" class="top-line__button">
							<i class="fa fa-sign-in"></i> Sign Up today
						</a>
					</div>
					<div class="top-line__item top-line__item--divider">
						<a href="#" class="top-line__link">Sign In</a>
					</div>

				</div>
			</div>
		<?php } else { ?>
			<div class="top-line">
				<div class="container">
					
					<div class="top-line__item top-line__item--text">Never miss <strong>events</strong> in Dublin!</div>
					<div class="top-line__item">
						<a href="/signup" class="top-line__button">
							<i class="fa fa-sign-in"></i> Sign Up today
						</a>
					</div>
					<div class="top-line__item top-line__item--divider">
						<a href="#" class="top-line__link" onclick="return false;">Sign In</a>
					</div>

				</div>
			</div>
		<?php } ?>
		
		
			<div class="middle-bg">
				<h1>
					<a class="logo" href="/"><strong>Event</strong>Weekly</a>
				</h1>
			</div>

			<form action="/search" method="get" class="form-horizontal" id="topSearchForm">

		<div class="filters">
				<div class="container">
						<div class="filters-form">
						
							<!-- search by event name -->
							<div class="filters-form__item filters-form__item--input-with-icon">
								<i class="fa fa-search"></i>
						        <?php if (isset($userSearch) && isset($userSearch['searchTitle'])) { ?>
						            <?php $searchTitle = $userSearch['searchTitle']; ?>
						        <?php } else { ?>
						            <?php $searchTitle = ''; ?>
						        <?php } ?>
						        <?php echo $searchForm->render('searchTitle', array('class' => 'filters-form__input', 'placeholder' => 'Event or venue...', 'value' => $searchTitle)); ?>
							</div>
							
							<!-- search by location -->
							<div class="filters-form__item filters-form__item--input-with-icon">
								<i class="fa fa-map-marker"></i>
								<?php if (isset($userSearch) && isset($userSearch['searchLocation'])) { ?>
						            <?php $searchLocation = $userSearch['searchLocation']; ?>
						            <?php $searchLocationPlaceholder = $userSearch['searchLocation']; ?>
						        <?php } else { ?>
						            <?php $searchLocation = ''; ?>
						            <?php $searchLocationPlaceholder = 'Dublin'; ?>
						        <?php } ?>
						        <input type="text" data-location-chosen="false" id="searchLocationField" name="searchLocationField" class="filters-form__input" placeholder="<?php echo $searchLocationPlaceholder; ?>" value="<?php echo $searchLocation; ?>"/>
						        
				                <?php if (isset($userSearch) && isset($userSearch['searchLocationLatMin'])) { ?>
						            <?php $searchLocationLatMin = $userSearch['searchLocationLatMin']; ?>
						        <?php } else { ?>
						            <?php $searchLocationLatMin = ''; ?>
						        <?php } ?>
						        <?php echo $searchForm->render('searchLocationLatMin', array('value' => $searchLocationLatMin)); ?>
						
						        <?php if (isset($userSearch) && isset($userSearch['searchLocationLngMin'])) { ?>
						            <?php $searchLocationLngMin = $userSearch['searchLocationLngMin']; ?>
						        <?php } else { ?>
						            <?php $searchLocationLngMin = ''; ?>
						        <?php } ?>
						        <?php echo $searchForm->render('searchLocationLngMin', array('value' => $searchLocationLngMin)); ?>
						
						        <?php if (isset($userSearch) && isset($userSearch['searchLocationLatMax'])) { ?>
						            <?php $searchLocationLatMax = $userSearch['searchLocationLatMax']; ?>
						        <?php } else { ?>
						            <?php $searchLocationLatMax = ''; ?>
						        <?php } ?>
						        <?php echo $searchForm->render('searchLocationLatMax', array('value' => $searchLocationLatMax)); ?>
						
						        <?php if (isset($userSearch) && isset($userSearch['searchLocationLngMax'])) { ?>
						            <?php $searchLocationLngMax = $userSearch['searchLocationLngMax']; ?>
						        <?php } else { ?>
						            <?php $searchLocationLngMax = ''; ?>
						        <?php } ?>
						        <?php echo $searchForm->render('searchLocationLngMax', array('value' => $searchLocationLngMax)); ?>
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
										<!-- li>
											<a role="menuitem" tabindex="-1" href="#">Venues</a>
										</li -->
									</ul>
								</div>
							</div>
							
							<!-- datetime dropdown -->
							<div class="filters-form__item">
								<div class="dropdown">
								  <a class="filters-form__dropdown" id="js-selectDateTime">
								  	<i class="fa fa-calendar"></i>
								  	 <?php if (isset($userSearch) && isset($userSearch['searchStartDate'])) { ?>
						                <?php $searchStartDate = $userSearch['searchStartDate']; ?>
						            <?php } else { ?>
						                <?php $searchStartDate = date('Y-m-d'); ?>
						            <?php } ?>
						            <span id="searchPanel-startDate" name="start_date"><?php echo $searchStartDate; ?></span>
								  	<?php echo $searchForm->render('searchStartDate', array('value' => $searchStartDate)); ?>
								  </a>
								</div>
							</div>
							
							<!-- map dropdown -->
							<div class="filters-form__item">
							 	<?php if (isset($userSearch['searchTypeResult'])) { ?>
						            <?php $searchTypeResult = $userSearch['searchTypeResult']; ?>
						        <?php } else { ?>
						            <?php $searchTypeResult = 'List'; ?>
						        <?php } ?>
								<div class="dropdown">
									<!-- button -->
									<a class="filters-form__dropdown" id="js-selectEventType" data-toggle="dropdown">
										<i class="fa fa-globe"></i>
										<span id="searchTypeResultCurrent"><?php echo $searchTypeResult; ?></span>
										<span class="caret"></span>
									</a>
									
									<!-- dropdown -->
									<ul class="dropdown-menu" role="menu" aria-labelledby="js-selectEventType" id="searchTypeResultMenu">
										<?php foreach ($searchTypes as $index => $type) { ?>
											<?php if ($type != $searchTypeResult) { ?>
												<li>
													<a role="menuitem" tabindex="-1" data-value="<?php echo $type; ?>"><?php echo $type; ?></a>
												</li>
											<?php } ?>
										<?php } ?>
									</ul>
									<?php echo $searchForm->render('searchTypeResult', array('value' => $searchTypeResult)); ?>
								</div>
							</div>

							<div class="filters-form__item filters-form__divider"></div>

							<div class="filters-form__item">
								<button type="submit" id="searchSubmit" class="filters-form__button">Show results</button>
							</div>

						</div>

				</div>
			</div>
			
</form>

		</header>


			<section id="content" class="container page page-main">

			 <?php if (isset($featuredEvents)) { ?>
			 	<?php if (isset($featuredEvents[0])) { ?>
					<div class="b-popular-events-slider">
						<div class="js-main-popular-events-slider">
							<?php foreach ($featuredEvents[0] as $index => $event) { ?>
								<!-- start slider item -->
								<div class="b-popular-events-slider__slide b-slide js-main-popular-events-slider-slide">
		
									<div class="b-slide__picture">
										<?php if (isset($event->cover)) { ?>
											<img src="<?php echo file_exists(ROOT_APP . 'public/upload/img/event/' . $event->cover -> event_id . '/cover/' . $event->cover ->image) ? '/upload/img/event/' . $event->cover ->event_id . '/cover/' . $event->cover ->image : '/img/logo200.png'; ?>" alt="<?php echo $event->name; ?>">
										<?php } else { ?>
											<img src="<?php echo file_exists(ROOT_APP . 'public/upload/img/event/' . $event -> id . '/' . $event ->logo) ? '/upload/img/event/' . $event ->id . '/' . $event ->logo : '/img/logo200.png'; ?>" alt="<?php echo $event->name; ?>">
										<?php } ?>
									</div>
									
									<div class="b-slide__info">
										<h2 class="b-slide__title">
											<a href="#"><?php echo $event->name; ?></a>
										</h2>
		
										<div class="b-slide__description">
											<p>
												<?php if ($event->start_date != '0000-00-00') { ?>
													<time datetime="2014-09-21T22:00+00:00"><?php echo \Core\Utils\DateTime::format($event->start_date, '%d %b %Y'); ?>
													<?php if (\Core\Utils\DateTime::format($event->start_date, '%R') != '00:00') { ?>, <?php echo \Core\Utils\DateTime::format($event->start_date, '%R'); ?><?php } ?></time>
												<?php } ?>
												<?php if (isset($event->venue->name)) { ?>
													- <?php echo strip_tags($event->venue->name); ?>
												<?php } ?>
											</p>
										</div>
										
										<a href="#" class="b-slide__button-detail">Details →</a>
		
									</div>
		
								</div>
								<!-- end slider item -->
							<?php } ?>						
						</div>
	
						<!-- swiper dots -->
						<div class="b-popular-events-slider__dots js-main-popular-events-slider-dots"></div>
	
					</div>
				</div>
			  <?php } ?>

			  <?php if (isset($featuredEvents[1])) { ?>
				<div class="list-of-events col-3 container">
	
					<div class="header">
						<h2 class="header__title">
							<strong>What’s on in Dublin</strong>
							<span class="divider"></span> 
							<a href="#">Featured events</a>
						</h2>
	
						<a href="#" class="header__link-show-more">Show more What’s on in Dublin</a>
					</div>
	
					<div class="clearfix"></div>
	
					<div class="list-of-events__container featured" id="list-of-events-featured">
						<a class="b-gallery__arrow b-gallery__arrow--prev js-b-gallery-arrow-prev" id="list-of-events-featured-prev">
		                	<i class="fa fa-chevron-left"></i>
		                </a>
		                <a class="b-gallery__arrow b-gallery__arrow--next js-b-gallery-arrow-next" id="list-of-events-featured-next">
		                	<i class="fa fa-chevron-right"></i>
		                </a>
					<?php foreach ($featuredEvents[1] as $index => $event) { ?>
						<!-- item start -->
							<div class="list-of-events__item pure-u-1-3">
								<div class="list-of-events__picture">
									<img src="<?php echo file_exists(ROOT_APP . 'public/upload/img/event/' . $event -> id . '/' . $event ->logo) ? '/upload/img/event/' . $event ->id . '/' . $event ->logo : '/img/logo200.png'; ?>" alt="<?php echo $event->name; ?>">
								</div>				
		
								<div class="list-of-events__info">
									<h3 class="list-of-events__title">
										<a href="#"><?php echo $event->name; ?></a>
									</h3>
		
									<div class="list-of-events__description">
										<p>
											<?php if ($event->start_date != '0000-00-00') { ?>
												<time datetime="2014-09-21T22:00+00:00"><?php echo \Core\Utils\DateTime::format($event->start_date, '%d %b %Y'); ?>
												<?php if (\Core\Utils\DateTime::format($event->start_date, '%R') != '00:00') { ?>, <?php echo \Core\Utils\DateTime::format($event->start_date, '%R'); ?><?php } ?></time>
											<?php } ?>
											<?php if (isset($event->venue->name)) { ?>
												- <?php echo strip_tags($event->venue->name); ?>
											<?php } ?>
										</p>
									</div>
								</div>
							</div>
						<!-- item end -->
					<?php } ?>
					</div>
					<div class="clearfix"></div>
	
				</div>
			<?php } ?>
		  <?php } ?>


		  <?php if (isset($trendingEvents)) { ?>
			<div class="list-of-events col-4 container">

				<div class="header">
					<h2 class="header__title">
						<strong>What’s on in Dublin</strong>
						<span class="divider"></span> 
						<a href="#">Trending events</a> 
					</h2>

					<a href="#" class="header__link-show-more">Show more What’s on in Dublin</a>
				</div>

				<div class="clearfix"></div>

				<div class="list-of-events__container trending" id="list-of-events-trending">
					<a class="b-gallery__arrow b-gallery__arrow--prev js-b-gallery-arrow-prev" id="list-of-events-trending-prev">
	                	<i class="fa fa-chevron-left"></i>
	                </a>
	                <a class="b-gallery__arrow b-gallery__arrow--next js-b-gallery-arrow-next" id="list-of-events-trending-next">
	                	<i class="fa fa-chevron-right"></i>
	                </a>
	                
	                <?php foreach ($trendingEvents as $index => $event) { ?>
						<!-- item start -->
							<div class="list-of-events__item pure-u-1-4">
								<div class="list-of-events__picture">
									<img src="<?php echo file_exists(ROOT_APP . 'public/upload/img/event/' . $event -> id . '/' . $event ->logo) ? '/upload/img/event/' . $event ->id . '/' . $event ->logo : '/img/logo200.png'; ?>" alt="<?php echo $event->name; ?>">
								</div>				
		
								<div class="list-of-events__info">
									<h3 class="list-of-events__title">
										<a href="#"><?php echo $event->name; ?></a>
									</h3>
		
									<div class="list-of-events__description">
										<p>
											<?php if ($event->start_date != '0000-00-00') { ?>
												<time datetime="2014-09-21T22:00+00:00"><?php echo \Core\Utils\DateTime::format($event->start_date, '%d %b %Y'); ?>
												<?php if (\Core\Utils\DateTime::format($event->start_date, '%R') != '00:00') { ?>, <?php echo \Core\Utils\DateTime::format($event->start_date, '%R'); ?><?php } ?></time>
											<?php } ?>
											<?php if (isset($event->venue->name)) { ?>
												- <?php echo strip_tags($event->venue->name); ?>
											<?php } ?>
										</p>
									</div>
								</div>
							</div>
						<!-- item end -->
					<?php } ?>
				</div>
			<?php } ?>

			<div class="clearfix"></div>

		</section>


		<div class="ew-filter-link">
			<a href="#" class="Show Filter">Show Filter</a>	
		</div>


		<!-- Footer -->
			<footer id="footer">
				<div class="container">
					<div class="widgets">
						<div class="widget pure-u-1-4">
							<h3 class="widget__title">Lorem ipsum</h3>
							<div class="widget__text">
								<ul>
									<li><a href="#">Phasellus non nisi</a></li>
									<li><a href="#">Viverra, pharetra est quis</a></li>
									<li><a href="#">Congue erat. </a></li>
									<li><a href="#">Mauris sapien elit</a></li>
									<li><a href="#">Sagittis eget viverra</a></li>
								</ul>
							</div>
						</div>

						<div class="widget pure-u-1-4">
							<h3 class="widget__title">Lorem ipsum</h3>
							<div class="widget__text">
								<ul>
									<li><a href="#">Phasellus non nisi</a></li>
									<li><a href="#">Viverra, pharetra est quis</a></li>
									<li><a href="#">Congue erat. </a></li>
									<li><a href="#">Mauris sapien elit</a></li>
									<li><a href="#">Sagittis eget viverra</a></li>
								</ul>
							</div>
						</div>

						<div class="widget pure-u-1-4">
							<h3 class="widget__title">Lorem ipsum</h3>
							<div class="widget__text">
								<ul>
									<li><a href="#">Phasellus non nisi</a></li>
									<li><a href="#">Viverra, pharetra est quis</a></li>
									<li><a href="#">Congue erat. </a></li>
									<li><a href="#">Mauris sapien elit</a></li>
									<li><a href="#">Sagittis eget viverra</a></li>
								</ul>
							</div>
						</div>

						<div class="widget pure-u-1-4">
							<h3 class="widget__title">Follow us</h3>
							<div class="widget__text">
								<ul class="social">
									<li class="social__item">
										<a href="#"><i class="fa fa-twitter-square"></i>Twitter</a>
									</li>
									<li class="social__item">
										<a href="#"><i class="fa fa-facebook-square"></i>Facebook</a>
									</li>
									<li class="social__item">
										<a href="#"><i class="fa fa-rss-square"></i>RSS</a>
									</li>
								</ul>
							</div>
						</div>

					</div>
					<!-- end .widgets -->
				</div>
			</footer>

</body>





</html>