<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <title>EventWeekly</title>
    <meta charset="utf-8"/>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{ stylesheet_link('/css/bootstrap.css') }}
    {{ stylesheet_link('/css/style.css') }}
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>

    <script data-main="/js/config" src="/js/requirePlugins/require.js"></script>
</head>
<body>

<header>
<div class="container">
	<div class="col-xs-4 logo">
		<a href="/"><img src="../img/logo.png" alt=""> </a>
    </div>
    <div class="col-xs-4 logo2 text-center">
    	<img src="../img/logo_add.png" alt="">
    </div>
    <div class="col-xs-4 logo3 text-right">
    	<img src="../img/ireland.png" alt="">
    </div>
</div>
</header>

<section class="banner">
<div class="container">
	<div class="inner-banner">
		<div class="col-md-4 fst-ban clearfix">
			<figure>
			</figure>
			<figcaption class="banner-cap">
				<div class="fig-cont">
					<p>Never miss</p>
					<p>Motorsport Events</p>
				</div>
			</figcaption>

		</div>
		<div class="col-md-4 snd-ban clearfix">
			<figure>
			</figure>
			<figcaption class="banner-cap">
				<div class="fig-cont">
					<p>Personalise your</p>
					<p>Motorsport Event</p>
					<p>Listings</p>
				</div>
			</figcaption>

		</div>
		<div class="col-md-4 trd-ban clearfix">
			<figure>
			</figure>
			<figcaption class="banner-cap">
				<div class="fig-cont">
					<p>Invite your</p>
					<p>friends to</p>
					<p>Motorsport Events</p>
				</div>
			</figcaption>
		</div>
	</div>
</div>
</section>

<section class=" banner-bot">
	<div class="container">
		<div class="col-md-12 ban-bot-cont">
			<h3>How does it work?</h3>
			<ul class="list-inline how">
				<li class="col-md-4">
					<span class="list">1</span>
					<p>Login with facebook and personalise you Motorsport Events Listings.</p>
				</li>
				<li class="col-md-4">
					<span class="list">2</span>
					<p>Find Local Motorsport Events relevant to your tastes.</p>
				</li>
				<li class="col-md-4">
					<span class="list">1</span>
					<p>Create a Motorsport Event and invite friends..</p>
				</li>
			</ul>
		</div>
	</div>
</section>


<section class="fb-login">
<div class="container">
	<div class="col-ms-12">
		<div class="col-md-4 log">
		    <h4>Login</h4>
		    <p>with facebook </p>
		</div>
		<div class="col-md-5 fb">
		    <input type="button" class="btn-fb" value="facebook" id="fb-login">
		    <div class="fb-no">we respect your privacy and will not post information without you permission to your social network accounts.</div>
		</div>
	</div>
</div>
</section>

<footer>
<div class="container">
	<!--<h2>No Facebook? Ok, try:</h2>
	<div class="login-variants col-md-9 col-sm-9">
	    <div class="line-box clearfix">
            <div class="link email"><a href="#" onclick="return false;" class="fb-login-popup" id="email-login">e-mail</a></div>
             <div class="label-or"><span>or</span></div>
        </div>
        <div class="line-box">
            <div class="link register-later"><a href="#">Register later</a></div>
        </div>
    </div> -->
	<div class="col-md-3 col-sm-3 pull-right text-right foot-logo">
		<p>Powerd By</p>
		<a href="/"><img src="../img/logo-foot.png"></a>
	</div>            
</div>
</footer>

{% include 'layouts/stuff.volt' %}

</body>
</html>
