define('eventFriendControl',
    ['jquery', 'utils', 'noti', 'domReady'],
    function($, utils, noti) {

        function eventFriendControl($, utils, noti)
        {
            var self = this;

            self.settings = {
                accSynced: '#acc_synced',
                externalLogged: '#external_logged'
            },

            self.init = function()
            {
                self.bindEvents();
            }

            self.bindEvents = function()
            {
                if ($(self.settings.externalLogged).length != 1 && $(self.settings.accSynced).val() !== '1') {
                    noti.createNotification(
                        'Login or <a href="/profile">syncronise</a> your account with facebook to see events attended by your friends.</a>',
                        'warning'
                    );
                }
            }
        };

        return new eventFriendControl($, utils, noti);
    }
);
