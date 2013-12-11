{% extends "layouts/base.volt" %}

{% block content %}
<div class="padd_70"></div>
<div class="container content_noBorder">
	<div class="row-fluid profile-top">
	
        <div class="span9">
            <div class="profile-img">
                <img alt="" 
					{% if member.logo != '' %}
        				src="{{ member.logo }}" 
					{% else %}
        				src ='/img/demo/h_back_1.jpg'
        			{% endif %}
                id="member_logo">
            </div>
            <div class="profile-info clearfix">
                <a style="cursor:pointer; text-decoration:none;" class="profile-name" id="member_name">
                	{{ member.name }}
                </a>
                <input type="text" name="member_name" id="member_name_update" value="{{ member.name }}" style="background:#29ABE2; color: #fff; border: 0; display:none;">
                
                <div class="clear"></div>
                <span class="location-state"><span id="member_location">{{ member.location.alias }}</span> <span id="member_address">{{ member.address }}</span></span>
                <span class="mail">{{ member.email }}</span>
				<input type="text" name="member_email" id="member_email_update" value="{{ member.email }}" style="background:#29ABE2; color: #fff; border: 0; display:none;">

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

    <div class="row-fluid">
        <div class="span12">
            <h2>Filters:</h2>

            <h4>Categories:</h4>
            <form action="/member/save-filters" method="post">
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


<script type="text/javascript">
	$('#member_name').click(function () { 
		
	});
</script>

{% endblock %}
