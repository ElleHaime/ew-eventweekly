{% extends "layouts/base.volt" %}

{% block content %}
    <div class="padd_70"></div>
    <div class="container content_noBorder">

        <div class="row-fluid ">
            <div class="span12">
                <div class="row-fluid ">
                    <div class="span9">
	                     <div class="add-event">
							<h3 class="title-page">Create event</h3>
							<form method="post">	                         
							<div class="row-fluid">
	                             <div class="span3">
	                                 <div class="add-img">
	                                     <a  href=""><img src="img/demo/q1.jpg" alt=""></a>
	                                     <button class="btn" type="button">add image</button>
	                                 </div>
	                             </div>
	                             <div class="span9">
	                                 <div class="input-div clearfix">
	                                     <input  type="text" value=""  placeholder="main title">
	                                     {#<span>description / example</span>#}
	                                 </div>
                                     <div class="input-div_date clearfix">
                                         <div id="date-picker" class="input-div_small">
                                             <input data-format="MM/dd/yyyy" type="text"/>
                        <div class="add-event">
                            <h3 class="title-page">Create event</h3>
                            <div class="row-fluid">
                                <div class="span3">
                                    <div class="add-img">
                                        <a  href=""><img src="img/demo/q1.jpg" alt=""></a>
                                        <button class="btn" type="button">add image</button>
                                    </div>
                                </div>
                                <div class="span9">
                                    <div class="input-div clearfix">
                                        <input  type="text" value=""  placeholder="main title">
                                        {#<span>description / example</span>#}
                                    </div>
                                    <div class="input-div_date clearfix">
                                        <div id="date-picker" class="input-div_small">
                                            <input id="date-input" data-format="dd/MM/yyyy" type="text">
                                                <span class="add-on">
                                                    <i data-time-icon="icon-date" data-date-icon="icon-calendar"></i>
                                                </span>
                                        </div>
                                        <div id="time-picker" class="input-div_small">
                                            <input data-format="hh:mm:ss" type="text" value="00:00:00"/>
                                                <span class="add-on">
                                                    <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                                                </span>
                                        </div>
                                        {#
                                        <div class="input-div_small">
                                            <input type="text" placeholder=""><i class="icon-calendar"></i>
                                        </div>
                                        <div class="input-div_small">
                                            <input type="text" placeholder=""><i class="icon-time"></i>
                                        </div>
                                        #}
                                        <div class="date-box" id="time-string">
                                            <span id="date-start" class="date-start">12 Aug 2013</span>, starts at <span id="time-start" class="date-time">00:00:00</span>
                                            <span class="day-title">—tomorrow</span>
                                        </div>
                                    </div>
                                    <select>
                                        <option>only for my Facebook friends</option>
                                        <option>only for my Facebook friends</option>
                                        <option>only for my Facebook friends</option>
                                        <option>only for my Facebook friends</option>
                                        <option>only for my Facebook friends</option>
                                        <option>only for my Facebook friends</option>
                                        <option>only for my Facebook friends</option>
                                    </select>
                                    <div class="clear"></div>
                                    <textarea class="field-big"  placeholder="add description"> </textarea>
                                    <div class="btn-add_group clearfix">
                                        <button class="btn btn-cancel">Cancel</button>
                                        <button class="btn" type="button">Preview</button>
                                        <button class="btn" type="button">Save and publish</button>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="span3">
                        <div class="sidebar">
                            <div class="input-append">
                                <input  type="text" placeholder="choose location"><button class="btn btn-primary" type="button">
                                <i class="icon-map-marker"></i></button>
                            </div>
                            <div class=" place-address">
                                Smock Alley Theatre
                                <button class="btn btn-primary">
                                    <i class="icon-map-marker"></i>
                                </button>
                            </div>
                            <div class="input-append change-input">
                                <button class="btn btn-primary" type="button">
                                    <i class="icon-map-marker"></i></button>
                                <input  type="text" placeholder="change location">
                            </div>
                            <div class="input-append">
                                <input  type="text" placeholder="Event web site"><button class="btn btn-primary" type="button">Ok</button>
                            </div>
                            <div class="event-site clearfix">
                                <p>Event web-site :</p>
                                <a href="#">http://www.dpdp.com</a>
                                <button class="btn btn-sm">Edit</button>
                            </div>
                            <select>
                                <option>Suggest category</option>
                                <option>Suggest category</option>
                                <option>Suggest category</option>
                                <option>Suggest category</option>
                                <option>Suggest category</option>
                            </select>
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
                                <p>Reacuring event</p>
                                <label class="radio">
                                    <input type="radio" name="optionsRadios"  value="option1" checked>
                                    daily
                                </label>
                                <label class="radio">
                                    <input type="radio" name="optionsRadios" value="option1" checked>
                                    weekly
                                </label>
                                <label class="radio">
                                    <input type="radio" name="optionsRadios" value="option1" checked>
                                    monthly
                                </label>
                                <label class="radio" style="display: none">
                                    <input type="radio" name="optionsRadios" value="option1" checked>
                                    other
                                </label>
                                <hr>
                                <div class="checkbox-block">
                                    <label class="checkbox">

                                        <input type="checkbox" value="">
                                        Create Promoter
                                    </label>

                                    <p> Choose from existing</p>
                                    <select  disabled="disabled" >
                                        <option>Select existing</option>
                                        <option>Select existing</option>
                                        <option>Select existing</option>
                                        <option>Select existing</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

{% endblock %}