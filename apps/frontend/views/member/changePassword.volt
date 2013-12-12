{% extends "layouts/base.volt" %}

{% block content %}
    <div class="padd_70"></div>
    <div class="container content_noBorder">
        <div class="row-fluid profile-top">
            <div class="span12">
                <h2>Change password:</h2>

                <form action="/profile/change-password" method="post">
                    <label for="">Old password</label>
                    {{ form.render('old_password') }}
                    {{ form.messages('old_password') }}

                    <label for="">New password</label>
                    {{ form.render('password') }}
                    {{ form.messages('password') }}

                    <label for="">Confirm New password</label>
                    {{ form.render('conf_password') }}
                    {{ form.messages('conf_password') }}

                    <input type="submit" value="Save"/>
                </form>
            </div>
        </div>
    <div>
{% endblock %}