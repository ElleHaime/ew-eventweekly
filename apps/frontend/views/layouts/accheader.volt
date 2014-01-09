<div class="out">
    <div class="container-fluid header">
        <div class="row-fluid">
            <div class="span5">
                <div class=" header-logo">
                    <a href="/" class="logo"></a>
                </div>

                {% if member.id is defined %}
                
                    <div class=" user-block clearfix">
                        <div class="user-box">
                            <a id="user-down-caret">
                                <img alt=""
                                    {% if member.logo != '' %}
                                        src="{{ member.logo }}"
                                    {% else %}
                                        src='/img/demo/h_back_1.jpg'
                                    {% endif %}
                                >
                                <span>
                                    {% if member.name|length %}
                                        {{ member.name }}
                                    {% else %}
                                        {{ member.email }}
                                    {% endif %}
                                </span><i class="caret"></i>
                            </a>
                            <div class="user-down" id="user-down" style="display:none;">
                                <div class="edit-btn clearfix">
                                    <div class="btn-line clearfix">
                                        <button class="btn" onclick="location.href='/profile'">
                                            <span class="edit-icon"></span>
                                            <span class="btn-text">profile settings</span>
                                        </button>
                                        <a href="#" class="btn-logout" onclick="location.href='/logout'"></a>
                                    </div>
                                    <button class="btn btn-block" onclick="location.href='/campaign/list'">
                                        <span class="edit-icon"></span><span class="btn-text">manage campaigns</span>
                                    </button>
                                </div>
                                <div class="btn-list">
                                    <div class="btn-line clearfix">
                                        <button class="btn btn-block" onclick="location.href='/event/list'">
                                            <span class="btn-count" id="userEventsCreated">{{ userEventsCreated }}</span>
                                            <span class="btn-text">Created</span>
                                        </button>
                                    </div>
                                    <button class="btn btn-block" onclick="location.href='/event/joined'">
                                        <span class="btn-count" id="userEventsGoing">{{ userEventsGoing }}</span>
                                        <span class="btn-text">Where I Go</span>
                                    </button>
                                    <button class="btn btn-block" onclick="location.href='/event/liked'">
                                        <span class="btn-count" id="userEventsLiked">{{ userEventsLiked }}</span>
                                        <span class="btn-text">Liked by you</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-add" onclick="location.href='/event/edit'">
                            <span class="icon-plus"></span>
                            <span class="text-btn">Add event</span>
                        </button>
                    </div>

                {% else %}

                    {% if hideYouAreNotLoggedInBtn is empty %}
                        <div class=" user-block  no_user clearfix">
                            <div class="user-box">
                                <a href="/#fb-login">
                                    <i class="log-icon"></i>
                                    <span class="no_log">you are not logged in</span>
                                </a>
                            </div>
                        </div>
                    {% endif %}

                {% endif %}

            </div>    

            <div class="span7 location-box">
                <div class="show-list">
                    {% if link_back_to_list is defined %}
                        <button class="btn btn-show tooltip-text"
                                onclick="location.href='/list'" title="" rel="tooltip" data-placement="bottom"
                                data-original-title="Back link"><i class="icon-back"></i>
                        </button>
                    {% elseif link_to_list is defined %}
                        <button class="btn btn-show  tooltip-text" data-placement="bottom" rel="tooltip" title=""
                                onclick="location.href='/list'" data-original-title="Show all list"><i class="icon-sel"></i>
                        </button>
                    {% else %}
                        <button class="btn btn-show  tooltip-text" data-placement="bottom" rel="tooltip" title=""
                                onclick="location.href='/map'" data-original-title="Show all map"><i class="icon-map"></i>
                        </button>
                    {% endif %}
                </div>

                <div class="location clearfix">
                    {% if eventsTotal is defined %}
                        <span class="location-count tooltip-text" 
                              data-placement="bottom" 
                              rel="tooltip" 
                              title=""        
                              id="events_count"
                              data-original-title="All events {{ eventsTotal }}">{{ eventsTotal }}
                        </span>
                    {% else %}
                        <span class="location-count tooltip-text" 
                              data-placement="bottom" 
                              rel="tooltip" 
                              title="" 
                              id="events_count"
                              data-original-title="0">0</span>
                    {% endif %}

                    <div class="location-place location-place_country">
                        <a href="#" class="location-city locationCity">
                            <i class="caret"></i>
                            <span>{{ location.alias }}</span>
                        </a>

                        <div class="location-search searchCityBlock clearfix" style="display: none;">
                            <div class="input-append" style="float: none">
                                <input class="input-large" size="16" type="text" placeholder="Search city" id="topSearchCity">
                                <button class="btn" type="button">Find</button>
                            </div>
                        </div>
                    </div>

                    <div class="location-place location-place_ask">
                        <a href="#" class="advancedSearchBtn">
                            <i class="caret"></i>
                            <i class="search-img"></i>
                            <span>What are you looking for?</span>
                        </a>

                        <div class="location-search clearfix advancedSearchBlock">
                            {% include 'layouts/searchform.volt' %}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="notiBlock">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <span id="notiText">Warning message</span>
                <span class="notiBtnArea"></span>
                <a href="#" class="  icon-remove notiHide"></a>
            </div>
        </div>
    </div>
</div>
<div class="padd_70"></div>
