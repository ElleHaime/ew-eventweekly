{% extends "layouts/base.volt" %}

{% block content %}

<div class="content-bg">
    <div class="big-top">
        <div class="square red">
            <span>Never<br/> miss<br/> an Event!</span>
        </div>
        <div class="square white">
            <span>Get<br/> personalised<br/> listings</span>
        </div>
        <div class="square blue">
            <span>See which<br/> of your friends<br/> are going</span>
        </div>
    </div>
    <div class="how-it-works container-box">
        <h2>How it works?</h2>

        <div class="column">
            <div class="number">1</div>
            <div class="text">
                <p>Войдите через Фейсбук, чтоб использовать наш сервис на полную. </p>
            </div>
        </div>
        <div class="column">
            <div class="number">2</div>
            <div class="text">
                <p>Посмотрите интереснейшие события в вашем городе. </p>
            </div>
        </div>
        <div class="column">
            <div class="number">3</div>
            <div class="text">
                <p>Запланируйте свое событие, пригласив на него более 100 000 пользователей по всему миру </p>
            </div>
        </div>
    </div>
    <div class="facebook-button">
        <div class="container-box">
            <div class="button">
                <a href="#" onclick="return false;" id="fb-login">facebook</a>
            </div>
            <div class="tip clear">
                <i class="fb-lock"></i>

                <p>Мы серьзено относимся к вопросам приватности — личные события останутся личными, пока вы напрямую
                    не включите их. Также мы ничего не запостим на вашу стену. </p>
            </div>
        </div>
    </div>
    <footer>
        <div class="container-box">
            <h2>No Facebook? Ok, try:</h2>

            <div class="login-variants clearfix">
                <div class="line-box clearfix">
                    <div class="twitter icon"><a href="#" class="color-blue">twiiter</a></div>
                    <div class="gplus icon"><a href="#" class="color-red">google+</a></div>
                    <div class="link email"><a href="/login">e-mail</a></div>
                </div>
                <div class="line-box">
                    <div class="label-or"><span>or</span></div>
                    <div class="link register-later"><a href="/map">Register later</a></div>
                </div>
                <div class="link read-more"><a href="#">Read more about Event Weekly</a></div>
            </div>
            <div class="clear"></div>
        </div>
    </footer>
</div>

{% endblock %}
