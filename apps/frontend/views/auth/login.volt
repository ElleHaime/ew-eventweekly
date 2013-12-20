{% extends "layouts/base.volt" %}

{% block content %}

    <div class="container-fluid" id="container-box">
        <div class="row-fluid">
            <div class="span12">

                <div class="agreement-box clearfix">
                    <form class="form-horizontal" method="post">

                        <h4>Log in &nbsp;&nbsp;- or -&nbsp;&nbsp; <a href="/signup">Register</a></h4>

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
                                <button type="submit" class="btn btn-block">Log in</button>
                            </div>
                            <div class="controls"><a href="restore">Forgot password?</a></div>
                        </div>
                    </form>

                </div>
            </div>
         </div>
    </div>

{% endblock %}