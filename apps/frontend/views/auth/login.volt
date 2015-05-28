{% extends "layouts/base_new.volt" %}

{% block content %}
<div class="page">
    <div class="container-fluid" id="container-box">
        <div class="agreement-box ">
            <div class="row-fluid ">
                <div class="span4">
                <section id="content" class="container">
                    <h1 class="page__title">Register</h1>

					{% if form is defined %}
	                    <form class="form-horizontal" method="post" id="form_signup">
	
							<p>Use your facebook account:</p>
	                        <a href="#" onclick="return false;" id="fb-login" class="ew-button">Login with <i class="fa fa-facebook-square" ></i> </a>
	
	                        <p>Don't have an account? Create new via e-mail only:</p>
	
	
	                        <div id="signup-labels">
	
	                            <div class="control-group" >
	                                <div class="input-labels" >
	                                    <label for="inputEmail" class="input-registration-label">{{ form.label('email') }}</label>
	                                </div>
	                                <div class="controls" >
	                                    {{ form.render('email',{'class': 'input-registration-control'}) }}
                                		{{ form.messages('email') }}
	                                </div>
	                            </div>
	
	                            <div class="control-group" >
	                                <div class="input-labels" >
	                                    <label for="inputPassword" class="input-registration-label">{{ form.label('password') }}</label>
	                                </div>
	                                <div class="controls" >
		  								{{ form.render('password', {'class': 'input-registration-control'}) }}
		                                {{ form.messages('password') }}
	                                </div>
	                            </div>
	
	                            <div class="control-group" >
	                                <div class="input-labels" >
	                                    <div class="controls"><a href="/auth/restore">Forgot password?</a></div>
	                                </div>
	                            </div>
		                        
		                        <div class="control-group">
		                            <div class="controls">
		                                <button type="submit" class="btn btn-block" id="submit_signup"><i class="fa fa-sign-in"></i>Log In</button>
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