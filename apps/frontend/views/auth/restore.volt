{% extends "layouts/base.volt" %}

{% block content %}

    <div class="container-fluid content">

        <div class=" agreement-box clearfix">
            <div class="row-fluid">
                <div class="span12">

                    <h4>Restore password</h4>
                    <form class="form-horizontal" method="post" id="restore-password-form">
                        <div class="control-group">
                            <label class="control-label" for="inputEmail">{{ form.label('email') }}</label>
                            <div class="controls">
                                {{ form.render('email') }}
                                {{ form.messages('email') }}
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" class="btn btn-block">Restore</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

{% endblock %}
