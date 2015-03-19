{% extends "layouts/base_new.volt" %}

{% block content %}

<div class="page">
    <section id="content" class="container">
        

        <div class="container-fluid content">

            <div class=" agreement-box clearfix">
                <div class="row-fluid">
                    <div class="span12">


                        <h1 class="page__title">Restore password</h1>

                        <form class="form-horizontal" id="form_restore" method="post" >
                            <div class="control-group input-labels">
                                <label for="email" class='input-registration-label' id="restore_password_label">Enter your email, please</label>
                                <div class="controls">
                                    <input type="text" id="email" name="email" class="input-registration-control">
                                    {{ form.messages('email') }}
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="controls">
                                    <button type="submit" class="ew-button"><i class="fa fa-reply-all"></i>Restore</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
 

    </section>
</div>


{% endblock %}
