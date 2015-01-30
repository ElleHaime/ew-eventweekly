{% extends "layouts/base_new.volt" %}

{% block content %}
    <div class="page">
    <div class="container-fluid" id="container-box">
        <div class="agreement-box ">
            <div class="row-fluid ">
                <div class="span4">
                <section id="content" class="container">
                    <h1 class="page__title">Registration</h1>

                    <form class="form-horizontal" method="post" id="form_signup">
                    

                        <div id="signup-labels">

                            <div class="control-group" >
                                <div class="input-labels" >
                                    {{ form.label('email', {'class':'input-registration-label'}) }}
                                </div>
                                <div class="controls" >
                                    {{ form.render('email', {'class':'input-registration-control'}) }}
                                </div>
                            </div>
                            







                            <div class="control-group">
                                <label class="control-label" for="inputPassword">{{ form.label('password', {'class':'input-registration-label'}) }}</label>

                                <div class="controls">
                                    {{ form.render('password', {'class':'input-registration-control'}) }}
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="confirmPassword">
                                    <label for="confirmPassword">{{ form.label('confirm_password', {'class':'input-registration-label'}) }}</label>
                                </label>

                                <div class="controls">
                                    {{ form.render('confirm_password', {'class':'input-registration-control'}) }}
                                </div>
                            </div>
                        </div>



                        <div class="control-group">
                            <div class="controls">
                                <button type="button" class="btn btn-block" id="submit_signup">Sign Up</button>
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


