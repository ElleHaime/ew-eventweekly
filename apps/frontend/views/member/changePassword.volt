{% extends "layouts/base.volt" %}

{% block content %}
    <div class="container-fluid" id="container-box">
        <div class="row-fluid">
            <div class="span12">
                <div class="agreement-box">

                    <form action="/profile/change-password" method="post">
                        <h4>Change password:</h4>

                        <label for="">Old password</label>
                        {{ form.render('old_password') }}
                        {{ form.messages('old_password') }}

                        <label for="">New password</label>
                        {{ form.render('password') }}
                        {{ form.messages('password') }}

                        <label for="">Confirm New password</label>
                        {{ form.render('conf_password') }}
                        {{ form.messages('conf_password') }}

                        <div class="control-group">
                            <div class="controls">
                                <input class="btn" type="submit" value="Save"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
{% endblock %}