{% extends "layouts/base_new.volt" %}

{% block content %}


    <div class="page">
    <section id="content" class="container page-search" >

    <div id="add_event_left">
    
        <form method="post" enctype="multipart/form-data" name="addEventForm">

            <div class="row">
                <div class="span12">
                    <div class="add-event_i clearfix">

                        <div class="row-fluid">
                            <div class="span12">
                                <div class="padd_30"></div>
                                <h1 class="page__title">
                                    {% if editEvent is defined %}
                                        Edit event
                                    {% else %}
                                        Create event
                                    {% endif %}

                                    {{ form.render('id') }}
                                </h1>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span12">

                                <div id="content_right">
                                    <div id="content_right_inner">
                                        <div id="content_center">
                                            <div id="content-box">
                                                <div class="form-center clearfix">
                                                    <div class="input-div clearfix">
                                                         {{ form.render('name') }} 
                                                    </div>

                                                    <div class="input-div_date clearfix">
                                                        <div class="date-picker_one clearfix">
                                                            <div id="date-picker-start" class="input-div_small">
                                                                <!-- input type="text" id="start_date" name="start_date" data-format="dd/MM/yyyy" data-type="event_date" placeholder="Start date" autocomplete="off" class="input_add_event_date" -->
                                                                {{ form.render('start_date') }}
                                                            <span class="add-on"></span>
                                                            </div>
                                                        </div>
                                                        <div class="date-picker_one clearfix">
                                                            <div id="date-picker-end" class="input-div_small">
                                                                <!-- input type="text" id="end_date" name="end_date" data-format="dd/MM/yyyy" data-type="event_date" placeholder="End date" autocomplete="off" class="input_add_event_date" -->
                                                                {{ form.render('end_date') }}
                                                            <span class="add-on"></span>
                                                            </div>
                                                        </div>

                                                        <div class="date-box clearfix">
                                                            <label class="add_event_label_to_checkbox">
                                                                {{ form.render('event_status') }} Publish event immediately
                                                            </label>
                                                        </div>

                                                        <div class="date-box" id="time-string" style="display:none;">
                                                            <span id="date-start" class="date-start">12 Aug 2013</span>, starts at <span id="time-start" class="date-time">00/span><br>
                                                            <span id="days-count" class="day-title">Event happens today</span>
                                                        </div>
                                                    </div>

                                                    
                                                    {{ form.render('description') }}

                                                    <div class="input-div clearfix">
                                                        {{ form.render('tickets_url') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="sidebar-box">
                                            <div class="input-append">
                                                <label for="location">
                                                    {{ form.render('location') }}
                                                    <button class="btn" type="button" style="display:none;"><i class="icon-place-marker"></i></button>
                                                </label>


                                                {{ form.render('location_latitude') }}
                                                {{ form.render('location_longitude') }}
                                                {{ form.render('location_id') }}
                                                <div class="search-queries hidden">
                                                    <ul id="locations-list">
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="input-append">
                                                <label for="address">
                                                    {{ form.render('address') }}
                                                    <button class="btn" type="button" style="display:none;"><i class="icon-place-marker"></i></button>
                                                </label>

                                                {{ form.render('address-coords') }}
                                                <div class="search-queries hidden">
                                                    <ul id="addresses-list">
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="input-append">
                                                <label for="venue">
                                                    {{ form.render('venue') }}
                                                    <button class="btn" type="button" style="display:none;"><i class="icon-place-marker"></i></button>
                                                </label>

                                                {{ form.render('venue_latitude') }}
                                                {{ form.render('venue_longitude') }}
                                                <div class="search-queries hidden">
                                                    <ul id="venues-list">
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="input-append">

                                                <input type="text" id="sites" placeholder="Event web site"/>
                                                <button class="ew-button" id="add-web-site" type="button">Add</button>

                                                <div class="warning-box"
                                                     style="background: green; width: 200px; height: 200px; display: none; position: absolute; z-index: 101; top:43px; right:0">
                                                </div>

                                            </div>
                                            <div id="event-site-selected" class="event-site clearfix" {% if not (event.site|length) %} style="display:none;" {% endif %}>
                                                <input type="hidden" id="event_site" name="event_site" value="{% if event.site|length %}{% for es in event.site %}{{ es.url }},{% endfor %}{% endif %}">
                                                <p>Event web-sites:</p>
                                                {% if event.site|length %}

                                                    {% for site in event.site %}
														<div class = "ecat_elem">
                                                             <a target="_blank" href="{{ site.url }}">{{ site.url }}</a>
                                                             <a href="#" class="icon-remove" siteid={{ site.id }}><i class="fa fa-close"></i></a>
                                                        </div>
                                                    {% endfor %}
                                                    
                                                {% endif %}
                                            </div>

				
                                            {{ form.render('event_category') }}

                                            <div id="defaultCategories" style="display: none">{{ categories }}</div>

                                            <div id="event-category-selected" class="event-site clearfix" {% if not (event.category|length) %}style="display:none;"{% endif %}>
                                                <input type="hidden" id="category" name="category" value="{% if event.category|length %}{% for key, name in event.category %}{{ key }},{% endfor %}{% endif %}">

                                                <p>Event categories :</p>
                                                {% if event.category | length %}
                                                    {% for key, name in event.category %}
                                                        <div class="ecat_elem">
                                                            <div>
                                                                <label>{{ name }}</label>
                                                                <a href="#" class="icon-remove" catid="{{ key }}"><i class="fa fa-close" ></i></a>
                                                            </div>
                                                        </div>
                                                    {% endfor %}
                                                {% endif %}
                                            </div>

                                            <div class="type-box" style="display:none;">
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
                                                        <input type="text" placeholder="Tags">
                                                        <button class="btn btn-primary" type="button">Ok</button>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="radio-box">
                                                <p> {{ form.label('recurring') }}</p>
                                                {{ form.render('recurring') }}
                                                <div class="date-picker_one clearfix">
                                                	<div id="date-picker-recurring" class="input-div_small">
                                                			<!-- input type="text" id="recurring_end_date" name="recurring_end_date" data-format="dd/MM/yyyy" data-type="event_date" placeholder="Recurring till" autocomplete="off" class="input_add_event_date" -->
                                                               {{ form.render('recurring_end_date') }}
                                                         <span class="add-on"></span>
                                                          </div>
                                                     </div>
                                                 </div>
                                            </div>
                                            {# <div>   
                                                <div class="checkbox-block">
                                                    {{ form.render('campaign_id') }}
                                                    <input id="hiddenCampaignId" name="hiddenCampaignId" value="{{ event.campaign_id }}" type="hidden"/>
                                                </div>
                                            </div> #}
                                            {#% if eventRecurring is defined %}
                                            	<div class="checkbox-block">
                                            		<label>This event is recurring. Change also events:</label>
                                            		{% for index, event in eventRecurring %}
                                            			<input type="checkbox" name=recurring[{{index}}] id="recurring_{{index}}" checked>
                                            				<a href="/{{ toSlugUri(event) }}-{{ index }}">{{event}}</a> 
                                            		{% endfor %}
                                            	</div>
                                            {% endif %#}
                                        </div>
                                        <div class="btn-add_group clearfix">
                                            <button class="ew-button " type="button" id="btn-cancel">Cancel</button>
                                            <button class="btn" type="button" id="btn-preview" href="#previewEvent" role="button" data-toggle="modal">Preview</button>
                                            <button class="ew-button " type="submit" id="btn-submit">Save</button>
                                        </div>
                                    </div>
                                </div>
<div class="add_event_images">
<div class="add-img-box clearfix">
    <div class="add-img">
        <div class="event-one-img">
            <div class="all-img clearfix">
                {% if event.logo %}
                    <img
                            id="img_posters"
                            data-id="{{ event.id }}"
                            class='img-box img-logo'
                            src="/upload/img/event/{{ event.id }}/{{ event.logo }}"
                            alt=""
                            style="width: 200px; height: 200px;"
                            />

                    <span class="delete-logo"></span>
                {% else %}
                    <img id="img_posters" class='img-box img-uploaded-logo' src="/img/demo/q1.jpg" alt="" style="width: 200px; height: 200px;"/>
                    <span class="delete-logo"></span>
                {% endif %}
                <input type="hidden" name="event_logo" value="{{ event.logo }}"/>
            </div>

            {{ form.render('logo') }}
        </div>
        <button style="text-align: center; overflow: hidden; height: 42px;" class="ew-button edit_event_img_button btn-file add-img-btn"
                type="button">{{ form.label('logo') }}</button>
        <!-- input id="add-img-upload" type="file" value="upload" style="display:none;" -->
        {{ form.render('add-img-logo-upload') }}
    </div>

    <div class="add-img">
        <div class="event-one-img">
            <div class="all-img clearfix">
                {% if poster is defined %}
                	{% for pimg in poster %}
	                    <img
	                            id="img_posters"
	                            data-id="{{ pimg.id }}"
	                            class='img-box img-poster'
	                            src="/upload/img/event/{{ event.id }}/poster/{{ pimg.image }}"
	                            alt=""
	                            style="width: 200px; height: 200px;"
	                            />
	
	                    <input type="hidden" name="event_poster" value="{{ pimg.image }}"/>
	
	                    <span class="delete-logo"></span>
	                 {% endfor %}
                {% else %}
                    <img id="img_posters" class='img-box img-uploaded-poster' src="/img/demo/q1.jpg" alt="" style="width: 200px; height: 200px;"/>
                    <span class="delete-logo"></span>
                {% endif %}
            </div>

            {{ form.render('poster') }}
        </div>
        <button style="text-align: center; overflow: hidden; height: 42px;" class="ew-button edit_event_img_button btn-file add-img-btn"
                type="button">{{ form.label('poster') }}</button>
        <!-- input id="add-img-upload" type="file" value="upload" style="display:none;" -->
        {{ form.render('add-img-poster-upload') }}
    </div>

    <div class="add-img">
        <div class="event-one-img">
            <div class="all-img clearfix">
                {% if flyer is defined %}
					{% for fimg in flyer %}
	                    <img
	                            id="img_posters"
	                            data-id="{{ fimg.id }}"
	                            class='img-box img-flyer'
	                            src="/upload/img/event/{{ event.id }}/flyer/{{ fimg.image }}"
	                            alt=""
	                            style="width: 200px; height: 200px;"
	                            />
	
	                    <input type="hidden" name="event_flyer" value="{{ fimg.image }}"/>
	
	                    <span class="delete-logo"></span>
	                 {% endfor %}
                {% else %}
                    <img id="img_posters" class='img-box img-uploaded-flyer' src="/img/demo/q1.jpg" alt="" style="width: 200px; height: 200px;"/>
                    <span class="delete-logo"></span>
                {% endif %}
            </div>

            {{ form.render('flyer') }}
        </div>
        <button style="text-align: center; overflow: hidden; height: 42px;" class="ew-button edit_event_img_button btn-file add-img-btn"
                type="button">{{ form.label('flyer') }}</button>
        <!-- input id="add-img-upload" type="file" value="upload" style="display:none;" -->
        {{ form.render('add-img-flyer-upload') }}
    </div>
</div>
</div>



                            </div>
                            <div class="padd_30"></div>
                        </div>

                    </div>
                </div>
            </div>
        </form>

    </div><!-- add_event_left --> 

        {% if viewMode is defined %}
        	<div id = "viewMode" switch = "on" fbevent = "{{ event.fb_uid }}" style="display:none; visibility:hidden;"></div>
        {% else %}
        	<div id = "viewMode" switch = "off" style="display:none; visibility:hidden;"></div>
        {% endif %}
    </div>


    <!-- Button to trigger modal -->
    {#<a href="#previewEvent" role="button" class="btn" data-toggle="modal">Launch demo modal</a>#}

    <!-- Modal -->

        <div class="modal fade" id="previewEvent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="width:90%; height:100%;">
                <div class="modal-content" style="height:100%;">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 id="myModalLabel" >Event Preview</h3>
                    </div>
                    <div class="modal-body" style="height:80%;">
                        <!-- iframe name="eventPreview_iframe" src="{{ hostName }}" style="width: 100%; height: 100%;"></iframe -->
                        <iframe name="eventPreview_iframe" style="width: 100%; height: 100%;"></iframe>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

    </section>
    </div>


{% endblock %}