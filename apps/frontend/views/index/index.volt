{% extends "layouts/base.volt" %}

{% block content %}

<div class="content-bg">
    <div class="big-top">
        <div class="square red">
            <span>Never<br/> miss<br/> an event!</span>
        </div>
        <div class="square white">
            <span>Get<br/> personalised<br/> listings</span>
        </div>
        <div class="square blue">
            <span>Invite<br/> your friends<br/> to events</span>
        </div>
    </div>

	{% if isMobile == 1 %}
    <div class="facebook-button facebook-button_small">
        <div class="container-box">
            <div class="text-label"><span>Login</span> <br/>through Facebook:</div>
            <div class="button">
                <a href="#" onclick="return false;" id="fb-login">facebook</a>
            </div>
            <div class="tip clear">
                <i class="fb-lock"></i>
                <p>We respect your privacy and will not post any information without your permission to your social network accounts. </p>
            </div>
        </div>
    </div>
    {% endif %}

    <div class="how-it-works container-box">
        <h2>How does it work ?</h2>

        <div class="column">
            <div class="number">1</div>
            <div class="text">
                <p>Login with facebook and personalise event listings. </p>
            </div>
        </div>
        <div class="column">
            <div class="number">2</div>
            <div class="text">
                <p>Find world wide and local events relevant to your tastes. </p>
            </div>
        </div>
        <div class="column">
            <div class="number">3</div>
            <div class="text">
                <p>Create events, post to facebook and invite al your friends. </p>
            </div>
        </div>
    </div>

	{% if isMobile == 0 %}
    <div class="facebook-button">
        <div class="container-box">
            <div class="text-label"><span>Login</span> <br/>with Facebook:</div>
            <div class="button clearfix">
                <a href="#"  class="btn-facebook" onclick="return false;" id="fb-login">facebook</a>
            </div>
            <div class="tip clear">
                <i class="fb-lock"></i>
                <p>We respect your privacy and will not post  information without your permission to your social network accounts. </p>
            </div>
        </div>
    </div>
    {% endif %}

    <footer>
        <div class="container-box">
            <h2>No Facebook? Ok, try:</h2>

            <div class="login-variants clearfix">
                <div class="line-box clearfix">
                    {#<div class="twitter icon"><a href="#" class="color-blue">twiiter</a></div>#}
                    {#<div class="gplus icon"><a href="#" class="color-red">google+</a></div>#}
                    <div class="link email"><a id="email-login" class="fb-login-popup" onclick="return false;" href="/login">e-mail</a></div>
                    <div class="label-or"><span>or</span></div>
                </div>
                <div class="line-box">

                    <div class="link register-later"><a href="/map">Register later</a></div>
                </div>
                {#<div class="link read-more"><a href="#">Read more about Event Weekly</a></div>#}
            </div>
            <div class="clear"></div>
        </div>
    </footer>
</div>

{% endblock %}
