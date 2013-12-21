<form action="/search" method="post" class="form-horizontal" id="topSearchForm">
    <div class="input-append" style="float: none">
        {{ searchForm.render('title', {'class':'input-search input-large', 'placeholder':'Title'}) }}
    </div>

    <div class="input-append" style="float: none">
        {{ searchForm.render('locationSearch', {'class':'input-search input-large', 'placeholder':'Location'}) }}
    </div>

    <div class="date-picker clearfix">
        <div class="input-div ">
            {{ searchForm.render('start_dateSearch', {'placeholder':'From Date'}) }}
            <i class="icon-calendar"></i>
        </div>
        <div class="input-div ">
            {{ searchForm.render('end_dateSearch', {'placeholder':'End Date'}) }}
            <i class="icon-calendar"></i>
        </div>
    </div>

    <div class="switch-box clearfix ">
        <h5>Change your settings</h5>

        <div class="switch-btn clearfix">
            <button class="on" onclick="return false;">Personalize</button>
            <button class="off active " onclick="return false;">Global</button>
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
                        {% for index, node in formCategories %}
                            <a href="#" class="table-box-row other-border clearfix searchChooseCatBtn" id="cat{{ index }}" data-active="0">
                                <div class="cell number-events">0</div>
                                <div class="cell location-name">{{ node['name'] }}</div>
                                <div class="cell"><span class="icon-device"></span></div>
                            </a>
                        {% endfor %}
                    </div>
                </div>
            </div>

            <div class="hidden-categories" style="display: none">
                {% for index, node in formCategories %}
                    {{ check_field('category[]', 'value': node['id'], 'class': 'cat'~index) }} - {{ node['name'] }}
                {% endfor %}
            </div>

        </div>
        <button class="btn btn-block">Find</button>
    </div>
</form>