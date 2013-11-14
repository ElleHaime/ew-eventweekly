				<div class="span10">
	                <span  class="line"></span>
	                <div class="user-box active-box">
	                    <a style="cursor:pointer; padding-bottom:0px; margin-top:0px;" id="user-down-caret">
                    		<img id="user-down-logo"
                    			{% if member.logo != '' %}
                    				src="{{ member.logo }}" 
								{% else %}
                    				src ="/img/demo/user.jpg"
                    			{% endif %}
							>
                    		<span id="usr-down-name">{{ member.name }}</span><i class="caret"></i></a>

	                    <div class="user-down" id="user-down" style="display:none;">
	                        <div class="edit-btn">
	                            <button class="btn btn-block" onclick="location.href='/profile'"><span class="edit-icon"></span><span class="btn-text">profile</span></button>
	                            <button class="btn btn-block" onclick="location.href='/campaign/list'"><span class="edit-icon"></span><span class="btn-text">manage campaigns</span></button>
	                            <button class="btn btn-block" onclick="location.href='/event/list'"><span class="edit-icon"></span><span class="btn-text">manage events</span></button>
	                        </div>

	                        <div class="btn-list">
	                            <button class="btn btn-block"><span class="btn-count">21</span><span class="btn-text">Recommended for you</span></button>
	                            <button class="btn btn-block"><span class="btn-count">12</span><span class="btn-text">Shared by your friends</span></button>
	                        </div>
	                        
	                        <div class="edit-btn">
								<button class="btn btn-block" onclick="location.href='/logout'"><span class="edit-icon"></span><span class="btn-text">logout</span></button>	                            
	                        </div>

	                    </div>
	                </div>
	                <button class="btn btn-add" onclick="location.href='/event/add'"> <span class="icon-plus-sign"></span>Add Event</button>
	                <span  class="line"></span>
	                <div class="location clearfix">
	                    <span class="location-count">0</span>

	                  <div class="location-place">
	                      <a href="#" class="location-city">
	                          <i class="caret"></i>
	                          <span>{{ member.location.name }}</span>
	                      </a>
	                      <div class="location-search clearfix">
	                          <div class="input-append">
	                              <input class=" input-large"  size="16" type="text" placeholder="Event search engine">
	                              <button class="btn" type="button">Find</button>
	                          </div>
	                      </div>
	                  </div>
	                  <div class="location-place">
	                      <a href="#">
	                          <span>What are you looking for?</span>
	                      </a>
	                      <div class="location-search clearfix">
	                          <div class="input-append">
	                              <input class=" input-large"  size="16" type="text" placeholder="Search city">
	                              <button class="btn" type="button">Find</button>
	                          </div>
	                      </div>

	                  </div>

	                </div>
	                <span  class="line"></span>
	                <div class="show-box">
	                    <button class="btn btn-show" onclick="location.href='/event/events'"><i class=" icon-list"></i><span>Show as list </span></button>
	                </div>

	            </div>