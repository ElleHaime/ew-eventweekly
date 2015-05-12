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

    {{ stylesheet_link('/_new-layout-eventweekly/css/style.css') }}
    {{ stylesheet_link('/_new-layout-eventweekly/libs/idangerous.swiper/idangerous.swiper.min.css') }}
    {{ stylesheet_link('/_new-layout-eventweekly/libs/bootstrap/bootstrap.css') }}
    {{ stylesheet_link('/_new-layout-eventweekly/libs/bootstrap/bootstrap-theme.css') }}

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>

    <script data-main="/js/config" src="/js/requirePlugins/require.js"></script>
</head>

<body>

    <div class="page">
        <div class="container-fluid" id="container-box">
            <div class="agreement-box ">
                <div class="row-fluid ">
                    <div class="span4">
                        <section id="content" class="container">
                            <h1 class="page__title">Login</h1>
                                <div class="form-horizontal" method="post" id="form_signup">

                                    <p>Use your facebook account:</p>
                                    <a href="#" onclick="return false;" id="fb-login" class="ew-button">Login with <i class="fa fa-facebook-square" ></i> </a>

                                    <p>Don't have an account? Create new via e-mail only:</p>
                                    <a href="/auth/signup" class="ew-button" id="createAcc">
                                        <i class="fa fa-sign-in"></i> Sign Up
                                    </a>
                                
                                    <p>Log into your existing account with e-mail:</p>

                                    <div class="control-group">
                                        <div class="input-labels" >
                                            <label for="email" class="input-registration-label">Email</label>
                                        </div>
                                        <div class="controls" >
                                            <input type="text" id="email" name="email" class="input-registration-control">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="input-labels" >
                                            <label for="password" class="input-registration-label">Password</label>
                                        </div>
                                        <div class="controls" >
                                            <input type="password" id="password" name="password" class="input-registration-control">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="controls">
                                            <a id="loginBtn" type="button" class="ew-button"><i class="fa fa-smile-o"></i> Log in</a>
                                        </div>
                                    </div>
                                
                                    <a style="cursor:pointer;" id="restorePass"> restore password</a>

                                </div>
                                
                            
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>


{% include 'layouts/stuff.volt' %}
</body>
</html>