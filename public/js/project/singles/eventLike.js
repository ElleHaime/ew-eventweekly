/**
 * Created by slav on 12/13/13.
 */

var EventLike = {

    settings: {
        likeUrl: '/event/like',
        likeBtn: '.eventLikeBtn',
        likeBlock: '.like-box',
        thank: 'Thank!'
    },

    target: null,

    /**
     * Constructor
     *
     * @param options
     */
    init: function(options){
        var $this = this;
        // extend options
        $this.settings = _.extend($this.settings, options);

        // initialize clicks
        _.once($this.__bindClicks());
    },

    /**
     * initialize clicks
     *
     * @private
     */
    __bindClicks: function() {
        var $this = this;
        $('body').on('click', $this.settings.likeBtn, $this.__clickHandler());
    },

    /**
     * Click handler
     *
     * @returns {Function}
     * @private
     */
    __clickHandler: function() {
        var $this = this;
        return function(event){
            event.preventDefault();
            var status = $(this).data('status'),
                eventId = $(this).data('id');

            $this.target = $(this);

            $.when($this.__sendStatus(eventId, status)).then(function(response){
                $this.__responseHandler(response);
            });
        }
    },

    /**
     * Send request to server
     *
     * @param eventId
     * @param status
     * @returns {*}
     * @private
     */
    __sendStatus: function(eventId, status) {
        var url = this.settings.likeUrl+'/'+eventId+'/'+status;
        return $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json'
        });
    },

    /**
     * Handle response from server
     *
     * @param data
     * @private
     */
    __responseHandler: function(data) {
        var $this = this;
        if (data.status == true) {
            $($this.target).closest($this.settings.likeBlock).html($this.settings.thank);
        }else {
            var noti = new Noti();
            noti.createNotification('Oops! Error occurred. Can\'t save you choice.', 'error');
        }
    }

};