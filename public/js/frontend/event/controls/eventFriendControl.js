define('eventFriendControl',
    ['jquery', 'utils', 'noty', 'domReady'],
    function($, utils, noty) {

        function eventFriendControl($, utils, noty)
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
                    noty({text: 'Login or <a href="/profile">syncronise</a> your account with facebook to see events attended by your friends.</a>', type: 'warning'});
                }
            }
        };

        return new eventFriendControl($, utils, noty);
    }
);
