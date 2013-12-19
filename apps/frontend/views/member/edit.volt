{% extends "layouts/base.volt" %}

{% block content %}
    <div class="container-fluid" id="container-box">
        <div class="row-fluid">
            <div class="span12">
                <div class="agreement-box">
                    {#{{ form() }}#}
                    <form enctype="multipart/form-data" method="post" class="form-horizontal">
                        <h4>Edit Profile</h4>

                        <div class="control-group">
                            <label class="control-label" for="inputEmail">{{ form.label('extra_email') }}</label>

                            <div class="controls">
                                {{ form.render('extra_email') }}
                                {{ form.messages('extra_email') }}
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="inputPassword">{{ form.label('name') }}</label>

                            <div class="controls">
                                {{ form.render('name') }}
                                {{ form.messages('name') }}
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="inputPassword">{{ form.label('address') }}</label>

                            <div class="controls">
                                {{ form.render('address') }}
                                {{ form.messages('address') }}
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="inputPassword">{{ form.label('phone') }}</label>

                            <div class="controls">
                                {{ form.render('phone') }}
                                {{ form.messages('phone') }}
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="inputPassword">{{ form.label('logo') }}</label>
                            {{ form.render('logo') }}

                            <div class="controls">
                                <button style="text-align: center; overflow: hidden; height: 42px; width: 227px;" class="btn btn-block btn-file"
                                        id="add-img-btn" type="button">Add Image</button>
                                {{ form.messages('logo') }}
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                {{ form.render('Save') }}
                            </div>
                        </div>
                    </form>
                    {{ endform() }}
                </div>
            </div>
        </div>
    </div>

{% endblock %}