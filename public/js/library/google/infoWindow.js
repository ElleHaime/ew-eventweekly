define('googleInfoWindow',
    ['jquery', 'underscore', 'jTruncate', 'domReady', 'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places'],
    function($, _) {

        function InfoWindow(event) {

            self.createInfoPopupContent = function(event) {
                return '<div class="info-win" id="content">' +
                    '<div class="venue-name">'+event.name+'</div><div>'+$.truncate(event.description, {length: 200})+'</div>' +
                    '<div>' +
                    '<a target="_blank" href="https://www.facebook.com/events/'+event.eid+'">Facebook link</a> ' +
                    '<a href="'+window.location.origin+'/event/show/'+event.id+'">Eventweekly link</a></div>' +
                    '</div>';
            };

            // prepare HTML for popup window on map
            var contentString = self.createInfoPopupContent(event);

            var InfoWindow = new google.maps.InfoWindow({
                content: contentString
            });

            InfoWindow.createInfoPopupContent = self.createInfoPopupContent;

            // initialize popup window
            return InfoWindow;
        }

        return InfoWindow;

    }
);