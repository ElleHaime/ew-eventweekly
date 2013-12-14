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
                            	{% if campaign.id %}
                            		Edit campaign
                            	{% else %}
                            		Create campaign
                            	{% endif %}
                                {{ form.render('id') }}
                            </h3>
                            <div class="row-fluid">
                                <div class="span3">
                                    <div class="add-img">
                                    	{% if campaign.logo %}
                                        	<a href=""><img id='img-box' src="/upload/img/campaign/{{ campaign.logo }}" alt=""></a>
                                        {% else %}
											<a href=""><img id='img-box' src="/img/demo/q1.jpg" alt=""></a>
                                        {% endif %}
                                        <button class="btn" id ="add-img-btn" type="button">{{ form.label('logo')}}</button>
                                        {{ form.render('add-img-upload') }}
                                    </div>
                                </div>
                                {{ form.render('logo')}}

                                <div class="span9">
                                
                                    <div class="input-div clearfix">
                                        {{ form.render('name')}}
                                    </div>

                                    <div class="clear"></div>
                                    {{ form.render('description') }}
                                    <div class="btn-add_group clearfix">
                                        <button class="btn btn-cancel" id="btn-cancel">Cancel</button>
                                        <button class="btn" type="submit" id="btn-submit">Save</button>
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
                            </div>
                            <div class="input-append">
                                {{ form.render('address') }}
                                {{ form.render('address-coords')}}
                                <div class="search-queries hidden">
                                    <ul id="addresses-list">
                                    </ul>
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