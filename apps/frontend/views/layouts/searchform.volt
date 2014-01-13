<form action="/search" method="post" class="form-horizontal" id="topSearchForm">
    <div class="input-append" style="float: none">
        {% if userSearch is defined and userSearch['searchTitle'] is defined %}
            {% set searchTitle = userSearch['searchTitle'] %}
        {% else %}
            {% set searchTitle = '' %}
        {% endif %}
        {{ searchForm.render('searchTitle', {'class':'input-search input-large', 'placeholder':'Title', 'value': searchTitle}) }}
    </div>

    <div class="input-append" style="float: none">
        {% if userSearch is defined and userSearch['searchLocation'] is defined %}
            {% set searchLocation = userSearch['searchLocation'] %}
        {% else %}
            {% set searchLocation = '' %}
        {% endif %}
        <input type="text" id="searchLocationField" class="input-search input-large" placeholder="Location. Current value is your location" value="{{ searchLocation }}"/>
        {% if  userSearch is defined and userSearch['searchLocationLatMin'] is defined %}
            {% set searchLocationLatMin = userSearch['searchLocationLatMin'] %}
        {% else %}
            {% set searchLocationLatMin = '' %}
        {% endif %}
        {{ searchForm.render('searchLocationLatMin', {'value': searchLocationLatMin}) }}

        {% if  userSearch is defined and userSearch['searchLocationLngMin'] is defined %}
            {% set searchLocationLngMin = userSearch['searchLocationLngMin'] %}
        {% else %}
            {% set searchLocationLngMin = '' %}
        {% endif %}
        {{ searchForm.render('searchLocationLngMin', {'value': searchLocationLngMin}) }}

        {% if  userSearch is defined and userSearch['searchLocationLatMax'] is defined %}
            {% set searchLocationLatMax = userSearch['searchLocationLatMax'] %}
        {% else %}
            {% set searchLocationLatMax = '' %}
        {% endif %}
        {{ searchForm.render('searchLocationLatMax', {'value': searchLocationLatMax}) }}

        {% if  userSearch is defined and userSearch['searchLocationLngMax'] is defined %}
            {% set searchLocationLngMax = userSearch['searchLocationLngMax'] %}
        {% else %}
            {% set searchLocationLngMax = '' %}
        {% endif %}
        {{ searchForm.render('searchLocationLngMax', {'value': searchLocationLngMax}) }}
    </div>

    <div class="date-picker clearfix" style="position: relative">
        <div class="input-div ">
            {% if  userSearch is defined and userSearch['searchStartDate'] is defined %}
                {% set searchStartDate = userSearch['searchStartDate'] %}
            {% else %}
                {% set searchStartDate = '' %}
            {% endif %}
            {{ searchForm.render('searchStartDate', {'placeholder':'From date', 'value': searchStartDate, 'class': 'startDatePicker'}) }}
            <i class="icon-calendar"></i>
        </div>
        <div class="input-div ">
            {% if userSearch is defined and userSearch['searchEndDate'] is defined %}
                {% set searchEndDate = userSearch['searchEndDate'] %}
            {% else %}
                {% set searchEndDate = '' %}
            {% endif %}
            {{ searchForm.render('searchEndDate', {'placeholder':'End date', 'value': searchEndDate, 'class': 'endDatePicker'}) }}
            <i class="icon-calendar"></i>
        </div>
    </div>

    <div class="switch-box clearfix ">
        <h5>Change your settings</h5>

        <div class="switch-btn clearfix">
            <button data-type="private" class="on {% if userSearchTab is defined and userSearchTab == 'private' %}active{% endif %}" onclick="return false;">Personalize</button>
            <button data-type="global" class="off {% if userSearchTab is defined and userSearchTab == 'global' %}active{% endif %}" onclick="return false;">Global</button>
        </div>
    </div>

    <div class="service-search clearfix" style="display: block">
        <div class=" category-list clearfix">
            <div class="range clearfix" style="display: none">
                <h5>Choose a date</h5>
                <div class="demo">
                    <p>
                        <select id="my_min" class="range-select">
                            <option value="Today" selected="">Today</option>
                            <option value="Tomorrow">Tomorrow</option>
                            <option value="in 7 days">in 7 days</option>
                            <option value="in 30 days">in 30 days</option>
                            <option value="in 30 days">in 30 days</option>
                            <option value="in 30 days">in 30 days</option>
                            <option value="All time">All time</option>
                        </select>

                        <select id="my_max" class="range-select">
                            <option value="Today">Till 4:00 Am</option>
                            <option value="Tomorrow" selected="">Tomorrow</option>
                            <option value="in 7 days">in 7 days</option>
                            <option value="in 30 days">in 30 days</option>
                            <option value="in 30 days">in 30 days</option>
                            <option value="in 30 days">in 30 days</option>
                            <option value="All time">All time</option>
                        </select>
                    </p>

                    <span class="date-range"></span>

                    <div id="slider-range"
                         class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                         aria-disabled="false">

                        <div class="ui-slider-range ui-widget-header"></div>
                        <a class="ui-slider-handle ui-state-default ui-corner-all first" href="#"
                           style="left: 0%;">
                            <span class="bottom-btn ">Today</span>
                        </a>
                        <a class="ui-slider-handle ui-state-default ui-corner-all last" href="#"
                           style="left: 16.666666666666664%;">
                           <span class="top-btn">Tomorrow</span>
                        </a>

                        <div class="ui-slider-range ui-widget-header ui-corner-all"
                             style="left: 0%; width: 16.666666666666664%;"></div>
                        <ul class="slider-legend">
                            <li>Today</li>
                            <li>Tomorrow</li>
                            <li>in 7 days</li>
                            <li>in 30 days</li>
                            <li>in 30 days</li>
                            <li>in 30 days</li>
                            <li>All time</li>
                        </ul>
                    </div>
                </div>
                <div class="time-range">
                    <i class="icon-time"></i> from 
                        <span>21 Jun </span> till 
                        <span> 24 Jun 2013</span>
                </div>
            </div>

            <h5>Choose events type</h5>
            <div class="row-fluid">
                <div class="table-box">
                    <div class="table-box span12">
                        {% if userSearch is defined and userSearch['searchCategory'] is defined %}
                            {% set searchCategory = userSearch['searchCategory'] %}
                        {% else %}
                            {% set searchCategory = [] %}
                        {% endif %}
                        {% for index, node in formCategories %}
                            {% set dataActive = 0 %}
                            {% for value in searchCategory if value == node['id'] %}
                                {% set dataActive = 1 %}
                            {% endfor %}
                            <a href="#" class="table-box-row {{ node['key'] }}-border clearfix searchChooseCatBtn {% if dataActive == 1 %}active-line{% endif %}" id="cat{{ index }}" data-active="{{ dataActive }}" data-id="{{ node['id'] }}">
                                <div class="cell number-events">
                                    {% if eventsInCategories[node['id']] is defined %}
                                        {{ eventsInCategories[node['id']] }}
                                    {% else %}
                                        0
                                    {% endif %}
                                </div>
                                <div class="cell location-name">{{ node['name'] }}</div>
                                <div class="cell"><span class="icon-device"></span></div>
                            </a>

                            {% if dataActive == 1 %}
                                {{ check_field('searchCategory[]', 'value': node['id'], 'class': 'cat'~index, 'checked': true, 'style': 'display: none;') }}
                            {% else %}
                                {{ check_field('searchCategory[]', 'value': node['id'], 'class': 'cat'~index, 'style': 'display: none;') }}
                            {% endif %}
                        {% endfor %}
                    </div>
                </div>
            </div>
        </div>

        <div class="searchCategoriesTypeBlock hidden">
            {{ radio_field('searchCategoriesType', 'value': 'private') }}
            {{ radio_field('searchCategoriesType', 'value': 'global', 'checked': true) }}
        </div>
        <div class="btn-box clearfix">
            <button type="submit" value="in_list" class="btn" id="searchSubmitOnList">Find on List</button>
            <button type="submit" value="in_map" class="btn" id="searchSubmitOnMap">Find on Map</button>
            <input type="hidden" name="searchType" value="in_list"/>
            {#<button type="submit" name="searchType" value="in_list" class="btn">Find on List</button>
            <button type="submit" name="searchType" value="in_map" class="btn">Find on Map</button>#}
        </div>

    </div>
</form>