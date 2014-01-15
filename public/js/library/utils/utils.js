define('utils',
	['jquery', 'domReady'],
	function($) {
		function utils($) {
	 
			var self = this;
			

			self.addressAutocomplete = function(selector, type, lat, lng)
			{
				if (selector == '' || selector == undefined) {
			        return null;
			    }

                var typess = [];

                if (type == '' || type == undefined) {
                    typess.push('(cities)');
                }else {
                    typess.push(type);
                }

			    var input = selector, options = {types: typess};

			    if (lat != '' && lat != undefined && lng != '' && lng != undefined) {
                    var boundsOptions = {
			            bounds: new google.maps.LatLngBounds(new google.maps.LatLng(lat, lng),
			            									 new google.maps.LatLng(lat, lng))
			        };
                    options = $.extend(options, boundsOptions);
			    }
                console.log(options);
			    var autocomplete = new google.maps.places.Autocomplete(input, options);

			    return autocomplete;
			},
				
			self.ucfirst = function(str) 
			{
				return str.slice(0, 1).toUpperCase() + str.slice(1);
			},

			self.request = function(method, url, params, dataType)
			{

				if (typeof method == 'undefined') {
					method = 'post';
				}
				var call = { url: url, type: method };
				
				if (method == 'get') {
					if (typeof dataType == 'undefined') {
						dataType = 'json';
					}
					call.dataType = dataType;
				}

				if (params) {
					call.data = params;
				}

				return $.ajax(call);
			},
				
			self.isDomElementEmpty = function(elem)
			{

			},


			self.addEmptyOptionFirst = function(selectElm, optName, optVal)
			{
				if (typeof optVal == 'undefined') {
					optVal = '';
				}
				selectElm.prepend('<option value="' + optVal + '" selected="selected">' + optName + '</option>');
			},

			self.daysDifference = function(daysNum)
			{
				var result = '';

				if (daysNum > 30) {
		            var count = Math.round(daysNum/30),
                        result = 'Event happens more than in ' + count + ' month';
		            if (count > 1) {
		            	result += 's';
		            }

		        } else if (daysNum > 7) {
		            var count = Math.round(daysNum/7),
		                result = 'Event happens more than in ' + count + ' week';
		            if (count > 1) result += 's';

		        } else if (daysNum > 1) {
		            result = 'Event happens in ' + daysNum + ' days';

		        } else if (daysNum == 1) {
					result = 'Event happens tomorrow';

				} else if (daysNum == 0) {
		        	result = 'Event happens today';
		        }

		        return result;
			},


			self.dateFormat = function(format, timestamp) 
			{
				 function pad(number) {
					 return number.toString().replace(/^([0-9])$/, '0$1');
				 }
		
				 if (typeof timestamp == 'Date') {
					 var date = timestamp;
				 } else {
					 if (typeof timestamp == 'string' || typeof timestamp == 'object') {
						 var date = new Date(timestamp);
					 } else {
						 var date = new Date();
					 }
				 }

				 if (format === null) {
				 	return date;
				 }

				 var hours = date.getHours(),
				 	day = date.getDay(),
				 	dayOfMonth = date.getDate(),
				 	month = date.getMonth(),
				 	fullYear = date.getFullYear(),
				 	weekdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
				 	months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		
				 //list all format keys
				 var replacements = {
						 
					//day
					'a': weekdays[day].substr(0, 3), // Short weekday, like 'Mon'
					'A': weekdays[day], // Long weekday, like 'Monday'
					'd': pad(dayOfMonth), // Two digit day of the month, 01 to 31 
					'e': dayOfMonth, // Day of the month, 1 through 31 
		    
				    // Month
				    'b': months[month].substr(0, 3), // Short month, like 'Jan'
				    'B': months[month], // Long month, like 'January'
				    'm': pad(month + 1), // Two digit month number, 01 through 12
		    
				    // Year
				    'y': fullYear.toString().substr(2, 2), // Two digits year, like 09 for 2009
				    'Y': fullYear, // Four digits year, like 2009
				    
				    // Time
				    'H': pad(hours), // Two digits hours in 24h format, 00 through 23
				    'I': pad((hours % 12) || 12), // Two digits hours in 12h format, 00 through 11
				    'l': (hours % 12) || 12, // Hours in 12h format, 1 through 11
				    'M': pad(date.getMinutes()), // Two digits minutes, 00 through 59
				    'p': hours < 12 ? 'AM' : 'PM', // Upper case AM or PM
				    'P': hours < 12 ? 'am' : 'pm', // Lower case AM or PM
				    'S': pad(date.getSeconds()) // Two digits seconds, 00 through  59
				 };
		
				 // do the replaces
				 for (var key in replacements) {
				   format = format.replace('%' + key, replacements[key]);
				 }
		
				 return format;
			},

			self.daysCount = function(start, end)
			{
				return Math.ceil((start.getTime() - end.getTime())/(24*60*60*1000));
			}
		};
	
		return new utils($); 
	}
);