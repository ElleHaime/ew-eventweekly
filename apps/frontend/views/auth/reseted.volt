{% extends "layouts/base.volt" %}

{% block content %}

<div class="container content">
		<div class="row-fluid top-about">
	        <div class="span4">
	           {{ image('img/demo/img1.jpg', 'alt': 'Guard') }}
	                <div class="top-about-text">
	                    <h4>Never miss an Event</h4>
	                </div>
	        </div>
	        <div class="span4">
	            {{ image('img/demo/img2.jpg', 'alt': 'Grab') }}
	            <div class="top-about-text">
	                <h4>Get personalised listing</h4>
	            </div>
	
	        </div>
	        <div class="span4">
	            {{ image('img/demo/img3.jpg', 'alt': 'Know') }}
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
             <div class="span6 offset2">
                 <h4 style="color: white; padding-top: 10px; padding-left: 110px;">Please check your email</h3>
	        </div>
	    </div>      
</div> 	

{% endblock %}