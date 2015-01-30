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
                                    <label for="email" class="input-registration-label">Email</label>
                                </div>
                                <div class="controls" >
                                    <input type="text" id="email" name="email" class="input-registration-control">
                                </div>
                            </div>

                            <div class="control-group" >
                                <div class="input-labels" >
                                    <label for="password" class="input-registration-label">Password</label>
                                </div>
                                <div class="controls" >
                                    <input type="password" id="password" name="password" class="input-registration-control">
                                </div>
                            </div>

                            <div class="control-group" >
                                <div class="input-labels" >
                                    <label for="confirm_password" class="input-registration-label">Confirm password</label>
                                </div>
                                <div class="controls" >
                                    <input type="password" id="confirm_password" name="confirm_password" class="input-registration-control">
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


