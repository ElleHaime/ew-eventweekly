<!DOCTYPE html>
<html>
<head>
    <title>EventWeekly</title>
    <meta charset="utf-8"/>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
	<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">  

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
		{% if urlParams is defined %}
			<script type="text/javascript">
				window.searchUrlParams = "{{ urlParams }}";
    		</script>
    	{% endif %}
	{% endif %}

    {{ stylesheet_link('/css/normalBootstrapDateTimepicker.min.css') }}

    {{ stylesheet_link('/_new-layout-eventweekly/css/style.css') }}
    {{ stylesheet_link('/_new-layout-eventweekly/libs/idangerous.swiper/idangerous.swiper.min.css') }}
    {{ stylesheet_link('/_new-layout-eventweekly/libs/bootstrap/bootstrap.css') }}
    {{ stylesheet_link('/_new-layout-eventweekly/libs/bootstrap/bootstrap-theme.css') }}

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>

    <script data-main="/js/config" src="/js/requirePlugins/require.js"></script>
</head>

<body>
{% if eventPreview is defined %}
    <div class="preview_overlay" style="height: 100%;width: 100%;z-index: 10000;top:0;left:0;position:fixed;"></div>
{% endif %}

{% include 'layouts/stuff.volt' %}
{% include 'layouts/accheader_new.volt' %}

{% block content %}
{% endblock %}

{% include 'layouts/accfooter_new.volt' %}

</body>





</html>