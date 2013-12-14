{% extends "layouts/base.volt" %}

{% block content %}
    <div class="padd_70"></div>
    <div class="container content_noBorder">
    
	<form method="post" enctype="multipart/form-data">
        <div class="row-fluid ">
            <div class="span12">
                <div class="row-fluid ">
                    <div class="span9">

                        <div class="add-event">
                            <h3 class="title-page">
                            	{% if event.id %}
                            		Edit event
                            	{% else %}
                            		Create event
                            	{% endif %}

                                {{ form.render('id') }}
                            </h3>
                            <div class="row-fluid">
                                <div class="span3">
                                    <div class="add-img">
                                    	{% if event.logo %}
                                        	<a href=""><img id='img-box' src="/upload/img/event/{{ event.logo }}" alt=""></a>
                                        {% else %}
											<a href=""><img id='img-box' src="/img/demo/q1.jpg" alt=""></a>
                                        {% endif %}
                                        <button class="btn" id ="add-img-btn" type="button">{{ form.label('logo')}}</button>
                                        <input id="add-img-upload" type="file" value="upload" style="display:none;">
                                    </div>
                                </div>
                                {{ form.render('logo')}}

                                <div class="span9">
                                
                                    <div class="input-div clearfix">
                                        {{ form.render('name')}}
                                    </div>
                                    
                                    <div class="input-div_date clearfix">
                                    
                                        <div id="date-picker-start" class="input-div_small">
                                            {{ form.render('start_date')}}
                                            <span class="add-on">
                                                <i data-time-icon="icon-date" data-date-icon="icon-calendar"></i>
                                            </span>
                                        </div>
                                        <div id="time-picker-start" class="input-div_small">
                                            {{ form.render('start_time') }}
                                            <span class="add-on">
                                                <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                                            </span>
                                        </div>
                                        
                                        <div id="date-picker-end" class="input-div_small">
                                            {{ form.render('end_date')}}
                                            <span class="add-on">
                                                <i data-time-icon="icon-date" data-date-icon="icon-calendar"></i>
                                            </span>
                                        </div>
                                        <div id="time-picker-end" class="input-div_small">
                                            {{ form.render('end_time') }}
                                            <span class="add-on">
                                                <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                                            </span>
                                        </div>
									                                        
                                        
                                        <div class="date-box" id="time-string" style="display:none;">
                                            <span id="date-start" class="date-start">12 Aug 2013</span>, starts at <span id="time-start" class="date-time">00:00:00</span><br>
                                            <span id="days-count" class="day-title">Event happens today</span>
                                        </div>
                                    </div>
                                    
                                    <div class="clear"></div>
                                    {{ form.render('description') }}
                                    <div class="btn-add_group clearfix">
                                        <button class="btn btn-cancel" id="btn-cancel">Cancel</button>
                                        <button class="btn" type="button" id="btn-preview">Preview</button>
                                        <button class="btn" type="submit" id="btn-submit">Save and publish</button>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="span3">
                        <div class="sidebar">
                            <div class="input-append">
                                {{ form.render('location') }}
                                {{ form.render('location_latitude')}}
                                {{ form.render('location_longitude')}}
                                <div class="search-queries hidden">
                                    <ul id="locations-list">
                                    </ul>
                                </div>
                                {#<i class="icon-map-marker"></i></button>#}
                            </div>
                            <div class="input-append">
                                {{ form.render('address') }}
                                {{ form.render('address-coords')}}
                                <div class="search-queries hidden">
                                    <ul id="addresses-list">
                                    </ul>
                                </div>
                                {#<i class="icon-map-marker"></i></button>#}
                            </div>

                            <div class="input-append">
                                  {{ form.render('venue') }}
								  {{ form.render('venue_latitude')}}
                                  {{ form.render('venue_longitude')}}
                                <div class="search-queries hidden">
                                    <ul id="venues-list">
                                    </ul>
                                </div>
                                {#<i class="icon-map-marker"></i></button>#}
                            </div>

                            <div class="input-append change-input">
                                {#
                                <button class="btn btn-primary" type="button">
                                    <i class="icon-map-marker"></i></button>
                                <input  type="text" placeholder="change location">
                                #}
                            </div>

                            <div class="input-append">
                                <input type="text" id="sites" placeholder="Event web site"><button class="btn btn-primary" id="add-web-site" type="button">+</button>
                                {{ form.render('event_site') }}
                            </div>
                            <div id="event-site-selected" class="event-site clearfix" {% if event.site is empty %}style="display:none;"{% endif %}>
                                <p>Event web-sites :</p>
                                {% if event.site %}
                                	{% for site in event.site %}
                                		<div class = "esite_elem">
                                			<a target="_blank" href="{{ site.url }}">{{ site.url }}</a>
                                			<a href="#" class="icon-remove-sign"></a>
                                		</div>
                                	{% endfor %}
                                {% endif %}
                            </div>



							<div class="event-site">
								<p> {{ form.label('event_category') }}</p>
									{{ form.render('event_category') }}
	                            <div id="event-category-selected" class="event-site clearfix" {% if event.category is empty %}style="display:none;"{% endif %}>
	                            	<input type="hidden" id="category" value="{% if event.category %}{% for key, name in event.category %}{{ key }},{% endfor %}{% endif %}">
	                                <p>Event categories :</p>
	                                {% if event.category %}
	                                	{% for key, name in event.category %}
	                                		<div class = "ecat_elem">
	                                			<div>
	                                				<label>{{ name }}</label>
	                                				<a href="#" class="icon-remove-sign" catid="{{ key }}"></a>
	                                			</div>
	                                		</div>
	                                	{% endfor %}
	                                {% endif %}
	                            </div>
							</div>
							
                            <div class="type-box">
                                <div class="event-site">
                                    <p>Type:</p>
                                    <div class="event-category clearfix">
                                        <span class="color-type gray ">festival</span>
                                        <span class="arrow"></span>
                                    </div>

                                </div>
                                <div class="event-site">
                                    <p>Genres:</p>
                                    <div class="event-category clearfix">
                                        <span class="color-type yellow">rock</span>
                                        <span class="arrow arrow_yellow"></span>
                                    </div>
                                    <div class="event-category clearfix">
                                        <span class="color-type yellow">gothic</span>
                                        <span class="arrow arrow_yellow"></span>
                                    </div>
                                </div>

                                <div class="event-site">
                                    <p>Venue:</p>
                                    <div class="event-category clearfix">
                                        <span class="color-type light_yellow">gothic</span>
                                        <span class="arrow arrow_light-yellow"></span>
                                    </div>
                                </div>

                                <div class="event-site tags-box clearfix">
                                    <div class="input-append">
                                        <input  type="text" placeholder="Tags">
                                        <button class="btn btn-primary" type="button">Ok</button>
                                    </div>
                                </div>
                            </div>

                            <div class="radio-box">
                                <p> {{ form.label('recurring') }}</p>
                                	{{ form.render('recurring') }}
                                <hr>
                                <div class="checkbox-block">
                                    <p> Choose promoter</p>
                                    {{ form.render('campaign_id') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </form>
    </div>

{% endblock %}