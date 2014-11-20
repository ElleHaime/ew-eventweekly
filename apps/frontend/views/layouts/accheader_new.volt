<!-- Header -->
		<header id="header">
		
		{% if member.id is defined %}
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
		{% else %}
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
		{% endif %}
		
		
			<div class="middle-bg">
				<h1>
					<a class="logo" href="/"><strong>Event</strong>Weekly</a>
				</h1>
			</div>

			<div class="filters">
				<div class="container">
						<div class="filters-form">
							<!-- search by event name -->
							<div class="filters-form__item filters-form__item--input-with-icon">
								<i class="fa fa-search"></i>
								<input name="event_name" class="filters-form__input" placeholder="Event or venue...">
							</div>
							<!-- search by location -->
							<div class="filters-form__item filters-form__item--input-with-icon">
								<i class="fa fa-map-marker"></i>
								<input name="event_location" class="filters-form__input" placeholder="Dublin">
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
										<li>
											<a role="menuitem" tabindex="-1" href="#">Venues</a>
										</li>
									</ul>
								</div>
							</div>
							<!-- datetime dropdown -->
							<div class="filters-form__item">
								<div class="dropdown">
								  <a class="filters-form__dropdown" id="js-selectDateTime">
								  	<i class="fa fa-calendar"></i>
								  	22 Sep
								  </a>
								</div>
							</div>
							<!-- map dropdown -->
							<div class="filters-form__item">
								<div class="dropdown">
									<!-- button -->
									<a class="filters-form__dropdown" id="js-selectEventType" data-toggle="dropdown">
										<i class="fa fa-globe"></i>
										List <span class="caret"></span>
									</a>
									
									<!-- dropdown -->
									<ul class="dropdown-menu" role="menu" aria-labelledby="js-selectEventType">
										<li>
											<a role="menuitem"  tabindex="-1" href="#">Map</a>
										</li>
										<li>
											<a role="menuitem" tabindex="-1" href="#">List</a>
										</li>
									</ul>
								</div>
							</div>

							<div class="filters-form__item filters-form__divider"></div>

							<div class="filters-form__item">
								<button href="#" class="filters-form__button">Show results</button>
							</div>

						</div>

				</div>
			</div>

		</header>