{% extends "layouts/base.volt" %}

{% block content %}

    <div class="container" id="container-box">
        <div class="agreement-box">
            <div class="row-fluid ">
                <div class="span6 offset1">
                    <h4>Log in &nbsp;&nbsp;- or -&nbsp;&nbsp; <a href="/signup">Register</a></h4>

                    <form class="form-horizontal" method="post">
                        <div class="control-group">
                            <label class="control-label" for="inputEmail">{{ form.label('email') }}</label>

                            <div class="controls">
                                {{ form.render('email',{'class': 'input-large'}) }}
                                {{ form.messages('email') }}
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="inputPassword">{{ form.label('password') }}</label>

                            <div class="controls">
                                {{ form.render('password', {'class': 'input-large'}) }}
                                {{ form.messages('password') }}
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" class="btn">Log in</button>
                            </div>
                            <div class="controls"><a href="restore">Forgot password?</a></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

{% endblock %}