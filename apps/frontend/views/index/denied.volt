{% extends "layouts/base.volt" %}

{% block content %}

	<div class="container content">
	    <div class="row-fluid top-about">
	        <div class="span4">
	           {{ image('/img/demo/img1.jpg', 'alt': 'Guard') }}
	                <div class="top-about-text">
	                    <h4>Never miss an event</h4>
	                </div>
	        </div>
	        <div class="span4">
	            {{ image('/img/demo/img2.jpg', 'alt': 'Grab') }}
	            <div class="top-about-text">
	                <h4>Get personalised listing</h4>
	            </div>
	
	        </div>
	        <div class="span4">
	            {{ image('/img/demo/img3.jpg', 'alt': 'Know') }}
	                <div class="top-about-text">
	                  <h4>See which of your friends are going</h4>
	                </div>
	        </div>
	    </div>

		<div class="row-fluid link-about">
	        <div class="span12">
	            <a href="#">about us</a>
	        </div>
	    </div>

	    <div class="row-fluid agreement-box">
	        <div class="span8 offset2">
				<h3 align="center" style="color:#cecece;;">It's not your territory, Johnny. Get out</h3><br>
	        </div>
	    </div>
	    <div class="row-fluid sign-box">
	        <div class="span2"><p>Sign in:</p></div>
	        <div class="span1"> <button class="btn">twitter </button></div>
	        <div class="span1">  <button class="btn">g+ </button></div>
	        <div class="span3"> <button class="btn" onclick="location.href='/login'">e-mail</button></div>
	        <div class="span1"><span>or</span></div>
	        <div class="span4"><button class="btn" onclick="location.href='/map'">Register later</button></div>
    	</div>
	</div>

{% endblock %}
