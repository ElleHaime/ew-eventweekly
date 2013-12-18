{% extends "layouts/base.volt" %}

{% block content %}
    <div class="container" id="container-box">
        <div class="agreement-box">
            <div class="row-fluid ">
                <div class="span6 offset1">
                    <h4>Registration</h4>

                    <form class="form-horizontal" method="post" id="form_signup">
                        <div class="control-group">
                            <label class="control-label" for="inputEmail">{{ form.label('email') }}</label>

                            <div class="controls">
                                {{ form.render('email') }}
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="inputPassword">{{ form.label('password') }}</label>

                            <div class="controls">
                                {{ form.render('password') }}
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="confirmPassword">
                                <label for="confirmPassword">{{ form.label('confirm_password') }}</label>
                            </label>

                            <div class="controls">
                                {{ form.render('confirm_password') }}
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="button" class="btn" id="submit_signup">Sign Up</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

{% endblock %}

