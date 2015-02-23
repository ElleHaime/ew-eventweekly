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

    <?php echo $this->tag->stylesheetLink('/css/normalBootstrapDateTimepicker.min.css'); ?>

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

<?php if (isset($fbAppVersion)) { ?>
    <input id="fbAppVersion" type="hidden" value="<?php echo $fbAppVersion; ?>" />
<?php } ?>

<?php if (isset($fbAppSecret)) { ?>
    <input id="fbAppSecret" type="hidden" value="<?php echo $fbAppSecret; ?>" />
<?php } ?>
<!-- Header -->
		<header id="header">
		<?php if (isset($member->id)) { ?>
			<div class="top-line">
				<div class="container">

					<div class="user-bar">
						<div class="dropdown">
							<!-- button -->
							<a id="js-userBarDropDown" data-toggle="dropdown">
								<img 
									<?php if ($member->logo != '') { ?>
                                        src="<?php echo $member->logo; ?>"
                                    <?php } else { ?>
                                        src='/img/demo/h_back_1.jpg'
                                    <?php } ?> 
								class="user-bar__avatar" alt="Member logo" />

								<span class="user-bar__username">
									<?php if ($this->length($member->name)) { ?>
                                        <?php echo $member->name; ?>
                                    <?php } else { ?>
                                        <?php echo $member->email; ?>
                                    <?php } ?>
								</span> 
								<span class="caret"></span>
							</a>
							
							<!-- dropdown -->
							<ul class="dropdown-menu" role="menu" aria-labelledby="js-userBarDropDown">
								
                                <li>
                                	<a role="menuitem" tabindex="-1" href="/profile">Profile settings</a>
                                </li>
                                <li>
                                	<a role="menuitem" tabindex="-1" href="/campaign/list">Manage campaigns</a>
                                </li>
                                <li>
                                	<a role="menuitem" tabindex="-1" href="/event/list">
										<span class="btn-count" id="userEventsCreated"><?php echo $userEventsCreated; ?></span>
                                        <span class="btn-text">My events</span>
                                	</a>
                                </li>
                                <li>
                                	<a role="menuitem" tabindex="-1" href="/event/joined">
										<span class="btn-count" id="userEventsGoing"><?php echo $userEventsGoing; ?></span>
                                        <span class="btn-text">Events I’m attending</span>
                                	</a>
                                </li>
                                <li>
                                	<a role="menuitem" tabindex="-1" href="/event/liked">
										<span class="btn-count" id="userEventsLiked"><?php echo $userEventsLiked; ?></span>
                                        <span class="btn-text">Events I like</span>
                                	</a>
                                </li>
                                <li>
                                	<a role="menuitem" tabindex="-1" href="/event/friends">
										<span class="btn-count" id="userFriendsGoing"><?php echo $userFriendsGoing; ?></span>
                                        <span class="btn-text">Friends’ events</span>
                                	</a>
                                </li>

								<li>
									<a role="menuitem" tabindex="-1" href="/logout">Logout</a>
								</li>
							</ul>
						</div>
					</div>

					<a href="#" class="user-menu-button-open js-user-menu-button-open-trigger">Open Menu</a>

					<div class="clearfix"></div>

					<div class="user-menu-collapsed js-user-menu-collapsed">
						<div class="top-line__item">
							<a href="#" class="top-line__link">300 events</a>
						</div>

						<div class="top-line__item">
							<a href="#" class="top-line__link">Lorem</a>
						</div>

						<div class="top-line__item">
							<a href="#" class="top-line__link">Dolorsit</a>
						</div>

						<div class="top-line__item">
							<a href="#" class="top-line__button" onclick="location.href='/event/edit'">
								<i class="fa fa-plus"></i> Add event
							</a>
						</div>
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
						<a href="#" class="top-line__link"  onclick="return false;">Sign In</a>
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



<div class="page" >
	<div class="page__wrapper">
		<section id="content" class="container page-search" >

			<h1 class="page__title"><?php echo (empty($listTitle) ? ('Event list') : ($listTitle)); ?></h1>
			<div class="page__sort"></div>

				<div class="page-search__wrapper">
				<?php if (isset($list)) { ?>
					
				<!-- start container with events -->
					<div class="b-list-of-events-g">

						<?php $v78945441707659562161iterator = $list; $v78945441707659562161incr = 0; $v78945441707659562161loop = new stdClass(); $v78945441707659562161loop->length = count($v78945441707659562161iterator); $v78945441707659562161loop->index = 1; $v78945441707659562161loop->index0 = 1; $v78945441707659562161loop->revindex = $v78945441707659562161loop->length; $v78945441707659562161loop->revindex0 = $v78945441707659562161loop->length - 1; ?><?php foreach ($v78945441707659562161iterator as $event) { ?><?php $v78945441707659562161loop->first = ($v78945441707659562161incr == 0); $v78945441707659562161loop->index = $v78945441707659562161incr + 1; $v78945441707659562161loop->index0 = $v78945441707659562161incr; $v78945441707659562161loop->revindex = $v78945441707659562161loop->length - $v78945441707659562161incr; $v78945441707659562161loop->revindex0 = $v78945441707659562161loop->length - ($v78945441707659562161incr + 1); $v78945441707659562161loop->last = ($v78945441707659562161incr == ($v78945441707659562161loop->length - 1)); ?>
							<!-- item -->
							<div class="b-list-of-events-g__item pure-u-1-3 event-list-event" data-event-id=<?php echo $event->id; ?>>
								<div class="b-list-of-events-g__wrapper">
									<div class="b-list-of-events-g__picture">
										<a href="/<?php echo \Core\Utils\SlugUri::slug($event->name); ?>-<?php echo $event->id; ?>">
											<img src="<?php echo file_exists(ROOT_APP . 'public/upload/img/event/' . $event -> id . '/' . $event ->logo) ? '/upload/img/event/' . $event ->id . '/' . $event ->logo : '/img/logo200.png'; ?>" alt="<?php echo $event->name; ?>" class="lazy" data-original="<?php echo file_exists(ROOT_APP . 'public/upload/img/event/' . $event -> id . '/' . $event ->logo) ? '/upload/img/event/' . $event ->id . '/' . $event ->logo : '/img/logo200.png'; ?>">
										</a>

										<div class="like-buttons">  
											<div class="pure-u-1-2 like-buttons__item eventLikeBtn" data-id="<?php echo $event->id; ?>" data-status="1">
												<a href="/" class="ew-button" title="Like" >
													<i class="fa fa-thumbs-up"></i>
												</a>
											</div>

											<div class="pure-u-1-2 like-buttons__item eventDislikeBtn" data-id="<?php echo $event->id; ?>" data-status="0">
												<a href="#" class="ew-button" title="Dislike">
													<i class="fa fa-thumbs-down"></i>
												</a>
											</div>
										</div>
									</div>

									<div class="b-list-of-events-g__info">
										<h2 class="b-list-of-events-g__title">
											<a href="/<?php echo \Core\Utils\SlugUri::slug($event->name); ?>-<?php echo $event->id; ?>"><?php echo $event->name; ?></a>
										</h2>
										
										<div class="b-list-of-events-g__date">
											<?php if ($event->start_date != '0000-00-00') { ?>
                                                <?php echo \Core\Utils\DateTime::format($event->start_date, '%d %b %Y'); ?>
                                                
                                                <?php if ($event->end_date != '0000-00-00') { ?>
                                                 	- <?php echo \Core\Utils\DateTime::format($event->end_date, '%d %b %Y'); ?>
                                                 <?php } ?>
                                            <?php } ?>
										</div>

										<?php if ($this->length($event->category)) { ?>
											<div class="b-list-of-events-g__category">
												<i class="fa fa-tag"></i>
												<?php $v78945441707659562162iterator = $event->category; $v78945441707659562162incr = 0; $v78945441707659562162loop = new stdClass(); $v78945441707659562162loop->length = count($v78945441707659562162iterator); $v78945441707659562162loop->index = 1; $v78945441707659562162loop->index0 = 1; $v78945441707659562162loop->revindex = $v78945441707659562162loop->length; $v78945441707659562162loop->revindex0 = $v78945441707659562162loop->length - 1; ?><?php foreach ($v78945441707659562162iterator as $cat) { ?><?php $v78945441707659562162loop->first = ($v78945441707659562162incr == 0); $v78945441707659562162loop->index = $v78945441707659562162incr + 1; $v78945441707659562162loop->index0 = $v78945441707659562162incr; $v78945441707659562162loop->revindex = $v78945441707659562162loop->length - $v78945441707659562162incr; $v78945441707659562162loop->revindex0 = $v78945441707659562162loop->length - ($v78945441707659562162incr + 1); $v78945441707659562162loop->last = ($v78945441707659562162incr == ($v78945441707659562162loop->length - 1)); ?>
													<?php echo $cat->name; ?>
													<?php if (!$v78945441707659562162loop->last) { ?>, <?php } ?>
												<?php $v78945441707659562162incr++; } ?>
											</div>
										<?php } ?>
										<?php if (isset($event->location->city)) { ?>
											<div class="b-list-of-events-g__category">
												<i class="fa fa-map-marker"></i>
												<?php echo $event->location->city; ?>, <?php echo $event->location->country; ?>
											</div>
										<?php } ?>
										<div class="b-list-of-events-g__description" id="555">
											<p><?php echo mb_substr($this->escaper->escapeHtml(strip_tags($event->description)), 0, 250, 'utf-8') . $sep = (strlen($this->escaper->escapeHtml(strip_tags($event->description))) > 250) ? '...' : ''; ?></p>
										</div>


										<div class="footer">
											<div class="footer__item">
												<i class="fa fa-ticket"></i> Tickets: $100-$200
											</div>
											<?php if ($event->recurring == 7) { ?>
												<div class="footer__item"><i class="fa fa-retweet"></i> Weekly event</div>
											<?php } elseif ($event->recurring == 1) { ?>
												<div class="footer__item"><i class="fa fa-retweet"></i> Daily event</div>
											<?php } elseif ($event->recurring == 30) { ?>
												<div class="footer__item"><i class="fa fa-retweet"></i> Monthly event</div>
											<?php } ?>
										</div>

										<div class="actions">
											<a class="ew-button">
												<i class="fa fa-calendar"></i> Add to calendar
											</a>
											<a class="ew-button share-event" 
											   style="cursor:pointer;" 
											   data-event-source="/<?php echo \Core\Utils\SlugUri::slug($event->name); ?>-<?php echo $event->id; ?>"
											   data-image-source="<?php echo file_exists(ROOT_APP . 'public/upload/img/event/' . $event -> id . '/' . $event ->logo) ? '/upload/img/event/' . $event ->id . '/' . $event ->logo : '/img/logo200.png'; ?>"><i class="fa fa-share-alt"></i> Share
											</a>
										</div>
									</div>
								</div>

								<a class="b-list-of-events-g__link-detail" href="/<?php echo \Core\Utils\SlugUri::slug($event->name); ?>-<?php echo $event->id; ?>">Read More →</a>
							</div>
							
							<!-- item -->
							
							<!-- <?php if ($v78945441707659562161loop->index == 3) { ?>
								<div class="clearfix"></div>
							<?php } ?> -->
							
						<?php $v78945441707659562161incr++; } ?>							
							
					</div>

				<?php } ?>

						

				</div>
		</section>

<div class="page__wrapper_ajax_search"></div>


	</div>
	
			<!-- new -->
<input id="tagIds" name="tagIds" type="hidden" value="<?php echo $tagIds; ?>" />
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

				<?php $v78945441707659562161iterator = $userFilters; $v78945441707659562161incr = 0; $v78945441707659562161loop = new stdClass(); $v78945441707659562161loop->length = count($v78945441707659562161iterator); $v78945441707659562161loop->index = 1; $v78945441707659562161loop->index0 = 1; $v78945441707659562161loop->revindex = $v78945441707659562161loop->length; $v78945441707659562161loop->revindex0 = $v78945441707659562161loop->length - 1; ?><?php foreach ($v78945441707659562161iterator as $filter => $category) { ?><?php $v78945441707659562161loop->first = ($v78945441707659562161incr == 0); $v78945441707659562161loop->index = $v78945441707659562161incr + 1; $v78945441707659562161loop->index0 = $v78945441707659562161incr; $v78945441707659562161loop->revindex = $v78945441707659562161loop->length - $v78945441707659562161incr; $v78945441707659562161loop->revindex0 = $v78945441707659562161loop->length - ($v78945441707659562161incr + 1); $v78945441707659562161loop->last = ($v78945441707659562161incr == ($v78945441707659562161loop->length - 1)); ?>

					<!-- accordion item -->
						<div class="categories-accordion__item">
							<div class="categories-accordion__head">
								<div class="categories-accordion__line"></div>
	
								<div class="form-checkbox">
									<input type="checkbox" id="cattag-<?php echo $category['id']; ?>" class="userFilter-category" checked> 
									<!-- cattag -->
									<label for="t1"><span><span></span></span><?php echo $category['name']; ?></label>
								</div>

								<?php //var_dump($category);die; ?>
	
								<a class="categories-accordion__arrow categories-accordion__arrow--is-expanded" id="blockfilter-<?php echo $category['id']; ?>">
									<i class="icon"></i> Expand
								</a>
							</div>


							<?php $v78945441707659562162iterator = $_GET; $v78945441707659562162incr = 0; $v78945441707659562162loop = new stdClass(); $v78945441707659562162loop->length = count($v78945441707659562162iterator); $v78945441707659562162loop->index = 1; $v78945441707659562162loop->index0 = 1; $v78945441707659562162loop->revindex = $v78945441707659562162loop->length; $v78945441707659562162loop->revindex0 = $v78945441707659562162loop->length - 1; ?><?php foreach ($v78945441707659562162iterator as $name => $value) { ?><?php $v78945441707659562162loop->first = ($v78945441707659562162incr == 0); $v78945441707659562162loop->index = $v78945441707659562162incr + 1; $v78945441707659562162loop->index0 = $v78945441707659562162incr; $v78945441707659562162loop->revindex = $v78945441707659562162loop->length - $v78945441707659562162incr; $v78945441707659562162loop->revindex0 = $v78945441707659562162loop->length - ($v78945441707659562162incr + 1); $v78945441707659562162loop->last = ($v78945441707659562162incr == ($v78945441707659562162loop->length - 1)); ?>
							  <?php $keys_of_GET[] = $name; ?>
							<?php $v78945441707659562162incr++; } ?>
							<?php
								//check if tags were set in get array
								$tags_in_GET = false;
								$str_keys_of_GET = implode("",$keys_of_GET);
								if ( strpos($str_keys_of_GET, "tag") ) {
									$tags_in_GET = true;
								}
							?>

							<?php //var_dump($member_categories['tag']['value']);die;?>
							
							<div class="categories-accordion__body userTag-subfilters" id="subfilter-<?php echo $category['id']; ?>">
							<?php $v78945441707659562162iterator = $tags; $v78945441707659562162incr = 0; $v78945441707659562162loop = new stdClass(); $v78945441707659562162loop->length = count($v78945441707659562162iterator); $v78945441707659562162loop->index = 1; $v78945441707659562162loop->index0 = 1; $v78945441707659562162loop->revindex = $v78945441707659562162loop->length; $v78945441707659562162loop->revindex0 = $v78945441707659562162loop->length - 1; ?><?php foreach ($v78945441707659562162iterator as $tag) { ?><?php $v78945441707659562162loop->first = ($v78945441707659562162incr == 0); $v78945441707659562162loop->index = $v78945441707659562162incr + 1; $v78945441707659562162loop->index0 = $v78945441707659562162incr; $v78945441707659562162loop->revindex = $v78945441707659562162loop->length - $v78945441707659562162incr; $v78945441707659562162loop->revindex0 = $v78945441707659562162loop->length - ($v78945441707659562162incr + 1); $v78945441707659562162loop->last = ($v78945441707659562162incr == ($v78945441707659562162loop->length - 1)); ?>

                                <?php if ($category['id'] == $tag['category_id']) { ?>

                                    <?php $checked = true; ?>
                                    <?php if (isset($member_categories['tag']['value'])) { ?>
                                        <?php foreach ($member_categories['tag']['value'] as $tagId) { ?>

                                            <?php if ($tagId == $tag['id']) { ?>
                                                <?php $checked = false; ?>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                    <!-- if current tag is in GET -->
									<?php
										$checked_in_get = false;
										if (in_array( ("tag-" . $tag['id']), $keys_of_GET) ) {
											$checked_in_get = true;
										}

									?>
                                    
	                                    <div class="form-checkbox pure-u-1-2">
	                                    <!-- if tags set in get use them, else use user defined tags-->
											<?php if ($tags_in_GET) { ?>
												<input type="checkbox" id="tag-<?php echo $tag['id']; ?>" name="tag-<?php echo $tag['id']; ?>" data-category-id="<?php echo $tag['category_id']; ?>" class="userFilter-tag" <?php if ($checked_in_get) { ?>checked<?php } ?>> 
											<?php } else { ?>
												<input type="checkbox" id="tag-<?php echo $tag['id']; ?>" name="tag-<?php echo $tag['id']; ?>" data-category-id="<?php echo $tag['category_id']; ?>" class="userFilter-tag" <?php if ($checked) { ?>checked<?php } ?>> 
											<?php } ?>
											
											<label for="tag-<?php echo $tag['id']; ?>" title="<?php echo $tag['name']; ?>"><span><span></span></span><?php echo $tag['name']; ?></label>
										</div>
									
									
																			
											<!-- <div class="form-checkbox pure-u-1-2">
												<input type="checkbox" id="tag-<?php echo $tag['id']; ?>" data-category-id="<?php echo $tag['category_id']; ?>" class="userFilter-tag" checked> 
												<label for="t1" title="<?php echo $tag['name']; ?>"><span><span></span></span><?php echo $tag['name']; ?></label>
											</div> -->
										
									
									

                                <?php } ?>
                            <?php $v78945441707659562162incr++; } ?>
                            </div>






						</div>
						
					<?php $v78945441707659562161incr++; } ?>

				</div>
			</div>
		</aside>
		
		<div class="overlay" style="display:none;" id="filter-panel-overlay"></div>
</form>

	<div class="clearfix"></div>
		<div id="load_more" style="display:none;">
			<a class="ew-button" >
				<i class="fa fa-angle-double-down" ></i> Load more
			</a>
		</div>
	<div class="clearfix"></div>

	<img src="../img/preloader.gif" alt="" id='preloader' style="display: none;">
</div>

<div id="totalPagesJs" style="display: none;">
    <?php echo $totalPagesJs; ?>
</div>
<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>


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