{% extends "layouts/base.volt" %}

{% block content %}
    <!-- content -->
    <div class="container " id="content_noBorder">
    
    <form method="post" enctype="multipart/form-data">
    
    <div class="row">
    <div class="span12">
    <div class="add-event_i clearfix">

    <div class="row-fluid">
        <div class="span12">
            <div class="padd_30"></div>
            <h3 class="title-add">
                {% if event.id %}
                    Edit event
                {% else %}
                    Create event
                {% endif %}

                {{ form.render('id') }}
            </h3>
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

                            <div class="arrow_box"> arrow</div>
                            <span>description / example</span>
                        </div>

                        <div class="input-div_date clearfix">
                            <div class="date-picker_one clearfix">
                                <div id="date-picker-start" class="input-div_small">
                                    {{ form.render('start_date') }}<i class="icon-calendar"></i>
                                <span class="add-on">
                                                    <i data-time-icon="icon-date" data-date-icon="icon-calendar"></i>
                                                </span>
                                </div>
                                <div id="time-picker-start" class="input-div_small">
                                    {{ form.render('start_time') }}<i class="icon-time"></i>
                                <span class="add-on">
                                                    <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                                                </span>
                                </div>

                            </div>
                            <div class="date-picker_one clearfix">
                                <div id="date-picker-end" class="input-div_small">
                                    {{ form.render('end_date') }}<i class="icon-calendar"></i>
                                <span class="add-on">
                                                    <i data-time-icon="icon-date" data-date-icon="icon-calendar"></i>
                                                </span>
                                </div>
                                <div id="time-picker-end" class="input-div_small">
                                    {{ form.render('end_time') }}<i class="icon-time"></i>
                                <span class="add-on">
                                                    <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                                                </span>
                                </div>
                            </div>

                            <div class="date-box clearfix">
                                <label>
                                    {{ form.render('event_status') }} Publish event immediately
                                </label>
                            </div>

                            <div class="date-box" id="time-string" style="display:none;">
                                <span id="date-start" class="date-start">12 Aug 2013</span>, starts at <span id="time-start"
                                                                                                             class="date-time">00:00:00</span><br>
                                <span id="days-count" class="day-title">Event happens today</span>
                            </div>
                        </div>

                        {{ form.render('description') }}



                    </div>
                </div>
            </div>

                <div class="sidebar-box">
                    <div class="input-append">
                        {{ form.render('location') }}
                        <button class="btn" type="button"><i class="icon-place-marker"></i></button>
                        {{ form.render('location_latitude') }}
                        {{ form.render('location_longitude') }}
                        <div class="search-queries hidden">
                            <ul id="locations-list">
                            </ul>
                        </div>
                    </div>

                    <div class="input-append">
                        {{ form.render('address') }}
                        <button class="btn" type="button"><i class="icon-place-marker"></i></button>
                        {{ form.render('address-coords') }}
                        <div class="search-queries hidden">
                            <ul id="addresses-list">
                            </ul>
                        </div>
                    </div>

                    <div class="input-append">
                        {{ form.render('venue') }}
                        <button class="btn" type="button"><i class="icon-place-marker"></i></button>
                        {{ form.render('venue_latitude') }}
                        {{ form.render('venue_longitude') }}
                        <div class="search-queries hidden">
                            <ul id="venues-list">
                            </ul>
                        </div>
                    </div>

                    <div class="input-append">
                        <input type="text" id="sites" placeholder="Event web site"/>
                        <button class="btn btn-primary" id="add-web-site" type="button">Ok</button>
                        <div class="warning-box"
                             style="background: green; width: 200px; height: 200px; display: none; position: absolute; z-index: 101; top:43px; right:0">
                        </div>

                    </div>
                    <div id="event-site-selected" class="event-site clearfix" {% if not (event.site|length) %} style="display:none;" {% endif %}>
                        <input type="hidden" id="event_site" name="event_site" value="{% if event.site|length %}{% for es in event.site %}{{ es.url }},{% endfor %}{% endif %}">
                        <p>Event web-sites :</p>
                        {% if event.site|length %}

                            {% for site in event.site %}
                                <div class = "esite_elem">
                                    <a target="_blank" href="{{ site.url }}">{{ site.url }}</a>
                                    <a href="#" class="icon-remove"></a>
                                </div>
                            {% endfor %}
                        {% endif %}
                    </div>

                    {{ form.render('event_category') }}

                    <div id="defaultCategories" style="display: none">{{ categories }}</div>

                    <div id="event-category-selected" class="event-site clearfix" {% if not (event.category|length) %}style="display:none;"{% endif %}>
                        <input type="hidden" id="category" name="category" value="{% if event.category|length %}{% for key, name in event.category %}{{ key }},{% endfor %}{% endif %}">

                        <p>Event categories :</p>
                        {% if event.category|length %}
                            {% for key, name in event.category %}
                                <div class="ecat_elem">
                                    <div>
                                        <label>{{ name }}</label>
                                        <a href="#" class="icon-remove" catid="{{ key }}"></a>
                                    </div>
                                </div>
                            {% endfor %}
                        {% endif %}
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
                                <input type="text" placeholder="Tags">
                                <button class="btn btn-primary" type="button">Ok</button>
                            </div>

                        </div>
                    </div>

                    <div class="radio-box">
                        <p> {{ form.label('recurring') }}</p>
                        {{ form.render('recurring') }}
                        <div class="checkbox-block">
                            <p> Choose promoter</p>
                            {{ form.render('campaign_id') }}
                        </div>
                    </div>
                </div>
            <div class="btn-add_group clearfix">
                <button class="btn btn-cancel" type="button" id="btn-cancel">Cancel</button>
                <button class="btn" type="button" id="btn-preview" disabled="disabled">Preview</button>
                <button class="btn" type="submit" id="btn-submit">Save</button>
            </div>
        </div>
    </div>

        <div class="add-img">
            <div class="event-one-img">
                <div class="all-img clearfix">
                    {% if event.logo %}
                        <a href=""><img id='img-box' src="/upload/img/event/{{ event.logo }}" alt=""></a>
                    {% else %}
                        <a href=""><img id='img-box' src="/img/demo/q1.jpg" alt=""></a>
                    {% endif %}

                </div>


                {{ form.render('logo') }}

                <!-- modal window -->
                <div class="modal" id="myModal" tabindex="-1" role="dialog"
                     aria-labelledby="myModalLabel" aria-hidden="true">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                    </button>
                    <div class="modal-body">
                        <img src="img/bgs/img.jpg" alt="">
                    </div>
                </div>
                <!-- end modal -->


            </div>
            <button style="text-align: center; overflow: hidden; height: 42px;" class="btn btn-block btn-file"
                    id="add-img-btn" type="button">{{ form.label('logo') }}</button>
            <!-- input id="add-img-upload" type="file" value="upload" style="display:none;" -->
            {{ form.render('add-img-upload') }}
        </div>

    </div>
    <div class="padd_30"></div>
</div>

    </div>
    </div>
    </form>
    </div>
{% endblock %}