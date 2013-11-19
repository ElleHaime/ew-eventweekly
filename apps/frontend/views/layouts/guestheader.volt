					<div class="span4 sign_no">
	                    <span  class="line"></span>
	                    <div class="user-box">
	                       <a href="signup" style=" margin-top:2px;"><span>Sign Up</span></a>
	                    </div>

						<span  class="line"></span>	 
                    </div>
                    <div class="span4  location-box">
						<div class="location clearfix">
							<span class="location-count location-count_no">?</span>

		                    <div class="location-place">
		                        <a href="#" class="location-city">
		                            <span id="location">
		                            	{{ location.alias }}
		                           </span>
		                           <div id="results" hidden="hidden">
							          <ul data-role="listview" id="locations" data-inset="true">
							              <li data-role="list-divider" role="heading">Select one:</li>
							          </ul>
							      </div>
		                        </a>
		                    </div>
	                    	<div class="location-place active-box">
	                          	<a href="#" style="padding-bottom:10px;">
	                              	<span>What are you looking for?</span>
	                          	</a>
	                      	</div>
                    	</div>
                    	<span  class="line"></span>
                    </div>
                    <div class="span2">

	                    <div class="show-box">
	                    	{% if view_action is defined %}
	                        			{%if view_action == 'list' %}
											<button class="btn btn-block btn-show" onclick="location.href='map'"><i class=" icon-list"></i><span>Show as map</span></button>
										{% else %}
											<button class="btn btn-block btn-show" onclick="location.href='list'"><i class=" icon-list"></i><span>Show as list</span></button>
										{% endif %}
							{% else %}	
								<button class="btn btn-block btn-show" onclick="location.href='list'"><i class=" icon-list"></i><span>Show as list</span></button>
							{% endif %}
	                        	</span> 
	                       	</button>
	                    </div>

					</div>