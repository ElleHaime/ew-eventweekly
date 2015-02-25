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
                                                    <label for="name" >Name<span style="color: white;">*</span>:</label>

                                                    <div class="controls" >
                                                        <input type="text" id="name" name="name" class="input-registration-control" {% if member.name %} value="{{member.name}}" {% endif %}>
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <label for="address" >Address:</label>

                                                    <div class="controls" >
                                                        <input type="text" id="address" name="address" class="input-registration-control">
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <label for="phone" >Phone:</label>
                
                                                    <div class="controls" >
                                                        <input type="text" id="phone" name="phone" class="input-registration-control">
                                                    </div>
                                                </div>

                                                <div class="control-group">
                                                    <label for="extra_email" >E-mail:</label>

                                                    <div class="controls" >
                                                        <input type="text" id="extra_email" name="extra_email" class="input-registration-control">
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
                                                                    <input type="text" id="uLocation" class="input-registration-control">
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>


                                                <div class="control-group" id="save_button_div">
                                                    <div class="controls">
                                                        {{ memberForm.render('logo') }}
                                                        <button type="submit" id="save-member" value="Save" class="ew-button"><i class="fa fa-save"></i>Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>



                                    <div class=" profile-btn-rt clearfix profile_bottom">
                                        <div class="profile-btn">
                                                <p>You can import interests from facebook (need login via facebook)</p>
                                                <div id="facebook_sync_button_div">
                                                    <button id="syncFbAcc" class="ew-button"><i class="fa fa-facebook-square"></i>Facebook sync</button>
                                                </div>
                                        </div>
                                    </div>


                                    <div class=" profile-btn-rt clearfix profile_bottom">
                                            <div class="change-box">
                                                <button class="ew-button" onclick="window.location = '/profile/change-password'"><i class="fa fa-lock"></i>Change password</button>
                                            </div>
                                    </div>

                                    <div class=" profile-btn-rt clearfix profile_bottom">
                                            
                                                <a id="deleteMemberAcc">Delete account</a>
                    						
                                    </div>
                                            
                                        
                                    
                                </div>
                                </div>
                            </div>
                                


                </div> <!-- left -->

                <div id="profile_right" >
                    <div class="categories-accordion">

                        <div class="categories-accordion__item">
                            <div class="categories-accordion__head">
                                <div class="categories-accordion__line"></div>

                                <div class="row-fluid">
                                    <div class="span12">
                                    
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

                                        {% if member_categories['tag']['id'] is defined %}
                                            <input id="recordTagId" name="recordTagId" type="hidden" value="{{ member_categories['tag']['id'] }}" />
                                        {% endif %}

                                        <input id="tagIds" name="tagIds" type="hidden" value="{{ tagIds }}" />
                                    </form>

                                    <div class="settings-box">


                                        <!-- form with checkboxes1111111111111111111111111111111111111111111111111111111111 -->
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
                                                    


                                                <div class="categories-accordion__item">
                                      
<div class="checkbox">
                                                    <div class="1form-checkbox 1pure-u-1-2">
                                                        <input type="checkbox" class="checkbox" id="tag-{{ node['name'] }}" style="display:none;">
                                                        <label class='catNamen' for="tag-{{ node['name'] }}" title="{{ node['name'] }}"
    ><span><span></span></span>{{ node['name'] }}</label>    <!-- name of category -->
                                                    </div>
</div>




                                                    <div class="hide-box">
                                                        <div class="activity clearfix">
                                                            <div class="event-site clearfix">
                                                                
                                                                {% for tag in tags %}
                                                                    {% if node['id'] == tag['category_id'] %}

                                                                        {% set checked = true %}
                                                                        {% if member_categories['tag']['value'] is defined %}
                                                                            {% for tagId in member_categories['tag']['value'] %}
                                                                                {% if tagId == tag['id'] %}
                                                                                    {% set checked = false %}
                                                                                {% endif %}
                                                                            {% endfor %}
                                                                        {% endif %}
                                                                        

    <div class="form-checkbox 1pure-u-1-2 event-category  clearfix marker {% if checked %}disabled-marker{% endif %}" data-id="{{ tag['id'] }}">
        <input type="checkbox" name="tag-{{ tag['name']}}" {% if checked %}checked{% endif %} >
        <label for="tag-{{ tag['name']}}" title="{{ tag['name'] }}"
    style="overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 90%;
  padding: 2px 0;"><span><span></span></span>{{ tag['name'] }}</label>
 
    </div>


    

                                                                    {% endif %}
                                                                {% endfor %}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>



                                                </div>
                                            {% endfor %}
                                            </form>
                                            <!-- form with checkboxes1111111111111111111111111111111111111111111111111111111111 -->
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