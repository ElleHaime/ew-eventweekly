{% extends "layouts/base.volt" %}

{% block content %}
    <div class="container " id="content_noBorder">

	<form method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="span12">
            <div class="add-event_i clearfix">

                <div class="row-fluid">
                    <div class="span12">
                        <div class="padd_30"></div>
                        <div class="add-event clearfix">
                            {{ form.render('id') }}

                    <div class="add-img">
						<div class="event-one-img">
                            {% if campaign.logo %}
                                <a><img id='img-box' src="/upload/img/campaign/{{ campaign.logo }}" alt=""></a>
                            {% else %}
                                <a><img id='img-box' src="/img/demo/q1.jpg" alt=""></a>
                            {% endif %}

                            {{ form.render('logo')}}

                            <div style="text-align: center; overflow: hidden; height: 42px;" class="btn btn-block btn-file "id ="add-img-btn">
                                <div>{{ form.label('logo')}}</div>

                            </div>
                        </div>
                    </div>
					{{ form.render('add-img-upload') }}

                    <div class="form-center clearfix">
                        <div class="input-div clearfix">
							{{ form.render('name')}}
                            <div class="arrow_box"> arrow</div>
                        </div>

                        <div class="input-div clearfix">
                            {{ form.render('location') }}
                            <div class="arrow_box"> arrow</div>
                            {{ form.render('location_latitude') }}
                            {{ form.render('location_longitude') }}
                            <div class="search-queries hidden">
                                <ul id="locations-list">
                                </ul>
                            </div>
                        </div>

                        <div class="input-div">
                            {{ form.render('address') }}
                            <div class="arrow_box"> arrow</div>
                            {{ form.render('address-coords') }}
                            <div class="search-queries hidden">
                                <ul id="addresses-list">
                                </ul>
                            </div>
                        </div>

                        {{ form.render('description') }}
                        
                        <div class="btn-add_group clearfix">
                            <button class="btn btn-cancel" type="button" id="btn-cancel">Cancel</button>
                            <button class="btn" id="btn-submit" type="submit">Save</button>
                        </div>
                    </div>
                </div>
                
                <div class="sidebar">
 					<div class="event-site clearfix">
 						{% if campaign.event|length %}
 							<p>Events in campaign</p>
                            {% for event in campaign.event %}
                                <div class="events-list">
                                    <div class="row-fluid ">
                                        <div class="span12">
                                            <div class="event-one clearfix">
                                                <div class="event-one-text">
                                                    <a href="/event/edit/{{ event.id }}" class="name-link">{{ event.name }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {% endfor %}
                        {% else %}
                            Campaign has no events.
                        {% endif %}
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