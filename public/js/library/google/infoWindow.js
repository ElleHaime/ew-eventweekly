define('googleInfoWindow',
    ['jquery', 'underscore', 'jTruncate', 'niceDate', 'domReady', 'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places'],
    function($, _) {

        function InfoWindow(event) {

            self.createInfoPopupContentSimple = function(event) {
                var date = '', img = '', category = 'other';

                if (!_.isUndefined(event.start_date)) {
                    date = Date.parse(event.start_date).toString('d MMM yyyy');
                }else if (!_.isUndefined(event.start_date_nice)) {
                    date = Date.parse(event.start_date_nice).toString('d MMM yyyy');
                }

                if (!_.isUndefined(event.logo)) {
                    var curImg = new Image();
                    curImg.src = '/upload/img/event/' + event.id + '/' + event.logo;
                    console.log(curImg);
                    if (curImg.height != 0) {
                        img = '/upload/img/event/' + event.id + '/' + event.logo;
                    } else {
                        img = '/img/logo200.png';    
                    }
                } else {
                    img = '/img/logo200.png';
                }

                if (event.category && !_.isUndefined(event.category) && !_.isUndefined(event.category[0]) && !_.isUndefined(event.category[0].key)) {
                    category = event.category[0].key;
                }
                
                var eventlink = window.location.origin+'/event/'+event.id;
                if (!_.isUndefined(event.slugUri)) {
                    eventlink = '/'+event.slugUri;
                }

                return '<div class="info-win '+category+'-category " id="content"> ' +
                    '<div class="events-img-box">' +
                    '<a href="'+eventlink+'"><img  class="events-img" src="'+img+'" alt=""></a>' +
                    '<div class="events-date-box"><i class="icon-time"></i>'+date+'</div> ' +
                    '</div>' +
                    '<div class="events-descriptions-box">' +
                    '<div class="venue-name">'+event.name+'</div><div>'+$.truncate(event.description, {length: 150})+'</div>' +
                    '<a href="'+eventlink+'">Eventweekly link</a>' +
                    '</div>' +
                    '</div>';
            };

            self.createInfoPopupContentMany = function(event) {
                var date = '', category = 'other';

                if (!_.isUndefined(event.start_date)) {
                    date = Date.parse(event.start_date).toString('d MMM yyyy');
                }else if (!_.isUndefined(event.start_date_nice)) {
                    date = Date.parse(event.start_date_nice).toString('d MMM yyyy');
                }

                if (!_.isUndefined(event.category) && !_.isUndefined(event.category[0]) && !_.isUndefined(event.category[0].key)) {
                    category = event.category[0].key;
                }

                var eventlink = window.location.origin+'/event/'+event.id;
                if (!_.isUndefined(event.slugUri)) {
                    eventlink = '/'+event.slugUri;
                }

                return '<div class="events-map">' +
                    ' <div class="'+category+'-category">' +
                    '<a href="'+eventlink+'" class="clearfix">' +
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