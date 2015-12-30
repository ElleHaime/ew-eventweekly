{% extends "layouts/base_new.volt" %}

{% block content %}
	<div class="page" >
		<div class="page__wrapper">
			<section id="content" class="container page-search" >
				<h1 class="page__title">This event has passed</h1>
			</section>
	
			<div class="page__wrapper_ajax_search"></div>
		</div>
	
		{% include 'layouts/accfilter_new.volt' %}
		<div class="clearfix"></div>
	</div>
	
{% endblock %}