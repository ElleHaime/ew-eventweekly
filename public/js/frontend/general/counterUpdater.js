define('frontCounterUpdater',
    ['jquery', 'utils', 'noty', 'domReady'],
    function($, utils, noty) {

        function frontCounterUpdater($, utils, noty)
        {
            var self = this;

            var debug = true;

            var settings = {
                autoGetEvents: true,
                requestInterval: 2000, // TODO: set some interval
                eventsUrl: '/event/get-counter',

                externalLogged: '#external_logged',
                generalEventsTotal: '#events_total',
                userEventsCreated: '#userEventsCreated',
                userFriendsGoing: '#userFriendsGoing',
                userEventsGoing: '#userEventsGoing',
                userEventsLiked: '#userEventsLiked',
                alreadyGrabbed: false
            };

            self.init = function()
            {
                if ($(settings.externalLogged).length == 1) {
                    if (settings.requestInterval > 0) {
                        interval = setInterval(function(){
                            if (debug) {
                                //console.log('new request');
                            }
                            makeRequest();
                        }, settings.requestInterval);
                    }
                }
            }

            var responseHandler = function(data) {
                if (debug) {
                    //console.log(data);
                }

                if (data.userEventsCreated) {
                    $(settings.userEventsCreated).text(data.userEventsCreated);
                }

                if (data.userFriendsGoing) {
                    $(settings.userFriendsGoing).text(data.userFriendsGoing);
                }

                if (data.userEventsGoing) {
                    $(settings.userEventsGoing).text(data.userEventsGoing);
                }

                if (data.userEventsLiked) {
                    $(settings.userEventsLiked).text(data.userEventsLiked);
                }

                if (data.eventsGTotal) {
                    $(settings.generalEventsTotal).text(data.eventsGTotal);
                }
            };

            var makeRequest = function() {
                $.when($.ajax({
                    url: settings.eventsUrl,
                    type: 'GET',
                    dataType: 'json'})).done(function(response) {
                    responseHandler(response);
                }).always(function() {
                    //console.log('empty result');
                });
            };
        }

        return new frontCounterUpdater($, utils, noty);
    }
);

