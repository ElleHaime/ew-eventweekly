<!DOCTYPE html>
<html>
<head>
    <title>EventWeekly</title>
    <meta charset="utf-8"/>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <meta name="viewport" content="width = device-width, height = device-height, maximum-scale=1.0, minimum-scale=1.0" />  

    {#<script type="text/javascript">
        var meta = document.createElement('meta'), content = '';
        meta.setAttribute('name', 'viewport');
        if (window.innerWidth <= 480) {
            content = 'width=480,user-scalable=false';
        }else {
            content = 'width=device-width, initial-scale=1.0, user-scalable=no';
        }
        meta.setAttribute('content', content);
        var title = document.getElementsByTagName('title')[0];
        title.parentNode.insertBefore(meta, title);
    </script>#}
    <link type="image/ico" href="/img/128.ico" rel="icon">

    {% if eventMetaData is defined %}
        {% if logo is defined %}
            <meta property="og:image" content="{{ logo }}"/>
        {% endif %}
            <meta property="og:title" content="{{ eventMetaData.name|escape }}"/>
            <meta property="og:description" content="{{ eventMetaData.description|escape|striptags }}"/>
    {% else %}
        {% if logo is defined %}
            <meta property="og:image" content="{{ logo }}"/>
            <meta property="og:title" content="EventWeekly"/>
        {% endif %}
    {% endif %}

	{% if searchResult is defined %}
		{% if list is defined %}
			<script type="text/javascript">
		        window.searchResults = {{ list }};
		    </script>
		{% endif %}
	{% endif %}

    {{ stylesheet_link('/css/bootstrap.min.css') }}
    {#{{ stylesheet_link('/css/bootstrap-datetimepicker.min.css') }}#}
    {{ stylesheet_link('/css/normalBootstrapDateTimepicker.min.css') }}
    {#{{ stylesheet_link('/css/datepicker.css') }}#}
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

    <script data-main="/js/config" src="/js/requirePlugins/require.js"></script>
</head>

<body>
{% if eventPreview is defined %}
    <div class="preview_overlay" style="height: 100%;width: 100%;z-index: 10000;top:0;left:0;position:fixed;"></div>
{% endif %}
<div class="overlay">
</div>

{% include 'layouts/stuff.volt' %}
{% include 'layouts/accheader.volt' %}

{% block content %}
{% endblock %}

</body>
</html>