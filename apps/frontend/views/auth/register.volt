{% extends "layouts/base.volt" %}

{% block content %}
    <div class="container-fluid content">
        <div class="agreement-box clearfix">
            <div class="row-fluid ">
                <div class="span12">
                    <h4>Registration</h4>
                    <form class="form-horizontal" method="post">
                        <div class="control-group">
                            <label class="control-label" for="inputEmail">{{ form.label('email') }}</label>
                            <div class="controls">
                                {{ form.render('email') }}
                                {{ form.messages('email') }}
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="inputPassword">{{ form.label('password') }}</label>
                            <div class="controls">
                                {{ form.render('password') }}
                                {{ form.messages('password') }}
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" class="btn btn-block">Sign in</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

{% endblock %}