<!DOCTYPE html>
<html lang="en">
<head>
    <title>EventWeekly</title>
	<meta charset="utf-8"/>
	<meta name="title" content="Free Event Listings with EventWeekly.com"/>
    <meta name="description" content="List your events for free. If your event is all ready listed on Facebook then will automatically list it. Register to day and get listed. http://eventweekly.com/freelisting"/>

	<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
    
    <link type="image/ico" href="/img/128.ico" rel="icon">

    {{ stylesheet_link('/freelisting-css/css/style.css') }}
    {{ stylesheet_link('/freelisting-css/css/restore.css') }}    

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=latin,cyrillic-ext,cyrillic' rel='stylesheet'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,300italic,700' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Rokkitt:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    
    <script data-main="/js/config" src="/js/requirePlugins/require.js"></script>
</head>
<body>

	{% include 'layouts/stuff.volt' %}
	
    <div class="container">
        <div class="free-listing-page">
            <div class="free-listing-page-log-in">
                <a class="free-listing-page-log-in-link" id="fb-login" href="#">
                    Under maintenance.<br> Will be back shortly
                </a>
            </div>
            <img src="/freelisting-css/img/Smile.png"
                 alt=""
                 class="free-listing-page-img">
            <p class="free-listing-page-text">
                That's it, we do the rest, <br>
                all your Facebook events will appear on
            </p>

            <div class="free-listing-page-title-block">
                <a class="free-listing-page-title-block-link" href="/">
                    <span class="free-listing-page-title-block-link-bold">Event</span>Weekly
                </a>
            </div>


        </div>
    </div>
</body>
</html>