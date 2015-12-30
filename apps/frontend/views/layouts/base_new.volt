<!DOCTYPE html>
<html>
<head>
    <title>EventWeekly</title>
    <meta charset="utf-8"/>
    <meta name="keywords" content=""/>
    {% if seoMode is defined  %}
    	<meta name="google-site-verification" content="Rj42T-NtSwTkThqdAHd7cP2yG891RcCCviHpImE04GQ" />
    {% endif %}	
    <meta name="title" content="EventWeekly Personalised Whats On Event Listings"/>
    <meta name="description" content="Never miss an event again with our whats on event guide. Promoters list your event for free. Thousands of local and international events listed weekly."/>
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
	
	{% if seoMode is defined  %}
		<!-- Facebook Pixel Code -->
		<script>
		!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
		n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
		document,'script','//connect.facebook.net/en_US/fbevents.js');
		
		fbq('init', '495674447278343');
		fbq('track', "PageView");</script>
		<noscript><img height="1" width="1" style="display:none"
		src="https://www.facebook.com/tr?id=495674447278343&ev=PageView&noscript=1"
		/></noscript>
		<!-- End Facebook Pixel Code -->
	{% endif %}

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