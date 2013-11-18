				    <div class="span4 user-block">
                        <span  class="line"></span>
                        <div class="user-box">
                            <a id="user-down-caret">{{ image('img/demo/user.jpg') }}
                                    <span>{{ member.name }}</span><i class="caret"></i></a>

                            <div class="user-down" id="user-down" style="display:none;">
                                <div class="edit-btn">
                                    <button class="btn btn-block" onclick="location.href='/profile'"><span class="edit-icon"></span><span class="btn-text">edit profile</span></button>
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
                          <button class="btn btn-add" onclick="location.href='/event/add'"> <span class="icon-plus-sign"></span><span class="text-btn">Add Event</span></button>
                          <span  class="line"></span>
	                </div>
	                <div class="span4 location-box">

                        <div class="location clearfix">
                            <span class="location-count" id="events_count">
                              {% if eventsTotal is defined %}
                                {{ eventsTotal }}
                              {% else %}
                                0
                              {% endif %}
                            </span>

                          <div class="location-place">
                              <a href="#" class="location-city">
                                  <i class="caret"></i>
                                  <span>{{ location.alias }}</span>
                              </a>
                              <div class="location-search clearfix">
                                  <div class="input-append">
                                      <input class=" input-large"  size="16" type="text" placeholder="Event search engine">
                                      <button class="btn" type="button">Find</button>
                                  </div>
                              </div>
                          </div>
                          <div class="location-place location-place_ask">
                              <a href="#">
                              <i class="caret"></i>
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
	                </div>
	                
	                <div class="span2 show-list">

                        <div class="show-box">
                            <button class="btn btn-show" onclick="location.href='/list'"><i class=" icon-list"></i><span>Show as list </span></button>
                        </div>

	                 </div>