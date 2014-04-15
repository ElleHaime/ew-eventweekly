/**
 * Created by slav on 1/27/14.
 */
/**
 * Created by slav on 1/27/14.
 */
define('SingleEvent' ,['jquery', 'underscore', 'jTruncate', 'niceDate', 'domReady'],function($, _) {

    return function(event) {

        var settings = {
            templateId: '#eventListTemplate'
            //templateId: '.signleEventListElement'
        };

        var template = $(settings.templateId).html();

        var venue = '';

        if (!_.isUndefined(event.venue)) {
            venue = event.venue.name;
        }

        self.getHtml = function() {
            return _.template(template,{
                event_id: event.id,
                event_category: event.category,
                event_img: event.logo,
                event_name: event.name,
                event_start_date: event.start_date,
                event_start_time: event.start_date,
                event_description: $.truncate(event.description, {length: 160}),
                event_fb_id: event.fb_uid,
                event_venue: venue
            });
        };

        return self;
    }

});
