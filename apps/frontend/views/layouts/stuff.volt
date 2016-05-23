<div id="fb-root" display="none;"></div>

<div style="display:none;" id="current_location" latitude="{{ location.latitude }}" longitude="{{ location.longitude }}"></div>

<input type="hidden" id="popupRedirect" value="">

{% if flashMsgText is defined %}
	<div style="display:none;" id="splash_messages" flashMsgText="{{ flashMsgText }}" flashMsgType="{{ flashMsgType }}"></div>
{% endif %}

{% if location_conflict is defined %}
	<div style="display:none;" id="conflict_location" location_conflict="{{ location_conflict }}"></div>
{% endif %}

{% if external_logged is defined %}
    <div id="external_logged" extname="{{ external_logged }}" display="none;"></div>
{% endif %}

{% if permission_base is defined %}
	<input type="hidden" id="permission_base" value="{{ permission_base }}">
{% else %}
	<input type="hidden" id="permission_base" values = "0">
{% endif %}

{% if permission_publish is defined %}
    <input type="hidden" id="permission_publish" value="{{ permission_publish }}">
{% else %}
	<input type="hidden" id="permission_publish" value="0">
{% endif %}

{% if permission_manage is defined %}
    <input type="hidden" id="permission_manage" value="{{ permission_manage }}">
{% else %}
	<input type="hidden" id="permission_manage" value="0">
{% endif %}

{% if acc_external is defined %}
    <input type="hidden" id="member_ext_uid" value="{{ acc_external.account_uid }}">
{% endif %}

{% if acc_synced is defined %}
    <input type="hidden" id="acc_synced" value="1">
{% endif %}

{% if member.id is defined %}
    <input id="isLogged" type="hidden" value="1" />
{% else %}
    <input id="isLogged" type="hidden" value="0" />
{% endif %}

{% if isMobile is defined %}
    <input id="isMobile" type="hidden" value="{{ isMobile }}" />
{% endif %}

{% if seoMode is defined %}
    <input id="seoMode" type="hidden" value="1" />
{% endif %}

{% if fbAppId is defined %}
    <input id="fbAppId" type="hidden" value="{{ fbAppId }}" />
{% endif %}

{% if fbAppVersion is defined %}
    <input id="fbAppVersion" type="hidden" value="{{ fbAppVersion }}" />
{% endif %}

{% if fbAppSecret is defined %}
    <input id="fbAppSecret" type="hidden" value="{{ fbAppSecret }}" />
{% endif %}

{% if searchTypeResult is defined %}
    <input id="searchTypeResult" type="hidden" value="{{ searchTypeResult }}" />
{% else %}
	<input id="searchTypeResult" type="hidden" value="List" />    
{% endif %}