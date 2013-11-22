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
                                            <span id="days-count" class="day-title">Event happens - today</span>
                                        </div>
                                    </div>
                                    {#
                                    <select>
                                        <option>only for my Facebook friends</option>
                                        <option>only for my Facebook friends</option>
                                        <option>only for my Facebook friends</option>
                                        <option>only for my Facebook friends</option>
                                        <option>only for my Facebook friends</option>
                                        <option>only for my Facebook friends</option>
                                        <option>only for my Facebook friends</option>
                                    </select>
                                    #}
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
                                <input type="text" placeholder="Choose location" id="location-input" value="{{ location.city }}, {{ location.country }}">
                                {#<button class="btn btn-primary" type="button">Ok</button>#}
                                <div class="search-queries hidden">
                                    <ul id="locations-list">
                                        <li>Text1</li>
                                        <li>Text2</li>
                                        <li>Text3</li>
                                        <li>Text4</li>
                                        <li>Text5</li>
                                    </ul>
                                </div>
                                {#<i class="icon-map-marker"></i></button>#}
                            </div>
                            <div class="input-append">
                                <input type="text" placeholder="Choose address" id="address-input">
                                {#
                                <button class="btn btn-primary" type="button">
                                <button class="btn btn-primary" type="button">Ok</button>
                                #}
                                <div class="search-queries hidden">
                                    <ul id="addresses-list">
                                        <li>Text1</li>
                                        <li>Text2</li>
                                        <li>Text3</li>
                                        <li>Text4</li>
                                        <li>Text5</li>
                                    </ul>
                                </div>
                                {#<i class="icon-map-marker"></i></button>#}
                            </div>

                            <div class="input-append">
                                <input type="text" placeholder="Choose venue" id="venue-input">
                                {#
                                <button class="btn btn-primary" type="button">
                                <button class="btn btn-primary" type="button">Ok</button>
                                #}
                                <div class="search-queries hidden">
                                    <ul id="venues-list">
                                        <li>Text1</li>
                                        <li>Text2</li>
                                        <li>Text3</li>
                                        <li>Text4</li>
                                        <li>Text5</li>
                                    </ul>
                                </div>
                                {#<i class="icon-map-marker"></i></button>#}
                            </div>

                            {#
                            <div class=" place-address">
                                Smock Alley Theatre
                                <button class="btn btn-primary">
                                    <i class="icon-map-marker"></i>
                                </button>
                            </div>
                            #}

                            <div class="input-append change-input">
                                {#
                                <button class="btn btn-primary" type="button">
                                    <i class="icon-map-marker"></i></button>
                                <input  type="text" placeholder="change location">
                                #}
                            </div>

                            <div class="input-append">
                                <input  type="text" placeholder="Event web site"><button class="btn btn-primary" id="add-web-site" type="button">+</button>
                            </div>
                            <div id="event-sites" class="event-site clearfix">
                                <p>Event web-sites :</p>
                            </div>

                            <select id="categories">
                                <option>Suggest category</option>
                                <option>Music</option>
                                <option>Sport</option>
                                <option>Outdoors</option>
                                <option>Arts</option>
                                <option>Business</option>
                                <option>Shopping</option>
                                <option>Night life</option>
                                <option>Other</option>
                                {#<option>Custom</option>#}
                            </select>
                            <div id="event-categories" class="event-site clearfix">
                                <p>Event categories :</p>
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
                                <p>Reacuring event</p>
                                <label class="radio">
                                    <input type="radio" name="reacuring"  value="1" checked="checked">Once</label>
                                <label class="radio">
                                    <input type="radio" name="reacuring"  value="2">Daily</label>
                                <label class="radio">
                                    <input type="radio" name="reacuring" value="3">Weekly</label>
                                <label class="radio">
                                    <input type="radio" name="reacuring" value="4">Monthly</label>
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