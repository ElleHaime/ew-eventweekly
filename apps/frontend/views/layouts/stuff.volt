<div id="fb-root" display="none;"></div>

<div style="display:none;" id="current_location" latitude="{{ location.latitude }}" longitude="{{ location.longitude }}"></div>

{% if flashMsgText is defined %}
	<div style="display:none;" id="splash_messages" flashMsgText="{{ flashMsgText }}" flashMsgType="{{ flashMsgType }}"></div>
{% endif %}

{% if location_conflict is defined %}
	<div style="display:none;" id="conflict_location" location_conflict="{{ location_conflict }}"></div>
{% endif %}

{% if external_logged is defined %}
    <div id="external_logged" extname="{{ external_logged }}" display="none;"></div>
{% endif %}

{% if acc_external is defined %}
    <input type="hidden" id="member_ext_uid" value="{{ acc_external.account_uid }}">
{% endif %}

{% if member.id is defined %}
    <input id="isLogged" type="hidden" value="1" />
{% else %}
    <input id="isLogged" type="hidden" value="1" />
{% endif %}

{% if isMobile is defined %}
    <input id="isMobile" type="hidden" value="{{ isMobile }}" />
{% endif %}