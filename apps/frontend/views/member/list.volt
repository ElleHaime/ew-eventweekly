{% extends "layouts/base.volt" %}

{% block content %}

<div class="container content_noBorder">
	<div class="row-fluid profile-top">
        <div class="span9">
            <div class="profile-img">
                <img src="{{ member.logo }}" alt="" id="member_logo">
            </div>
            <div class="profile-info clearfix">
                <a href="profile/edit" class="profile-name" id="member_logo">{{ member.name }}</a>
                <div class="clear"></div>
                <span class="location-state"><span id="member_location">{{ member.location.name }}</span> <span id="member_address">{{ member.address }}</span></span>
                <span class="mail">{{ member.email }}</span>

                <div class="profile-btn" style="padding-top: 50px; display:none;" id="do_update_profile">
                 	<span class="mail" style="padding-bottom:20px;">Your Facebook and EventWeeklky profiles are different. Do you want sync?</span>
                	<button class="btn" id="sync_profiles">Yes, please </button>
                	<button class="btn" id="no_sync_profiles">No, thank you </button>
                	<button class="btn" id="he_is_nervous">Never show me this fuckin button </button>
               	</div>
            </div>

        </div>
        <div class="span3">
            <div class="profile-btn">
                <button class="btn ">Facebook button </button>
                <p>import interests from facebook <br>
                 / login through facebook</p>
            </div>
        </div>
    </div>
</div>

{% if acc_external is defined %}
	<input type='hidden' name='check_ext_profile' id='check_ext_profile's>
	<input type='hidden' name='member_uid' id='member_uid' value='{{ acc_external.account_uid }}'>
	<input type='hidden' name='acc_difference' id='acc_pic' ew_val='logo' value='{{ member.logo }}'>
	<input type='hidden' name='acc_difference' id='acc_username' ew_val="account_id" value='{{ acc_external.account_id }}'>
	<input type='hidden' name='acc_difference' id='acc_current_address' ew_val="address" value='{{ member.address }}'>
	<input type='hidden' name='acc_difference' id='acc_current_location' ew_val="location" value='{{ member.location.name }}'>
{% endif %}

{% endblock %}
