{% extends "layouts/base_new.volt" %}

{% block content %}

<div class="page">
    <div class="container-fluid" id="container-box">
        <div class="agreement-box ">
            <div class="row-fluid ">
                <div class="span4">
                <section id="content" class="container">
                    <h1 class="page__title">Reset your password</h1>

					{% if form is defined %}
	                    <!-- form class="form-horizontal" method="post" id="reset-password-form" -->
	                    <form class="form-horizontal" method="post" id="form_signup">
	
	                        <div id="signup-labels">
	
	                            <div class="control-group" >
	                                <div class="input-labels" >
	                                    <label for="password" class="input-registration-label">{{ form.label('password') }}</label>
	                                </div>
	                                <div class="controls" >
	                                    {{ form.render('password') }}
	                                    {{ form.messages('password') }}
	                                </div>
	                            </div>
	
	                            <div class="control-group" >
	                                <div class="input-labels" >
	                                    <label for="confirm_password" class="input-registration-label">{{ form.label('conf_password') }}</label>
	                                </div>
	                                <div class="controls" >
	                                    {{ form.render('conf_password') }}
                                		{{ form.messages('conf_password') }}
	                                </div>
	                            </div>
	
		                        <div class="control-group">
		                            <div class="controls">
		                                <button type="submit" class="btn btn-block" id="submit_signup"><i class="fa fa-sign-in"></i>Reset</button>
		                            </div>
		                        </div>
								                    
	                    </form>
	                {% endif %}
                </section>
                </div>
            </div>
        </div>
    </div>
</div>
    
{% endblock %}    