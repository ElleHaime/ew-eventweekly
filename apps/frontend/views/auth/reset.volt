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
                 <h4 style="color: white; padding-top: 10px; padding-left: 110px;">Reset your password</h4>
                 <form class="form-horizontal" method="post">
                     <div class="control-group">
                         <label class="control-label" for="inputEmail">{{ form.label('password') }}</label>
                         <div class="controls">
                             {{ form.render('password') }}
                             {{ form.messages('password') }}
                         </div>
                     </div>

                     <div class="control-group">
                         <label class="control-label" for="inputEmail">{{ form.label('conf_password') }}</label>
                         <div class="controls">
                             {{ form.render('conf_password') }}
                             {{ form.messages('conf_password') }}
                         </div>
                     </div>
                     
                     <div class="control-group">
                      <div class="controls">
                       <button type="submit" class="btn">Reset password</button>
                      </div>
                     </div>
                </form>
         </div>
     </div>      
</div>  

{% endblock %}