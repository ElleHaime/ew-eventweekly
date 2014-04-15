/**
 * Created by slav on 1/27/14.
 */
define('listListener' ,['jquery','noty','SingleEvent','utils','domReady','underscore'],
	function($, noty, SingleEvent) {

	    function listListener(options) {
	
	    	var self = this;
	        var settings = {
	        	eventsBlock: '#elementsBlockDiv',
	            //eventsUrl: '/event/test-get',
	            eventsUrl: '/event/get-list',
	            requestInterval: 5000
	        };
	        var interval = null;
	        settings = _.extend(settings, options);
	
	        
	        self.init = function()
	        {
	        	makeRequest();
	        	
	        	if (settings.requestInterval > 0) {
                    interval = setInterval(function(){
                        makeRequest();
                    }, settings.requestInterval);
                } 
	        }
	        
	        var request = function() {
	            return $.ajax({
	                url: settings.eventsUrl,
	                type: 'GET',
	                dataType: 'json'
	            });
	        };
	
	        var responseHandler = function(data) {
console.log(data);	        	
	            if (data.status == true && !_.isUndefined(data.events)) {
	                var eventsBlock = $(settings.eventsBlock);
	                
	                $.each(data.events, function(index,event) {
	                	newEventHtml = composeElement(event);
	                    //var newEventHtml = new SingleEvent(event);
	                    //newEventHtml = newEventHtml.getHtml();
//console.log(newEventHtml);	                	
	                    eventsBlock.append(newEventHtml);
	                });
	                //$('.tooltip-text').tooltip(); 
	            }
	            
	            if (data.stop == true) {
	                clearInterval(interval);
	            }
	        };
	        
	        var makeRequest = function() {
	            $.when(request()).then(function(response) {
	                responseHandler(response);
	            });
	        };
	        
	        var composeElement = function(elem)
	        {
	        	// header
	        	result = '<div class="events-list ' + elem.id + '-category signleEventListElement" event-id="' + elem.id + '">' +
	        				'<div class="row-fluid "><div class="span12"><div class="event-one clearfix">'
	        	+ '</div></div></div></div>';
	        	
	        	return result;
	        	
	        }
	    }
	    
	    return new listListener($, noty, SingleEvent);  
});
