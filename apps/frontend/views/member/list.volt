{% extends "layouts/base.volt" %}

{% block content %}

    <!-- MAIN CONTENT-->
    <div class="container" id="content_noBorder">
        <div class="profile-box">
            <div class="row-fluid">
                <div class="span9 profile-info-lf">
                    <div class="profile-img">
                        <div class="profile-img-box">
                        <img alt=""
                                {% if member.logo != '' %}
                                    src="{{ member.logo }}"
                                {% else %}
                                    src ='/img/demo/h_back_1.jpg'
                                {% endif %}
                             id="member_logo">
                        </div>
                        <div class="btn btn-block btn-file">
                            <div id="file">Change photo</div>
                        </div>
                    </div>

                    <form action="/member/edit" enctype="multipart/form-data" method="post">
                        <div class="profile-info clearfix">
                            <div class="control-group">
                                <div class="controls">
                                    <p class="profile-name">{{ member.name }}</p>
                                    {{ memberForm.messages('name') }}
                                </div>

                                <div class="controls" {% if member.name != "" %} style="display: none" {% endif %}>
                                    {{ memberForm.render('name') }}
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="controls">
                                    <span class="location-state" style="display: inline">{{ member.address }}</span>
                                    {{ memberForm.messages('address') }}
                                </div>

                                <div class="controls" {% if member.address != "" %} style="display: none" {% endif %}>
                                    {{ memberForm.render('address') }}
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="controls">
                                    <span class="phone">{{ member.phone }}</span>
                                    {{ memberForm.messages('phone') }}
                                </div>

                                <div class="controls" {% if member.phone != "" %} style="display: none" {% endif %}>
                                    {{ memberForm.render('phone') }}
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="controls">
                                    <span class="mail extra-email">{{ member.extra_email }}</span>
                                    {{ memberForm.messages('extra_email') }}
                                </div>

                                <div class="controls" {% if member.extra_email != "" %} style="display: none" {% endif %}>
                                    {{ memberForm.render('extra_email') }}
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="controls">
                                    {{ memberForm.render('logo') }}
                                    {{ memberForm.render('Save') }}
                                </div>
                            </div>
                        </div>

                        {#<div class="profile-info clearfix" style="display: none">
                            &#123;&#35;{{ form() }}&#35;&#125;
                                <div class="control-group">
                                    {{ memberForm.label('extra_email') }}

                                    <div class="controls">
                                        {{ memberForm.render('extra_email') }}
                                        {{ memberForm.messages('extra_email') }}
                                    </div>
                                </div>

                                <div class="control-group">
                                    {{ memberForm.label('name') }}

                                    <div class="controls">
                                        {{ memberForm.render('name') }}
                                        {{ memberForm.messages('name') }}
                                    </div>
                                </div>

                                <div class="control-group">
                                    {{ memberForm.label('phone') }}

                                    <div class="controls">
                                        {{ memberForm.render('phone') }}
                                        {{ memberForm.messages('phone') }}
                                    </div>
                                </div>

                                {{ memberForm.render('logo') }}

                                &#123;&#35;<div class="control-group">
                                    <label class="control-label" for="inputPassword">{{ memberForm.label('logo') }}</label>
                                    {{ memberForm.render('logo') }}

                                    <div class="controls">
                                        <button style="text-align: center; overflow: hidden; height: 42px; width: 227px;" class="btn btn-block btn-file"
                                                id="add-img-btn" type="button">Add Image</button>
                                        {{ memberForm.messages('logo') }}
                                    </div>
                                </div>&#35;&#125;

                                <div class="control-group">
                                    <div class="controls">
                                        {{ memberForm.render('Save',{'class':'btn btn-block'})}}
                                    </div>
                                </div>

                            &#123;&#35;<h4 class="profile-name">{{ member.name }}</h4>
                            <span class="location-state">{{ member.location.alias }} {{ member.address }}</span>
                            <span class="mail">{{ member.email }}</span>&#35;&#125;
                        </div>#}
                    </form>
                </div>

                <div class="span3 profile-btn-rt">
                    <div class="profile-btn">
                        <button class="btn btn-block ">Facebook Sinc</button>
                        <p>import interests from facebook
                            / login through facebook</p>

                        <div class="change-box">
                            <button class="btn btn-block" onclick="window.location = '/profile/change-password'">Change password</button>
                            <form action="#" class=" form-horizontal">
                                <div class="control-group">
                                    <label class="control-label">Old password</label>

                                    <div class="controls">
                                        <input type="text" placeholder="Old password">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Password</label>

                                    <div class="controls">
                                        <input type="text" placeholder="Password">
                                    </div>
                                </div>
                                <div class="control-group">

                                    <div class="controls">
                                        <button class="btn btn-block"> Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {#<div class="row-fluid">
                <div class="span9 profile-info-lf">
                    <h2>Your account information:</h2>

                    <form action="#" method="post" id="mLocationForm">
                        <label for="uLocation">Your current location is <strong id="mLocation">{{ location.alias }}</strong>. Change below:</label>
                        {% if conflict is defined %}
                            <p id="lConflict" style="color: #333333">Your location from Facebook does not match to location from IP. Please type and choose location from list.</p>
                        {% endif %}
                        <input type="text" id="uLocation"/>

                        &#123;&#35;<input type="submit" value="Save"/>&#35;&#125;
                    </form>
                </div>
            </div>#}

            <div class="profile-body">
                <hr/>
                <a href="#" class="edit-service"><span> customise your search event-profile </span></a>
                <form action="/member/save-filters" method="post" id="filters" style="display: none">
                    {% if member_categories['category']['id'] is defined %}
                        <input type="hidden" name="member_filter_category_id" value="{{ member_categories['category']['id'] }}"/>
                    {% endif %}
                    {% for index, node in categories %}
                        <label for="cat{{ index }}">
                            {% set checked = false %}
                            {% if member_categories['category'] is defined %}
                                {% for indx, id in member_categories['category']['value'] %}
                                    {% if id == node['id'] %}
                                        {% set checked = true %}
                                    {% endif %}
                                {% endfor %}
                            {% endif %}
                            <input type="checkbox" name="category[]" id="cat{{ index }}" value="{{ node['id'] }}" {% if checked %}checked{% endif %}/> - {{ node['name'] }}
                        </label>
                    {% endfor %}
                    <input type="submit" value="Save"/>
                </form>

                <div class="settings-box">
                    <form action="/member/save-filters" method="post">
                        {% if member_categories['category']['id'] is defined %}
                            {#<input type="hidden" name="member_filter_category_id" value="{{ member_categories['category']['id'] }}"/>#}
                        {% endif %}

                        {% for index, node in categories %}
                            {% set checked = false %}
                            {% if member_categories['category'] is defined %}
                                {% for indx, id in member_categories['category']['value'] %}
                                    {% if id == node['id'] %}
                                        {% set checked = true %}
                                    {% endif %}
                                {% endfor %}
                            {% endif %}

                            <div class="settings-box-one {% if checked %}active-box{% endif %}">
                                <input name="fieldId" class="fieldId" type="hidden" value="{{ node['id'] }}" />

                                <label class="checkbox">
                                    <span class="check-span {#{{ node['key'] }}#}other-color"><i class=" icon-ok icon-white"></i></span>
                                    {{ node['name'] }}
                                </label>
                                <div class="hide-box">
                                    <div class="activity clearfix">
                                        <label>Activity:</label>
                                        <div class="event-site clearfix">
                                            <div class="event-category clearfix">
                                                <span class="color-type {#{{ node['key'] }}#}other-color">one</span>
                                                <span class="arrow arrow-{#{{ node['key'] }}#}other"></span>
                                            </div>
                                            <div class="event-category disabled-marker clearfix">
                                                <span class="color-type {#{{ node['key'] }}#}other-color">two</span>
                                                <span class="arrow arrow-{#{{ node['key'] }}#}other"></span>
                                            </div>
                                            <div class="event-category clearfix">
                                                <span class="color-type {#{{ node['key'] }}#}other-color">three</span>
                                                <span class="arrow arrow-{#{{ node['key'] }}#}other"></span>
                                            </div>
                                            <div class="event-category clearfix">
                                                <span class="color-type {#{{ node['key'] }}#}other-color">four</span>
                                                <span class="arrow arrow-{#{{ node['key'] }}#}other"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="type-marker clearfix">
                                        <label>Type:</label>
                                        <div class="event-site clearfix">
                                            <div class="event-category clearfix">
                                                <span class="color-type {#{{ node['key'] }}#}other-color">one</span>
                                                <span class="arrow arrow-{#{{ node['key'] }}#}other"></span>
                                            </div>
                                            <div class="event-category disabled-marker clearfix">
                                                <span class="color-type {#{{ node['key'] }}#}other-color">two</span>
                                                <span class="arrow arrow-{#{{ node['key'] }}#}other"></span>
                                            </div>
                                            <div class="event-category clearfix">
                                                <span class="color-type {#{{ node['key'] }}#}other-color">three</span>
                                                <span class="arrow arrow-{#{{ node['key'] }}#}other"></span>
                                            </div>
                                            <div class="event-category clearfix">
                                                <span class="color-type {#{{ node['key'] }}#}other-color">four</span>
                                                <span class="arrow arrow-{#{{ node['key'] }}#}other"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {% endfor %}
                    </form>

                    <button id="saveFilter" class="btn" style="width: 250px; margin: 0px 0px 20px 0px;">Save</button>

                    <div class=" row-fluid add-settings-box clearfix">
                        <div class="span11"><p><i class="icon-plus"></i> Or add your interests manually: rock, queen, zombie walk, golf
                                party etc.</p></div>
                        <div class="span1">
                            <button class="btn btn-block"> add</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {% if acc_external is defined %}
        <input type='hidden' name='check_ext_profile' id='check_ext_profile's>
        <input type='hidden' name='member_uid' id='member_uid' value='{{ acc_external.account_uid }}'>
        <input type='hidden' name='acc_difference' id='acc_pic' ew_val='Member.logo' value='{{ member.logo }}'>
        <input type='hidden' name='acc_difference' id='acc_email' ew_val='Member.email' value='{{ member.email }}'>
        <input type='hidden' name='acc_difference' id='acc_username' ew_val="MemberNetwork.account_id" value='{{ acc_external.account_id }}'>
        <input type='hidden' name='acc_difference' id='acc_current_address' ew_val="Member.address" value='{{ member.address }}'>
    {% endif %}

{% endblock %}