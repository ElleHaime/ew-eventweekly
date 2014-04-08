{% extends "layouts/base.volt" %}

{% block content %}

   <div class="content-bg">
		<div class="facebook-button" align="center">
			<img src="img/motorsport_events_ireland.png">
		</div>
   	
   		<div class="facebook-button">
	        <div class="container-box">
	            <div class="text-label"><span>Login</span> <br/>with Facebook:</div>
	            <div class="button clearfix">
	                <a href="#"  class="btn-facebook" onclick="return false;" id="fb-login">facebook</a>
	            </div>
	            <div class="tip clear">
	                <i class="fb-lock"></i>
	                <p>We respect your privacy and will not post  information without your permission to your social network accounts. </p>
	            </div>
	        </div>
    	</div>
   </div>

{% endblock %}