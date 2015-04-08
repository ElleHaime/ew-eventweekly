<!-- Header -->
		<header id="header">
		{% if member.id is defined %}
			<div class="top-line">
				<div class="container">

					<div class="user-bar">
						<div class="dropdown">
							<!-- button -->
							<a id="js-userBarDropDown" data-toggle="dropdown">
								<img 
									{% if member.logo != '' %}
                                        src="{{ member.logo }}"
                                    {% else %}
                                        src='/img/demo/h_back_1.jpg'
                                    {% endif %} 
								class="user-bar__avatar" alt="Member logo" />

								<span class="user-bar__username">
									{% if member.name|length %}
                                        {{ member.name }}
                                    {% else %}
                                        {{ member.email }}
                                    {% endif %}
								</span> 
								<span class="caret"></span>
							</a>
							
							<!-- dropdown -->
							<ul class="dropdown-menu" role="menu" aria-labelledby="js-userBarDropDown">
								
                                <li>
                                	<a role="menuitem" tabindex="-1" href="/profile">Profile settings</a>
                                </li>
                                <!-- li>
                                	<a role="menuitem" tabindex="-1" href="/campaign/list">Manage campaigns</a>
                                </li -->
                                <li>
                                	<a role="menuitem" tabindex="-1" href="/event/list">
                                        <span class="btn-text">My events</span>
                                	</a>
                                </li>
                                <li>
                                	<a role="menuitem" tabindex="-1" href="/event/joined">
                                        <span class="btn-text">Events I’m attending</span>
                                	</a>
                                </li>
                                <li>
                                	<a role="menuitem" tabindex="-1" href="/event/liked">
                                        <span class="btn-text">Events I like</span>
                                	</a>
                                </li>
                                <li>
                                	<a role="menuitem" tabindex="-1" href="/event/friends">
                                        <span class="btn-text">Friends’ events</span>
                                	</a>
                                </li>

								<li>
									<a role="menuitem" tabindex="-1" href="/logout">Logout</a>
								</li>
							</ul>
						</div>
					</div>

					<a href="#" class="user-menu-button-open js-user-menu-button-open-trigger" onclick="location.href='/event/edit'">Open Menu</a>

					<div class="clearfix"></div>

					<div class="user-menu-collapsed js-user-menu-collapsed">
						<div class="top-line__item">
							<p class="top-line__counter" >
							{% if eventsTotal is defined %}
							<span class="location-count"
	                              data-placement="bottom" 
	                              title=""
	                              id="events_count"
	                              data-original-title="All events {{ eventsTotal }}">{{ eventsTotal }}
                        	</span>
							{% else %}
							<span class="location-count"
	                              data-placement="bottom" 
	                              title=""
	                              id="events_count"
	                              data-original-title="0">Go find</span>
							{% endif %}
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
						<a href="#" class="top-line__link fb-login-popup"  onclick="return false;">Sign In</a>
					</div>
					
                    
				</div>
			</div>
		{% endif %}
		
		
			<div class="middle-bg">
				<h1>
					<a class="logo" href="/"><strong>Event</strong>Weekly</a>
				</h1>
			</div>

			{% include 'layouts/searchform_new.volt' %}

		</header>