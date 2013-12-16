<div class="out">
    <div class="container header">
        <div class="row">
            <div class="span2">
                <span class="line"></span>
                <a href="/" class="logo"></a>
            </div>
            <div class="span4 user-block">
                <span  class="line"></span>

                {% if member.id is defined %}
                <div class="user-box" id="user-box">
                    <a id="user-down-caret">{{ image('img/demo/user.jpg') }}
                        <span>{{ member.name }}</span><i class="caret"></i>
                    </a>

                    <div class="user-down" id="user-down" style="display:none;">
                        <div class="edit-btn">
                            <button class="btn btn-block" onclick="location.href='/profile'"><span class="edit-icon"></span><span class="btn-text">edit profile</span></button>
                            <button class="btn btn-block" onclick="location.href='/campaign/list'"><span class="edit-icon"></span><span class="btn-text">manage campaigns</span></button>
                        </div>

                        <div class="btn-list">
                            <button class="btn btn-block" onclick="location.href='/event/liked'"><span class="btn-count">5</span><span class="btn-text">Liked</span></button>
                            <button class="btn btn-block" onclick="location.href='/event/list'"><span class="btn-count">9</span><span class="btn-text">Created</span></button>
                            <button class="btn btn-block" onclick="location.href='/event/joined'"><span class="btn-count">3</span><span class="btn-text">Where I Go</span></button>
                        </div>

                        <div class="edit-btn">
                            <button class="btn btn-block" onclick="location.href='/logout'"><span class="edit-icon"></span><span class="btn-text">logout</span></button>
                        </div>

                        <div id="back-to" class=" clearfix">
                            <a href="#">
                                <i class="icon-chevron-up icon-white"></i>

                            </a>
                        </div>
                    </div>
                </div>
                <button class="btn btn-add" onclick="location.href='/event/edit'"> <span class="icon-plus-sign"></span><span class="text-btn">Add Event</span></button>
                {% else %}
                <div class="user-box">
                    {#<a href="/signup" style=" margin-top:2px;"><span>Sign Up</span></a>#}
                    <a href="/" style=" margin-top:2px;"><span>Sign In</span></a>
                </div>
                {% endif %}

                <span  class="line"></span>

            </div>
            <div class="span4 location-box">

                <div class="location clearfix">
                    {% if eventsTotal is defined %}
                        <span class="location-count" id="events_count">{{ eventsTotal }}</span>
                    {% else %}
                        <span class="location-count location-count_no" id="events_count">0</span>
                    {% endif %}
                    <div class="location-place location-place_country">
                        <a href="#" class="location-city locationCity">
                            <i class="caret"></i>
                            <span>{{ location.alias }}</span>
                        </a>

                        <div class="location-search searchCityBlock clearfix" style="width: 295px">
                            <div class="input-append" style="float: none">
                                <input class=" input-large"  size="16" type="text" placeholder="Search city" id="topSearchCity" style="width: 69%">
                                <button class="btn" type="button">Find</button>
                            </div>
                        </div>
                    </div>
                    <div class="location-place location-place_ask">
                        <a href="#" class="advancedSearchBtn">
                            <i class="caret"></i>
                            <span>What are you looking for?</span>
                        </a>

                        <div class="location-search clearfix advancedSearchBlock">
                            <div class="input-append">
                                <input class=" input-large"  size="16" type="text" placeholder=" Event search engine">
                                <button class="btn" type="button">Find</button>
                            </div>

                            <div class="service-search clearfix">
                                <p><span class="count-events">54</span>Events for you, <a href="#">username</a> <span class="icon-refresh icon-white"></span></p>
                                <div class="noUiSlider">
                                    <i class="icon-time icon-white"></i>
                                    <i class="icon-calendar icon-white"></i>
                                </div>
                                <ul class=" days-events clearfix">
                                    <li class="activity">Now</li>
                                    <li>Today</li>
                                    <li>Tomorrow</li>
                                    <li>in 3 days</li>
                                    <li>in 7 days</li>
                                    <li>in 30 days</li>
                                    <li>2091 AD</li>
                                </ul>
                                <ul class=" category-list clearfix">
                                    <li><span class="count-category blue-color">24</span>Entertaiment</li>
                                    <li><span class="count-category purple-color">24</span>Music</li>
                                    <li><span class="count-category orange-color">4</span>Sport</li>
                                    <li><span class="count-category red-color">24</span>Shopping</li>
                                    <li class="none_active"><span class="count-category gray-color">24</span>NightLife</li>
                                </ul>

                                <a class="edit-link"  href="#"><i class=" icon-cog icon-white"></i>
                                    <span> customise your search event-profile </span>
                                </a>
                                <button class="btn text-right">Done</button>
                            </div>
                        </div>

                    </div>

                </div>
                <span  class="line"></span>
            </div>

            <div class="span2 show-list">
                <div class="show-box">
                    {% if link_to_list is defined %}
                        <button class="btn btn-show" onclick="location.href='/list'"><i class=" icon-list"></i><span>Show as list </span></button>
                    {% else %}
                        <button class="btn btn-show" onclick="location.href='/map'"><i class=" icon-map"></i><span>Show as map </span></button>
                    {% endif %}
                </div>
            </div>
        </div>

        <div class="container">
            <a href="#" class="btn-row-down">
                <i class=" icon-white icon-chevron-down "></i>
            </a>

            <div id="back-to-top" class="text-center clearfix">

                <div class=" location-box">
                    <div class="location clearfix">
                        {% if eventsTotal is defined %}
                            <span class="location-count" id="events_count">{{ eventsTotal }}</span>
                        {% else %}
                            <span class="location-count location-count_no" id="events_count">0</span>
                        {% endif %}
                        <div class="location-place location-place_country">
                            <a href="#" class="location-city">
                                <i class="caret"></i>
                                <span>{{ location.alias }}</span>
                            </a>

                            <div class="location-search searchCityBlock clearfix" style="width: 295px">
                                <div class="input-append" style="float: none">
                                    <input class=" input-large" size="16" type="text" placeholder="Event search engine"
                                           id="topSearchCity" style="width: 69%">
                                    <button class="btn" type="button">Find</button>
                                </div>
                            </div>
                        </div>
                        <div class="location-place location-place_ask">
                            <a href="#">
                                <i class="caret"></i>
                                <span>What are you looking for?</span>
                            </a>

                            <div class="location-search clearfix">
                                <div class="input-append">
                                    <input class=" input-large" size="16" type="text" placeholder="Search city">
                                    <button class="btn" type="button">Find</button>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                <span class="line"></span>
            </div>

        </div>
    </div>
</div>
<div class="notiBlock">
    <div class="container">
        <div class="row">
            <div class="span12">
                <span id="notiText">qwe</span>
                <span class="notiBtnArea"></span>
                <a href="#" class="notiHide">&times;</a>
            </div>
        </div>
    </div>
</div>