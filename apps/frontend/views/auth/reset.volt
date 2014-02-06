{% extends "layouts/base.volt" %}

{% block content %}

    <div class="container-fluid content">

        <div class=" agreement-box clearfix">
            <div class="row-fluid">
                <div class="span12">

                    <h4>Reset your password</h4>
                    {% if form is defined %}
                    <form class="form-horizontal" method="post" id="reset-password-form">
                        <div class="control-group">
                            <label class="control-label" for="inputEmail">{{ form.label('password') }}</label>
                            <div class="controls">
                                {{ form.render('password') }}
                                {{ form.messages('password') }}
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="inputEmail">{{ form.label('conf_password') }}</label>
                            <div class="controls">
                                {{ form.render('conf_password') }}
                                {{ form.messages('conf_password') }}
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" class="btn btn-block">Reset password</button>
                            </div>
                        </div>
                    </form>
                    {% endif %}
                </div>
            </div>
        </div>
    </div>

{% endblock %}
