<div class="events-list <%= event_category %>-category signleEventListElement" event-id="<%= event_id %>">
	<div class="row-fluid ">
		<div class="span12">
			<div class="event-one clearfix">
				<div class="event-one-img">
					<a href="/<%= event_url %>">
						 <img src="<%= event_image %>">
					</a>
				</div>

				<div class="event-one-text">
					<a href="/<%= event_url %>" class="name-link"><%= event_name%></a>
						<div class="date-list">
							<i class="icon-time"></i>
							<span class="date-start"><%= event_startDate %></span>
								starts at
								<span class="date-time"><%= event_startTime %></span>
						</div>
						<p>
							<%= event_description %><a href=/<%= event_url %>>Read more</a>
						</p>
						<div class="plans-box clearfix">
							<button class="btn eventLikeBtn" data-status="1" data-id="<%= event_id %>">Like</button>
							<button class="btn eventDislikeBtn" data-status="0" data-id="{{ event['id'] }}">Don't like</button>
						</div>

                                                {% set eVenue = 'Undefined place' %}
                                                {% if event['venue']['address'] is empty %}
                                                    {% if event['location']['city'] is empty %}
                                                        {% set eVenue = 'Undefined place' %}
                                                    {% else %}
                                                        {% set eVenue = event['location']['city'] %}
                                                    {% endif %}
                                                {% else %}
                                                    {% if event['location']['city'] is defined %}
                                                        {% set eVenue = event['location']['city']~', '~event['venue']['name']~', '~event['venue']['address'] %}
                                                    {% else %}
                                                        {% set eVenue = event['venue']['name']~' '~event['venue']['address'] %}
                                                    {% endif %}
                                                {% endif %}
                                                <div class="event-list-btn clearfix">
                                                    <div class=" place-address tooltip-text"  data-original-title="{{ eVenue }}" title="" rel="tooltip">
                                                        <span>
                                                            {% if event['venue']['address'] is empty %}
                                                                {% if event['location']['alias'] is empty %}
                                                                    Undefined place
                                                                {% else %}
                                                                    {{ event['location']['alias'] }}
                                                                {% endif %}
                                                            {% else %}
                                                                {{ event['venue']['address'] }}
                                                            {% endif %}
                                                        </span>
                                                    </div>
                                                    {% if event.site is defined %}
                                                        <div class="event-site clearfix">
                                                            <p>web-site : <a href="#">http://www.dpdp.com</a></p>
                                                        </div>
                                                    {% endif %}

                                                    {% if event['eid'] is defined %}
                                                        <div class="event-site clearfix">
                                                            <a target="_blank" href="https://www.facebook.com/events/{{ event['eid'] }}">Facebook link</a>
                                                        </div>
                                                    {% endif %}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>