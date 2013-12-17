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
    {{ stylesheet_link('/css/bootstrap-datetimepicker.min.css') }}
    {{ stylesheet_link('/css/styles.css') }}   
    {{ stylesheet_link('/css/jake.css') }}    
	{{ stylesheet_link('/css/old.css') }}
    {{ stylesheet_link('/css/noti.css') }}
    {{ stylesheet_link('/css/responsive-new.css') }}
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <script src="https://code.jquery.com/jquery.js"></script>
    <script data-main="/js/config" src="/js/requirePlugins/require.js"></script>
    <script data-main="/js/config" src="/js/bootstrap.min.js"></script>
</head>

<body>

{% include 'layouts/stuff.volt' %}
{% include 'layouts/accheader.volt' %}

{% block content %}
{% endblock %}

</body>
</html>