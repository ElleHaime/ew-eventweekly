define('googleInfoWindow',
    ['jquery', 'underscore', 'jTruncate', 'niceDate', 'domReady', 'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places'],
    function($, _) {

        function InfoWindow(event) {

            self.createInfoPopupContentSimple = function(event) {
                var date = '', img = '';

                if (!_.isUndefined(event.start_date)) {
                    date = Date.parse(event.start_date).toString('d MMM yyyy');
                }else if (!_.isUndefined(event.start_date_nice)) {
                    date = Date.parse(event.start_date_nice).toString('d MMM yyyy');
                }

                if (!_.isUndefined(event.image) && !_.isUndefined(event.image[0])) {
                    img = event.image[0].image;
                }else if (!_.isUndefined(event.pic_big)) {
                    img = event.pic_big;
                }
                
                return '<div class="info-win '+event.category[0].key+'-category " id="content"> ' +
                    '<div class="events-img-box">' +
                    '<img  class="events-img" src="'+img+'" alt="">' +
                    '<div class="events-date-box"><i class="icon-time"></i>'+date+'</div> ' +
                    '</div>' +
                    '<div class="events-descriptions-box">' +
                    '<div class="venue-name">'+event.name+'</div><div>'+$.truncate(event.description, {length: 150})+'</div>' +
                    '<a href="'+window.location.origin+'/event/show/'+event.id+'">Eventweekly link</a>' +
                    '</div>' +
                    '</div>';
            };

            self.createInfoPopupContentMany = function(event) {
                var date = '';

                if (!_.isUndefined(event.start_date)) {
                    date = Date.parse(event.start_date).toString('d MMM yyyy');
                }else if (!_.isUndefined(event.start_date_nice)) {
                    date = Date.parse(event.start_date_nice).toString('d MMM yyyy');
                }

                return '<div class="events-map">' +
                    ' <div class="'+event.category[0].key+'-category">' +
                    '<a href="'+window.location.origin+'/event/show/'+event.id+'" class="clearfix">' +
                    '<span class="date-events-map">'+date+'</span> ' +
                    '<span class="events-map-text">'+event.name+'</span>' +
                    '</a>' +
                    '</div>' +
                    '</div>';
            };

            // prepare HTML for popup window on map
            var contentString = self.createInfoPopupContentSimple(event);

            var InfoWindow = new google.maps.InfoWindow({
                content: contentString,
                maxWidth: 480
            });

            InfoWindow.createInfoPopupContentSimple = self.createInfoPopupContentSimple;
            InfoWindow.createInfoPopupContentMany = self.createInfoPopupContentMany;

            // initialize popup window
            return InfoWindow;

        }

        return InfoWindow;

    }
);