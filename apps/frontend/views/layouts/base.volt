<!DOCTYPE html>
<html>
<head>
    <title>EventWeekly</title>
    <meta charset="utf-8"/>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link type="image/ico" href="/img/128.ico" rel="icon">

    {% if logo is defined %}
    <meta property="og:image" content="http://events.apppicker.com/upload/img/event/{{ logo }}"/>
    <meta property="og:title" content="EventWeekly"/>
    {% endif %}

    {{ stylesheet_link('/css/bootstrap.min.css') }}
    {{ stylesheet_link('/css/bootstrap-responsive.min.css') }}
    {{ stylesheet_link('/css/bootstrap-datetimepicker.min.css') }}
    {{ stylesheet_link('/css/style.css') }}
    {{ stylesheet_link('/css/jake.css') }}

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <script data-main="/js/config" src="/js/requirePlugins/require.js"></script>
</head>

<body>
<div style="display:none;" id="current_location" latitude="{{ location.latitude }}" longitude="{{ location.longitude }}"></div>

<div id="fb-root"></div>
{% if external_logged is defined %}
    <div id="external_logged" extname="{{ external_logged }}"></div>
{% endif %}
{% if acc_external is defined %}
    <input type="hidden" id="member_ext_uid" value="{{ acc_external.account_uid }}">
{% endif %}

{% include 'layouts/accheader.volt' %}

{% block content %}
{% endblock %}

</body>
</html>