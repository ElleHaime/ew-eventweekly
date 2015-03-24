{% extends "layouts/base_new.volt" %}

{% block content %}
    <div class="page">
    <div class="container-fluid" id="container-box">
        <div class="agreement-box ">
            <div class="row-fluid ">
                <div class="span4">
                <section id="content" class="container">
                <h1 class="page__title">Change password</h1>
                    <form  class="form-horizontal" id="change-password-form" action="/profile/change-password" method="post">


                    <div class="control-group">
                        <div class="input-labels" >
                            <label for="old_password" class="input-registration-label" >Old password</label>
                        </div>
                        <div class="controls">
                            <input type="password" id="old_password" name="old_password" class="input-registration-control">     
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="input-labels" >
                            <label for="password" class="input-registration-label" >New password</label>
                        </div>
                        <div class="controls">
                            <input type="password" id="password" name="password" class="input-registration-control">               
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <div class="input-labels">
                            <label for="conf_password" class="input-registration-label" >Confirm New password</label>
                        </div>
                        <div class="controls">
                            <input type="password" id="conf_password" name="conf_password" class="input-registration-control"/>                                  
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="controls">
                            <button type="submit" class="btn btn-block" id="submit_signup"><i class="fa fa-save"></i>Save</button>
                        </div>
                    </div>
                    </form>
                </section>
                </div>
            </div>
        </div>
    </div>
    </div>
{% endblock %}