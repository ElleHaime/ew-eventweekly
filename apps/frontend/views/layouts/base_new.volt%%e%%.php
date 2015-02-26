a:3:{i:0;s:15750:"<!DOCTYPE html>
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
							<p class="top-line__counter" >
							<?php if (isset($eventsTotal)) { ?>
							<span class="location-count"
	                              data-placement="bottom" 
	                              title=""
	                              id="events_count"
	                              data-original-title="All events <?php echo $eventsTotal; ?>"><?php echo $eventsTotal; ?>
                        	</span>
							<?php } else { ?>
							<span class="location-count"
	                              data-placement="bottom" 
	                              title=""
	                              id="events_count"
	                              data-original-title="0">Go find</span>
							<?php } ?>
							 events
							</p>
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

";s:7:"content";a:1:{i:0;a:4:{s:4:"type";i:357;s:5:"value";s:1:"
";s:4:"file";s:74:"/media/Data/Projects/EventWeekly/apps/frontend/views/layouts/base_new.volt";s:4:"line";i:54;}}i:1;s:339:"

		<!-- Footer -->
			<footer id="footer">
				<div class="container">

						<div class="footer_item footer_item_left">
							<a href="about">About us</a>
						</div>

						<div class="footer_item">
							<a href="contact">Contact</a>
						</div>
						
				</div>
				<div class="clearfix"></div>
			</footer>

</body>





</html>";}