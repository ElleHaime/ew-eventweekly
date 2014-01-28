/**
 * Created by slav on 1/27/14.
 */
define('listListener' ,['jquery','noti','SingleEvent','utils','domReady','underscore'],function($, noti, SingleEvent) {

    return function(options) {

        var settings = {
            eventsBlock: '.eventsBlock',
            eventsUrl: '/event/test-get',
            requestInterval: 0
        };

        var interval = null;

        settings = _.extend(settings, options);

        var request = function() {
            return $.ajax({
                url: settings.eventsUrl,
                type: 'GET',
                dataType: 'json'
            });
        };

        var responseHandler = function(data) {
            if (data.status == true && !_.isUndefined(data.events)) {

                var eventsBlock = $(settings.eventsBlock);

                $.each(data.events, function(index,event) {
                    var newEventHtml = new SingleEvent(event);
                    newEventHtml = newEventHtml.getHtml();
                    eventsBlock.append(newEventHtml);
                });

                // initialize venue tooltips
                $('.tooltip-text').tooltip();
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

        makeRequest();

        if (settings.requestInterval > 0) {
            interval = setInterval(function(){
                makeRequest();
            }, settings.requestInterval);
        }

    }

});
