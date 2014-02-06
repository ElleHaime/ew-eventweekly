<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <title>EventWeekly</title>
    <meta charset="utf-8"/>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{ stylesheet_link('/css/bootstrap.min.css') }}
    {{ stylesheet_link('/css/noti.css') }}
    {{ stylesheet_link('/css/older.css') }}
    <!--{{ stylesheet_link('/css/responsive.css') }}-->
    {#{{ stylesheet_link('/css/responsive-new_login.css') }}#}
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>

    <script data-main="/js/config" src="/js/requirePlugins/require.js"></script>
</head>
<body>

<div class=" container wrapper">
    <div class="row-fluid">
        <div class="span12">
            <div class="form-horizontal">
                <div class="logo-box">
                    <img src="/img/demo/logo.png" alt="">
                </div>

                <div class="notiBlock">
                    <div class="container-fluid">
                        <div class="row-fluid">
                            <div class="span12">
                                <span id="notiText">Warning message</span>
                                <span class="notiBtnArea"></span>
                                <a href="#" class="  icon-remove notiHide"></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span12">
                        <div class="form-box">
                            <div class="social-network clearfix">
                                <a href="#" onclick="return false;" id="fb-login" class="facebook-link"></a>
                                {#<a href="#" class="twitter-link"></a>
                                <a href="#" class="google-link"></a>#}
                            </div>
                            <h4>
                                <span>Login</span>
                                or
                                <span>register</span>
                            </h4>
                            <hr>
                            <div class="account-info">
                                <h5> Don't have an account with any of the above?<br/>
                                    <a href="/signup" id="createAcc">Create new account using e-mail only</a>
                                </h5>
                                <hr/>
                                <h5>Log into your existing account with e-mail:</h5>
                                <div class="control-group">
                                    {#<input type="text" id="inputEmail" placeholder="Email">#}
                                    {{ form.render('email') }}
                                </div>
                                <div class="control-group">
                                    {{ form.render('password') }}
                                    <div class="controls-btn">
                                        <a href="/restore" id="restorePass"> restore password</a>
                                        <button id="loginBtn" type="submit" class="btn">Login</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
{% include 'layouts/stuff.volt' %}
</body>
</html>