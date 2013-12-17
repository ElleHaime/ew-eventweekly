{% extends "layouts/base.volt" %}

{% block content %}
{% if event['categories']|length %}
    <div class="top-line {{ event['categories'][0]['key'] }}-color">
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span12">
                    <div class="event-title ">
                        <span>{{ event['categories'][0]['name'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endif %}

<div class="container" id="content_noBorder">
    <div class="row-fluid ">
        <div class="span12">
            <div class="event-list_i">
                <div class="events-list-content">
                    <div class="padd_30"></div>
                    <div class="events-list events-list_nbr {% if event['categories']|length%} {{ event['categories'][0]['key'] }}-category" {% endif %}>
                        <div class="row-fluid ">
                            <div class="span12">
                                <div class="event-one clearfix">
                                    <div class="event-one-img">
                                        {% if event['logo'] is defined %}
                                            <a href="/event/show/{{ event['id'] }}">
                                                <img src="/upload/img/event/{{ event['logo'] }}">
                                            </a>
                                        {% endif %}
                                    </div>

                                    <div class="event-one-text">
                                        <a href="/event/show/{{ event['id'] }}" class="name-link">{{ event['name'] }}</a>

                                        <div class="date-list">
                                            <i class="icon-time"></i>
                                            <span class="date-start">20 Aug 2013</span> start at
                                            <span class="date-time">20:43</span> <span class="day-title"></span>
                                        </div>

                                        {{ event['description']|nl2br }}

                                        {% if event['answer'] != 3 %}
                                            <div class="plans-box clearfix">
                                                <span>So, whats your plan?</span>
                                                <div class="btn-hide clearfix">
                                                    <div class="event-site">
                                                            <div id="categ-join" class="event-category categ_green clearfix {% if event['answer'] == 1 %} active-btn {% endif %}">
                                                                <span class="color-type green">Im going!</span>
                                                                <span class="arrow arrow_green"></span>
                                                            </div>
                                                            <div id="categ-maybe" class="event-category categ_yellow clearfix {% if event['answer'] == 2 %} active-btn {% endif %}">
                                                                <span class="color-type yellow">Its interesting, maybe im going</span>
                                                                <span class="arrow arrow_yellow"></span>
                                                            </div>
                                                        {% if !event['answer'] %}
                                                            <button class="btn">I`m going!</button>
                                                            <button class="btn">I`m interested!</button>
                                                            <button class="btn">Don`t like</button>
                                                        {% endif %}
                                                    </div>
                                                </div>
                                            </div>
                                        {% endif %}
                                    </div>
                                    <div class="event-list-btn clearfix">
                                        <div class=" place-address">
                                            <span>Smock Alley Theatre</span>
                                        </div>
                                        {% if event['site'] is defined %}
                                            <div class="event-site clearfix">
                                                {% for key, val in event['site'] %}
                                                    <p>web-site : <a href="val" target="_blank">{{ val }}</a></p>
                                                {% endfor %}
                                            </div>
                                        {% endif %}
                                        <div class="event-list-category">
	                                        {% if event['categories']|length %}
	                    						{% for index, node in event['categories'] %}
	                    						    <span class="category-title">{{ node['name'] }}</span>
	                    						{% endfor %}
	                    					{% else %}
												 <span class="btn uncategorized_label" style="padding: 5px 47px; min-height: 0;">Uncategorized</span>
						                        <span class="btn" id="suggestCategoryBtn" style="padding: 5px 10px; min-height: 0;" title="Suggest Category">?</span>
						                        <ul id="suggestCategoriesBlock" style="padding: 10px; margin: 10px 0 0 15px; list-style: none; background: #67ADDA; width: 140px; display: none;">
						                        {% for index, node in categories %}
						                            <li><a href="/suggest-event-category/{{ event['id'] }}/{{ node['id'] }}" style="color: #ffffff; display: block">{{ node['name'] }}</a></li>
						                        {% endfor %}
						                        </ul>	                    					
	                    					{% endif %}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row-fluid">
                        <div class="span12">
                            <div class="share-box clearfix">
                                <span class="share-box-title">Share this article:</span>

                                <div class="share-box-icon">
                                    <!--Facebook-->
                                    <div class="fb-like" data-colorscheme="light" data-layout="button_count" data-action="like"
                                         data-show-faces="false" data-send="true"></div>

                                    <!--Google +-->
                                    <!-- Place this tag where you want the +1 button to render. -->
                                    <div class="g-plusone" data-size="medium" data-annotation="none"></div>
                                    <!-- Place this tag after the last +1 button tag. -->
                                    <script type="text/javascript">
                                        (function () {
                                            var po = document.createElement('script');
                                            po.type = 'text/javascript';
                                            po.async = true;
                                            po.src = 'https://apis.google.com/js/plusone.js';
                                            var s = document.getElementsByTagName('script')[0];
                                            s.parentNode.insertBefore(po, s);
                                        })();
                                    </script>

                                    <!--Twitter-->
                                    <a href="https://twitter.com/share" class="twitter-share-button" data-via="Apppicker"
                                       data-count="none">Tweet</a>
                                    <script>!function (d, s, id) {
                                        var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                                        if (!d.getElementById(id)) {
                                            js = d.createElement(s);
                                            js.id = id;
                                            js.src = p + '://platform.twitter.com/widgets.js';
                                            fjs.parentNode.insertBefore(js, fjs);
                                        }
                                    }(document, 'script', 'twitter-wjs');</script>

                                    <!--StumbleUpon-->
                                    <!-- Place this tag where you want the su badge to render -->
                                    <su:badge layout="1"></su:badge>
                                    <!-- Place this snippet wherever appropriate -->
                                    <script type="text/javascript">
                                        (function () {
                                            var li = document.createElement('script');
                                            li.type = 'text/javascript';
                                            li.async = true;
                                            li.src = ('https:' == document.location.protocol ? 'https:' : 'http:') + '//platform.stumbleupon.com/1/widgets.js';
                                            var s = document.getElementsByTagName('script')[0];
                                            s.parentNode.insertBefore(li, s);
                                        })();
                                    </script>

                                    <!--Reddit-->
                                    <a href="http://reddit.com/submit"  onclick="window.location = 'http://reddit.com/submit?url=' + encodeURIComponent(window.location); return false">
                                        <img src="http://reddit.com/static/spreddit7.gif" alt="submit to reddit" border="0"/> </a>

                                    <!--Pinterest-->
                                    <a href="//www.pinterest.com/pin/create/button/?url=http%3A%2F%2Fwww.apppicker.com%2Fapplists%2F2732%2FThe-best-sports-trivia-apps-for-iPhone-and-iPad&media=http%3A%2F%2Fwww.apppicker.com%2Fupload%2F2013%2F01%2F22%2Fgzpztqzohy.png&description=The+best+sports+trivia+apps+for+iPhone+and+iPad"
                                       data-pin-do="buttonPin" data-pin-config="none"><img
                                            src="//assets.pinterest.com/images/pidgets/pin_it_button.png"/></a>
                                    <script type="text/javascript">
                                        (function (d) {
                                            var f = d.getElementsByTagName('SCRIPT')[0], p = d.createElement('SCRIPT');
                                            p.type = 'text/javascript';
                                            p.async = true;
                                            p.src = '//assets.pinterest.com/js/pinit.js';
                                            f.parentNode.insertBefore(p, f);
                                        }(document));
                                    </script>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="comment-box">
                                <h2>Leave comments</h2>
                                <fb:comments href="http://events.apppicker.com/event/show/{{ event['id'] }}"></fb:comments>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{% endblock %}
