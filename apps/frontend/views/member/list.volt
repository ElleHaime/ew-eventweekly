{% extends "layouts/base_new.volt" %}

{% block content %}
<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>

<div class="page" id="profile_page">
    <div class="page__wrapper">
        <section id="content" class="container page-search" >
            <h1 class="page__title">Profile settings</h1>
                
                <div id="profile_left">
                        <!-- MAIN CONTENT-->
                        
                            <div class="profile-box">
                                <div class="row-fluid">
                                <div class="span12">
                                    <div class=" profile-info-lf clearfix">
                                        <div class="profile-img" >
                                            <div class="btn btn-block btn-file" >
                                                <div id="file">
                                                    <div class="profile-img-box" >
                                                    <img id="img-box" alt="" 
                                                            {% if member.logo != '' %}
                                                                src="{{ member.logo }}"
                                                            {% else %}
                                                                src ='/img/demo/h_back_1.jpg'
                                                            {% endif %}
                                                         id="member_logo">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <form action="/member/edit" id="profile_form" enctype="multipart/form-data" method="post">
                                            <div class="profile-info clearfix">

                                                <div class="control-group">
                                                    <label>{{ form.label('name') }}<span style="color: white;">*</span></label>

                                                    <div class="controls" >
                                                        {{ form.render('name') }}
                                                        {{ form.messages('name') }}
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <label for="address" >{{ form.label('address') }}</label>

                                                    <div class="controls" >
                                                        {{ form.render('address') }}
                                                        {{ form.messages('address') }}
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <label for="phone" >{{ form.label('phone') }}</label>
                
                                                    <div class="controls" >
                                                        {{ form.render('phone') }}
                                						{{ form.messages('phone') }}
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <label for="extra_email" >{{ form.label('extra_email') }}</label>
                                                    <div class="controls" >
                                                        {{ form.render('extra_email') }}
                                						{{ form.messages('extra_email') }}
                                                    </div>
                                                </div>

                                                <div class="row-fluid">
                                                    <div class="span12">
                                                        <form action="#" method="post" id="mLocationForm">
                                                            {% if conflict is defined %}
                                                                <p id="lConflict" style="color: #333333">Your location from facebook does not match to location from IP. Please type and choose location from list.</p>
                                                            {% endif %}

                                                            <div class="control-group">
                                                                <label for="uLocation" class="control-label mail">Default location:</label>
                                                                <div class="controls">
                                                                    <input type="text" id="uLocation" class="input-registration-control" value="{{ member.location.city }}, {{ member.location.country }}">
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>


                                                <div class="control-group" id="save_button_div">
                                                    <div class="controls">
                                                        {{ form.render('logo') }}
                                                        <button type="submit" id="save-member" value="Save" class="ew-button"><i class="fa fa-save"></i>Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div class=" profile-btn-rt clearfix profile_bottom">
                                        <div class="profile-btn">
                                        		{% if external_logged is defined and external_logged == 'facebook' %}
                                        			<p>Get your facebook events right now!</p>
                                        		{% else %}
                                                	<p>You can import interests from facebook (need login via facebook)</p>
                                                {% endif %}
                                                <div id="facebook_sync_button_div">
                                                    <button id="syncFbAcc" class="ew-button"><i class="fa fa-facebook-square"></i>Facebook sync</button>
                                                </div>
                                        </div>
                                    </div>


									{% if external_logged is not defined %}
	                                    <div class=" profile-btn-rt clearfix profile_bottom">
	                                            <div class="change-box">
	                                                <button class="ew-button" onclick="window.location = '/profile/change-password'"><i class="fa fa-lock"></i>Change password</button>
	                                            </div>
                                    	</div>
                                    {% endif %}

                                    <div class=" profile-btn-rt clearfix profile_bottom">
                                            
                                                <a id="deleteMemberAcc">Delete account</a>
                    						
                                    </div>
                                            
                                        
                                    
                                </div>
                                </div>
                            </div>
                                


                </div> <!-- left -->

                <div id="profile_right" >
                    <div class="categories-accordion">

                <div class="b-filters__buttons">
                    <a class="check_all ew-button"><i class="fa fa-check-square-o"></i> Check all</a>
                    <a class="uncheck_all ew-button"><i class="fa fa-square-o"></i> Uncheck all</a>
                </div>
                        <div class="categories-accordion__item">
                            <div class="categories-accordion__head">
                            
                                <div class="row-fluid">
                                    <div class="span12">
                                      <div class="settings-box">


                                    <!-- form with checkboxes -->
                                        <form action="/member/save-filters" method="post" id="filters">
                                            {% for index, node in userFilters %}
                                                <div class="settings-box-one {% if node['fullCategorySelect'] is defined %}active-box{% endif %}">
                                                    <input name="fieldId" class="fieldId" type="hidden" value="{{ node['id'] }}" />
                                                <div class="categories-accordion__item">

						                            <div class="toggle_category_profile_page">
						                                <div class="categories-accordion__line"></div>
						                                <a class="categories-accordion__arrow categories-accordion__arrow--is-expanded" id="toggle_category_button">
						                                    <i class="icon"></i> Expand
						                                </a>
						                            </div>
						                            
													<div class="checkbox">
	                                                    <div class="form-checkbox pure-u-1-2">
	                                                        <input type="checkbox" class="checkbox_category" name="category[{{ node['id'] }}]" id="tag-{{ node['name'] }}" 
	                                                        	style="display:visible;" {% if node['fullCategorySelect'] is defined %} checked{% endif %}>
	                                                        <label class='catNamen' for="tag-{{ node['name'] }}" title="{{ node['name'] }}">
	                                                        	<span><span>
	                                                        	</span></span>
	                                                        	{{ node['name'] }}
	                                                        </label>
	                                                    </div>
													</div>

                                                    <div class="hide-box">
                                                        <div class="activity clearfix">
                                                            <div class="event-site clearfix">
                                                                
                                                                {% for tag in node['tags'] %}

                                                                        {% set checked = false %}
                                                                        {% if tag['inPreset'] is defined %}
																			{% set checked = true %}
                                                                        {% endif %}

		<div class="form-checkbox 1pure-u-1-2 event-category  clearfix marker {% if checked %}disabled-marker{% endif %}" data-id="{{ tag['id'] }}">
        <input type="checkbox" class="userFilter-tag" name="tag[{{ tag['id']}}]" {% if checked %}checked{% endif %} >
        <label for="tag-{{ tag['id']}}" title="{{ tag['name'] }}" style="overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 90%;
  padding: 2px 0;"><span><span></span></span>{{ tag['name'] }}</label>
 
    </div>

                                                                {% endfor %}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>



                                                </div>
                                            {% endfor %}
                                            </form>
                                    <!-- form with checkboxes -->
                                            <div class="profile-btn">
                                                 
                                                 <button id="saveFilter" class="ew-button"><i class="fa fa-save"></i>Save</button>
                                            </div>

                                    </div>
                                </div>


                                </div>

                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>
</div>
{% include 'layouts/accfilter_new.volt' %}
	<input type='hidden' id="passwordChanged" value='{% if passwordChanged is defined %}1{% else %}0{% endif %}'>
	
    {% if acc_external is defined %}
        <input type='hidden' name='check_ext_profile' id='check_ext_profile's>
        <input type='hidden' name='member_uid' id='member_uid' value='{{ acc_external.account_uid }}'>
        <input type='hidden' name='acc_difference' id='acc_pic' ew_val='Member.logo' value='{{ member.logo }}'>
        <input type='hidden' name='acc_difference' id='acc_email' ew_val='Member.email' value='{{ member.email }}'>
        <input type='hidden' name='acc_difference' id='acc_username' ew_val="MemberNetwork.account_id" value='{{ acc_external.account_id }}'>
        <input type='hidden' name='acc_difference' id='acc_current_address' ew_val="Member.address" value='{{ member.address }}'>
    {% endif %}

{% endblock %}