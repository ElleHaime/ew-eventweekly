{% extends "layouts/base.volt" %}

{% block content %}
    <div class="container-fluid" id="container-box">
        <div class="agreement-box">
        <div class="row-fluid">
            <div class="span12">
                <h4>Change password</h4>
                    <form  class="form-horizontal" id="change-password-form" action="/profile/change-password" method="post">
                    <div class="control-group">
                        <label class="control-label" >Old password</label>
                        <div class="controls">
                            {{ form.render('old_password') }}
                            {{ form.messages('old_password') }}
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" >New password</label>
                        <div class="controls">
                            {{ form.render('password') }}
                            {{ form.messages('password') }}
                        </div>
                    </div>
                    <div class="control-group">
                         <label class="control-label" >Confirm New password</label>
                        <div class="controls">
                            {{ form.render('conf_password') }}
                            {{ form.messages('conf_password') }}
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <input class="btn btn-block" type="submit" value="Save"/>
                        </div>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
{% endblock %}