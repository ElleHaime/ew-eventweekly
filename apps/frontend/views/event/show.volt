{% extends "layouts/base.volt" %}

{% block content %}
{% if event.category|length %}
	{% for cat in event.category %}
        <div class="top-line {{ cat.key }}-color">
	        <div class="container-fluid">
	            <div class="row-fluid">
	                <div class="span12">

                            <div class="event-title ">
                                <span>{{ cat.name }}</span>
                            </div>
	                     </div>
	                </div>
	             </div>
	        </div>
        
	   {% break %}
	{% endfor %}
{% endif %}

<div class="container" id="content_noBorder">
    <div class="row-fluid ">
        <div class="span12">

            <div class="event-one-box">
                        <div class="row-fluid ">
                            <div class="span12">

                                <div class="padd_30"> </div>
                                <div class="event-discription">
                                    {% if event.image.cover is defined %}
                                        <div class="event-photo">
                                            <img src="/upload/img/event/{{ event.id }}/cover/{{ event.image.cover }}" alt="">
                                        </div>
                                    {% else %}
                                        <div class="add-img">
                                            <div id="current_event_id" class="event-one-img" event="{{ event.id }}">
                                                {#<a href="/event/100038">#}
                                                {% if eventPreview is defined %}
                                                    {% if eventPreviewLogo is defined %}
                                                        <img src="/upload/img/event/{{ event.id }}/{{ event.logo }}">
                                                    {% else %}
                                                        <img src="/upload/img/event/tmp/{{ event.logo }}">
                                                    {% endif %}
                                                {% else %}
                                                    <img src="/upload/img/event/{{ event.id }}/{{ event.logo }}">
                                                {% endif %}

                                                {#</a>#}
                                            </div>

                                           {% if poster is defined or flyer is defined %}
                                                <div class="all-img clearfix">
                                                    {% set count = 0 %}
                                                    {% if poster is defined %}
                                                        {% if eventPreview is defined %}
                                                            {% if eventPreviewPoster is defined %}
                                                                <a href="#poster-img" data-toggle="modal" class="clearfix" style="float:left;cursor: pointer"><img style="width: 93px; height: 84px" src="/upload/img/event/{{ event.id }}/poster/{{ eventPreviewPoster }}" alt="" /></a>
                                                            {% else %}
                                                                <a href="#poster-img" data-toggle="modal" class="clearfix" style="float:left;cursor: pointer"><img style="width: 93px; height: 84px" src="/upload/img/event/tmp/{{ poster }}" alt="" /></a>
                                                            {% endif %}
                                                        {% else %}
                                                            <a href="#poster-img" data-toggle="modal" class="clearfix" style="float:left;cursor: pointer"><img style="width: 93px; height: 84px" src="/upload/img/event/{{ event.id }}/poster/{{ poster.image }}" alt="" /></a>
                                                        {% endif %}


                                                        <!-- Modal -->
                                                        <div class="modal fade" id="poster-img" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                                    <div class="modal-body">
                                                                        {% if eventPreview is defined %}
                                                                            {% if eventPreviewPoster is defined %}
                                                                                <img src="/upload/img/event/{{ event.id }}/poster/{{ eventPreviewPoster }}" alt="">
                                                                            {% else %}
                                                                                <img src="/upload/img/event/tmp/{{ poster }}" alt="">
                                                                            {% endif %}
                                                                        {% else %}
                                                                            <img src="/upload/img/event/{{ event.id }}/poster/{{ poster.image }}" alt="">
                                                                        {% endif %}

                                                                    </div>

                                                                </div><!-- /.modal-content -->
                                                            </div><!-- /.modal-dialog -->
                                                        </div><!-- /.modal -->


                                                        {#<div id="poster-img" class="modal" role="dialog" aria-hidden="true">#}

                                                                {#<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>#}

                                                            {#<div class="modal-body">#}
                                                                {#{% if eventPreview is defined %}#}
                                                                    {#{% if eventPreviewPoster is defined %}#}
                                                                        {#<img src="/upload/img/event/{{ event.id }}/poster/{{ eventPreviewPoster }}" alt="">#}
                                                                    {#{% else %}#}
                                                                        {#<img src="/upload/img/event/tmp/{{ poster }}" alt="">#}
                                                                    {#{% endif %}#}
                                                                {#{% else %}#}
                                                                    {#<img src="/upload/img/event/{{ event.id }}/poster/{{ poster.image }}" alt="">#}
                                                                {#{% endif %}#}

                                                            {#</div>#}
                                                        {#</div>#}
                                                        {% set count = count + 1 %}
                                                    {% endif %}

                                                    {% if flyer is defined %}
                                                        {% if eventPreview is defined %}
                                                            {% if eventPreviewFlyer is defined %}
                                                                <a href="#flyer-img" data-toggle="modal" class="clearfix" style="float:left;cursor: pointer"><img style="width: 93px; height: 84px" src="/upload/img/event/{{ event.id }}/flyer/{{ eventPreviewFlyer }}" alt="" /></a>
                                                            {% else %}
                                                                <a href="#flyer-img" data-toggle="modal" class="clearfix" style="float:left;cursor: pointer"><img style="width: 93px; height: 84px" src="/upload/img/event/tmp/{{ flyer }}" alt="" /></a>
                                                            {% endif %}
                                                        {% else %}
                                                            <a href="#flyer-img" data-toggle="modal" class="clearfix" style="float:left;cursor: pointer"><img style="width: 93px; height: 84px" src="/upload/img/event/{{ event.id }}/flyer/{{ flyer.image }}" alt="" /></a>
                                                        {% endif %}


                                                        <!-- Modal -->
                                                        <div class="modal fade" id="flyer-img" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                                    <div class="modal-body">

                                                                            {% if eventPreview is defined %}
                                                                                {% if eventPreviewFlyer is defined %}
                                                                                    <img src="/upload/img/event/{{ event.id }}/flyer/{{ eventPreviewFlyer }}" alt="" >
                                                                                {% else %}
                                                                                    <img src="/upload/img/event/tmp/{{ flyer }}" alt="" >
                                                                                {% endif %}
                                                                            {% else %}
                                                                                <img src="/upload/img/event/{{ event.id }}/flyer/{{ flyer.image }}" alt="" >
                                                                            {% endif %}


                                                                    </div>

                                                                </div><!-- /.modal-content -->
                                                            </div><!-- /.modal-dialog -->
                                                        </div><!-- /.modal -->

                                                        {#<div id="flyer-img" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">#}

                                                                {#<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>#}
                                                            {#<div class="modal-body">#}
                                                                {#{% if eventPreview is defined %}#}
                                                                    {#{% if eventPreviewFlyer is defined %}#}
                                                                        {#<img src="/upload/img/event/{{ event.id }}/flyer/{{ eventPreviewFlyer }}" alt="" >#}
                                                                    {#{% else %}#}
                                                                        {#<img src="/upload/img/event/tmp/{{ flyer }}" alt="" >#}
                                                                    {#{% endif %}#}
                                                                {#{% else %}#}
                                                                    {#<img src="/upload/img/event/{{ event.id }}/flyer/{{ flyer.image }}" alt="" >#}
                                                                {#{% endif %}#}

                                                            {#</div>#}
                                                        {#</div>#}
                                                        {% set count = count + 1 %}
                                                    {% endif %}
                                                    <a href="#" class="btn-all-img">{{ count }}<span class="icon-all-img"></span></a>
                                                </div>
                                           {% endif %}
                                        </div>
                                    {% endif %}

                                    <div   class="text-description" style="padding-right: 20px;">
                                        <h4 class="name-link">{{ event.name }}</h4>

                                        <div class="date-list">
                                            {% if event.start_date != '0000-00-00' %}
                                                <i class="icon-time"></i>
                                                <span class="date-start">{{ dateToFormat(event.start_date, '%d %b %Y') }}</span>
                                                {% if dateToFormat(event.start_date, '%R') != '00:00' %}
                                                    starts at
                                                    <span class="date-time">{{ dateToFormat(event.start_date, '%R') }}</span>
                                                {% endif %}
                                            {% endif %}
                                            {#{% if event.start_date_nice is defined  %}
                                                <i class="icon-time"></i>
                                                <span class="date-start">{{ event.start_date_nice }}</span>
                                                {% if event.start_time is defined %}
                                                    starts at
                                                    <span class="date-time">{{ event.start_time }}</span> <span class="day-title"></span>
                                                {% endif %}
                                            {% endif %}#}
                                        </div>
                                        <div class="description-text">
                                            <p style="word-wrap: break-word;">{{ event.description|nl2br }}</p>

                                            {% if event.tickets_url != '' %}
                                                <a href="{{ event.tickets_url }}" target="_blank">You can buy tickets here</a>
                                            {% endif %}
                                        </div>
                                        <div class="btn-hide clearfix">

                                            {% if not (event.memberpart|length) %}
                                                <span>So, what's your plan?</span>
                                            {% endif %}
                                            <div class="event-site clearfix">
                                                {% if not (event.memberpart|length) %}
                                                    <button class="btn" id="event-join">I'm going!</button>
                                                    <button class="btn" id="event-maybe">I'm interested!</button>
                                                    <button class="btn" id="event-decline">Don't like</button>
                                                {% else %}
                                                    {% if event.memberpart == 1 %}
                                                        <button class="btn" id="event-join" disabled = true>I'm going!</button>
                                                    {% endif %}

                                                    {% if event.memberpart == 2 %}
                                                        <button class="btn" id="event-maybe" disabled = true>I'm interested!</button>
                                                    {% endif %}
                                                {% endif %}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="sidebar-box sidebar-box_event">

                                    <div class="event-list-btn">
                                        <div class=" clearfix">
                                            {% if event.venue.name is defined %}
                                                <div class=" place-address">
                                                    <span>{{ event.venue.name|striptags }}</span>
                                                </div>
                                            {% else %}
                                                {% if event.location.alias is defined %}
                                                    <div class=" place-address">
                                                        <span>{{ event.location.alias|striptags }}</span>
                                                    </div>
                                                {% endif %}
                                            {% endif %}

                                            <button class="btn btn-block btn_invite" type="button" id="fb-invite">
                                                <img alt="" src="/img/demo/btn-m.png">
                                                Invite friends
                                            </button>
                                            <div id="friendsBlock"></div>
                                            <input type="button" value="Invite All" id="fb-invite-all" style="display: none"/>
                                            {% if event.site|length %}
                                                <div class="event-site clearfix">
                                                    {% for site in event.site %}
                                                        <p>web-site : <a href="val" target="_blank">{{ site.url }}</a></p>
                                                    {% endfor %}
                                                </div>
                                            {% endif %}

                                            <div class="event-list-category">

                                                {% if event.category|length %}

                                                    {% for cat in event.category %}
                                                        <span class=" category-title {{ cat.key }}-title {% if cat.key == 'other' %}uncategorized_label{% endif %}">{{ cat.name }}</span>
                                                    {% endfor %}

                                                    <div class="sub_category clearfix">
                                                        {% for Ctag in event.tag %}
                                                        <div>
                                                            <a href="#"><span>{{ Ctag.name }}</span></a>
                                                        </div>
                                                        {% endfor %}
                                                    </div>
                                                    <a href="#" class="show-all">show all tags</a>

                                                    {% if event.category.getFirst().key == 'other' %}
                                                        <span class="btn btn-block suggest-btn" id="suggestCategoryBtn"title="Suggest Category">Suggest Category</span>
                                                        <ul id="suggestCategoriesBlock"  class="select-category">
                                                            {% for index, node in categories %}
                                                                <li><a data-catkey="{{ node['key'] }}" href="/suggest-event-category/{{ event.id }}/{{ node['id'] }}" style="color: #ffffff; display: block">{{ node['name'] }}</a></li>
                                                            {% endfor %}
                                                        </ul>
                                                    {% endif %}
                                                {% else %}
                                                    <span class="btn btn-block suggest-btn uncategorized_label" id="suggestCategoryBtn" >Suggest category</span>

                                                    {#<span class="btn" id="suggestCategoryBtn" title="Suggest Category">?</span>#}
                                                    <ul id="suggestCategoriesBlock"  class="select-category">
                                                        {% for index, node in categories %}
                                                            <li><a href="/suggest-event-category/{{ event.id }}/{{ node['id'] }}" style="color: #ffffff; display: block">{{ node['name'] }}</a></li>
                                                        {% endfor %}
                                                    </ul>
                                                {% endif %}
                                            </div>

                                            {% if event.fb_uid is defined %}
                                                <div class="event-site clearfix">
                                                    <a target="_blank" href="https://www.facebook.com/events/{{ event.fb_uid }}">Facebook link</a>
                                                </div>
                                            {% endif %}

                                        </div>
                                    </div>
                                </div>
                                {#<div id="content_right">#}
                                    {#<div id="content_right_inner">#}
                                        {#<div id="content_center">#}
                                            {#<div class="event-photo">#}
                                                {#<img src="../img/big.jpg" alt="">#}
                                            {#</div>#}
                                            {#<div id="content-box" style="padding-right: 20px">#}
                                                {#<h4 class="name-link">{{ event.name }}</h4>#}

                                                {#<div class="date-list">#}
                                                    {#{% if event.start_date_nice is defined  %}#}
                                                        {#<i class="icon-time"></i>#}
                                                        {#<span class="date-start">{{ event.start_date_nice }}</span>#}
                                                        {#{% if event.start_time is defined %}#}
                                                            {#start at#}
                                                            {#<span class="date-time">{{ event.start_time }}</span> <span class="day-title"></span>#}
                                                        {#{% endif %}#}
                                                    {#{% endif %}#}
                                                {#</div>#}
                                                {#<div class="description-text">#}
                                                    {#<p style="word-wrap: break-word;">{{ event.description|nl2br }}</p>#}

                                                    {#{% if event.tickets_url != '' %}#}
                                                       {#<a href="{{ event.tickets_url }}" target="_blank">You can buy tickets here</a>#}
                                                    {#{% endif %}#}
                                                {#</div>#}
                                                {#<div class="btn-hide clearfix">#}

                                                    {#{% if not (event.memberpart|length) %}#}
                                                        {#<span>So, whats your plan?</span>#}
                                                    {#{% endif %}#}
                                                    {#<div class="event-site clearfix">#}
                                                        {#{% if not (event.memberpart|length) %}#}
                                                            {#<button class="btn" id="event-join">I`m going!</button>#}
                                                            {#<button class="btn" id="event-maybe">I`m interested!</button>#}
                                                            {#<button class="btn" id="event-decline">Don`t like</button>#}
                                                        {#{% else %}#}
                                                            {#{% if event.memberpart == 1 %}#}
                                                                {#<button class="btn" id="event-join" disabled = true>I`m going!</button>#}
                                                            {#{% endif %}#}

                                                            {#{% if event.memberpart == 2 %}#}
                                                                {#<button class="btn" id="event-maybe" disabled = true>I`m interested!</button>#}
                                                            {#{% endif %}#}
                                                        {#{% endif %}#}
                                                    {#</div>#}
                                                {#</div>#}
                                            {#</div>#}
                                        {#</div>#}

                                        {#<div class="sidebar-box">#}


                                            {#<div class="event-list-btn">#}
                                                {#<div class=" clearfix">#}
                                                    {#{% if event.venue.name is defined %}#}
                                                        {#<div class=" place-address">#}
                                                            {#<span>{{ event.venue.name|striptags }}</span>#}
                                                        {#</div>#}
                                                    {#{% else %}#}
                                                        {#{% if event.location.alias is defined %}#}
                                                            {#<div class=" place-address">#}
                                                                {#<span>{{ event.location.alias|striptags }}</span>#}
                                                            {#</div>#}
                                                        {#{% endif %}#}
                                                    {#{% endif %}#}

                                                    {#<button class="btn btn-block btn_invite" type="button" id="fb-invite">#}
                                                        {#<img alt="" src="/img/demo/btn-m.png">#}
                                                        {#Invite friends#}
                                                    {#</button>#}
                                                    {#<div id="friendsBlock"></div>#}
                                                    {#<input type="button" value="Invite All" id="fb-invite-all" style="display: none"/>#}
                                                    {#{% if event.site|length %}#}
                                                        {#<div class="event-site clearfix">#}
                                                            {#{% for site in event.site %}#}
                                                                {#<p>web-site : <a href="val" target="_blank">{{ site.url }}</a></p>#}
                                                            {#{% endfor %}#}
                                                        {#</div>#}
                                                    {#{% endif %}#}

                                                    {#<div class="event-list-category">#}

                                                        {#{% if event.category|length %}#}

                                                            {#{% for cat in event.category %}#}
                                                                {#<span class=" category-title {{ cat.key }}-title {% if cat.key == 'other' %}uncategorized_label{% endif %}">{{ cat.name }}</span>#}
                                                            {#{% endfor %}#}

                                                            {#&#123;&#35;--------- comment tags#}

                                                            {#<div class="sub_category clearfix">#}
                                                               {#<div>#}
                                                                   {#<a href="#"><span>pop rock</span></a>#}
                                                               {#</div>#}
                                                               {#<div>#}
                                                                   {#<a href="#"><span>new album</span></a>#}
                                                               {#</div>#}
                                                               {#<div>#}
                                                                   {#<a href="#"><span>event of the year 2014</span></a>#}
                                                               {#</div>#}
                                                            {#</div>#}
                                                            {#<a href="#" class="show-all">show all tags</a>#}
                                                            {#&#35;&#125;#}

                                                            {#{% if event.category.getFirst().key == 'other' %}#}
                                                                {#<span class="btn btn-block suggest-btn" id="suggestCategoryBtn"title="Suggest Category">Suggest Category</span>#}
                                                                {#<ul id="suggestCategoriesBlock"  class="select-category">#}
                                                                    {#{% for index, node in categories %}#}
                                                                        {#<li><a data-catkey="{{ node['key'] }}" href="/suggest-event-category/{{ event.id }}/{{ node['id'] }}" style="color: #ffffff; display: block">{{ node['name'] }}</a></li>#}
                                                                    {#{% endfor %}#}
                                                                {#</ul>#}
                                                            {#{% endif %}#}
                                                        {#{% else %}#}
                                                            {#<span class="btn btn-block suggest-btn uncategorized_label" id="suggestCategoryBtn" >Suggest category</span>#}

                                                            {#&#123;&#35;<span class="btn" id="suggestCategoryBtn" title="Suggest Category">?</span>&#35;&#125;#}
                                                            {#<ul id="suggestCategoriesBlock"  class="select-category">#}
                                                                {#{% for index, node in categories %}#}
                                                                    {#<li><a href="/suggest-event-category/{{ event.id }}/{{ node['id'] }}" style="color: #ffffff; display: block">{{ node['name'] }}</a></li>#}
                                                                {#{% endfor %}#}
                                                            {#</ul>#}
                                                        {#{% endif %}#}
                                                    {#</div>#}

                                                    {#{% if event.fb_uid is defined %}#}
                                                        {#<div class="event-site clearfix">#}
                                                            {#<a target="_blank" href="https://www.facebook.com/events/{{ event.fb_uid }}">Facebook link</a>#}
                                                        {#</div>#}
                                                    {#{% endif %}#}

                                                {#</div>#}
                                            {#</div>#}
                                        {#</div>#}
                                    {#</div>#}
                                {#</div>#}



                                </div>
                        </div>

                            {% include 'layouts/sharebar.volt' %}

                        <div class="padd_30"></div>
                        <div class="row-fluid">
                            <div class="span12">
                                <div class="comment-box">
                                    <h2>Leave comments</h2>
                                    {% if eventPreview is defined %}
                                        <img src="/img/comment_tmp.png" alt=""/>
                                        <div style="height: 20px"></div>
                                    {% else %}
                                        <fb:comments href="http://dev.eventweekly.com/{{ toSlugUri(event.name) }}-{{ event.id }}"></fb:comments>
                                        {#<div id="fb-comments-block" class="fb-comments" data-href="http://dev.eventweekly.com/{{ toSlugUri(event.name) }}-{{ event.id }}" data-numposts="2" data-colorscheme="light"></div>#}
                                    {% endif %}
                                </div>
                            </div>
                        </div>
            </div>
        </div>
    </div>
</div>
    <fb:ref href="http://dev.eventweekly.com/{{ toSlugUri(event.name) }}-{{ event.id }}" />
{% endblock %}


