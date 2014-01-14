/**
 * Created by Slava Basko on 12/19/13 <basko.slava@gmail.com>.
 */

define('frontEventInviteFriend', ['jquery', 'noti', 'domReady'],
    function($, noti) {

        var EventInviteFriend = {

            /**
             * @type {{inviteBtn: string, inviteAllBtn: string, friendsBlock: string, friendClass: string, eventLink: string, wallText: string}}
             */
            settings: {
                inviteBtn: '#fb-invite',
                inviteAllBtn: '#fb-invite-all',
                friendsBlock: '#friendsBlock',
                friendClass: 'friendItem',
                eventLink: window.location.href,
                wallText: 'Check out this awesome event!',

                isLogged: '#isLogged',
                isMobile: '#isMobile',
                externalLogged: '#external_logged'
            },

            /**
             * Array of friends
             *
             * @type {null}
             * @private
             */
            __friends: null,

            /**
             * Initialize clicks. Constructor
             */
            init: function(options){
                var $this = this;

                // TODO: delete below line
                //$this.settings.eventLink = 'http://events.apppicker.com';

                $this.settings = _.extend($this.settings, options);
                _.once($this.__bindClicks());
            },

            /**
             * Click binding
             *
             * @private
             */
            __bindClicks: function() {
                var $this = this, body = $('body');
                body.on('click', $this.settings.inviteBtn, $this.__friendsClickHandler());
                body.on('click', '.'+$this.settings.friendClass, $this.__fbMsgHandler());
                body.on('click', $this.settings.inviteAllBtn, $this.__postWallHandler());
            },

            /**
             * After click on invite button
             *
             * @returns {Function}
             * @private
             */
            __friendsClickHandler: function() {
                var $this = this;
                return function(event){
                    if ($($this.settings.isLogged).val() === '0' || $($this.settings.externalLogged).length == 0) {
                        noti.createNotification(
                            'Please <a href="/#fb-login">login via Facebook</a> to be able to invite your friends to event', 'warning'
                        )
                    } else {
                        event.preventDefault();
                        $this.__getFriends();
                    }
                    return true;
                }
            },

            /**
             * After click on single friend
             *
             * @returns {Function}
             * @private
             */
            __fbMsgHandler: function() {
                var $this = this;
                return function(event) {
                    if ($($this.settings.isMobile).val() === '1') {
                        console.log($(this));
                        var friendId = $(this).attr('data-id');
                        window.location = 'http://www.facebook.com/dialog/feed?app_id=166657830211705&link=' +
                            document.URL + '&redirect_uri=' + document.URL + '&to='+ friendId;
                    } else {
                        if ($this.__issetFB()) {
                            if ($this.__issetFB()) {
                                FB.ui({
                                    method: 'send',
                                    to: $(this).data('id'),
                                    link: $this.settings.eventLink
                                });
                            }
                            $this.__removeFriendList();
                        }
                    }
                }
            },

            /**
             * After click on Invite All button
             *
             * @returns {Function}
             * @private
             */
            __postWallHandler: function() {
                var $this = this;
                return function() {
                    if ($this.__issetFB()) {
                        FB.ui({
                            method: 'feed',
                            link: $this.settings.eventLink,
                            caption: $this.settings.wallText
                        }, $this.__afterWallPost());
                    }
                }
            },

            /**
             * After wall post handler
             *
             * @returns {Function}
             * @private
             */
            __afterWallPost: function() {
                var $this = this;
                return function(response) {
                    if (!_.isUndefined(response.post_id) && !_.isNaN(response.post_id) && !_.isNull(response.post_id)) {
                        console.log('post wall success');
                    }else {
                        console.log('post wall fail!');
                    }
                    $this.__removeFriendList();
                }
            },

            /**
             * Render friends
             *
             * @private
             */
            __renderFriends: function() {
                var $this = this;
                // create list
                var container = $($this.settings.friendsBlock);
                container.html('');
                var mfsForm = document.createElement('ul');
                mfsForm.id = 'fbFriendList';
                mfsForm.setAttribute('style', 'overflow-y: scroll; height: 300px');

                // generate element with one friend
                _.each($this.__friends, function(node, index){
                    var friendItem = document.createElement('li');
                    friendItem.id = 'friend_' + node.id;
                    friendItem.setAttribute('data-id', node.id);
                    friendItem.setAttribute('class', $this.settings.friendClass);
                    friendItem.setAttribute('class', friendItem.getAttribute('class')+' clearfix');
                    friendItem.setAttribute('style', 'cursor:pointer');
                    friendItem.title = node.name;
                    friendItem.innerHTML = '<img src="'+node.picture.data.url+'" alt="'+node.name+'"><span>'+node.name+'</span>';
                    mfsForm.appendChild(friendItem);
                });

                // insert HTML
                container.append(mfsForm);

                // Create button Invite All for sending post to user wall
                var inviteAllBtn = document.createElement('input');
                inviteAllBtn.type = 'button';
                inviteAllBtn.value = 'Invite All';
                inviteAllBtn.id = 'fb-invite-all';
                mfsForm.appendChild(inviteAllBtn);
            },

            /**
             * Get friends form facebook
             *
             * @private
             */
            __getFriends: function() {
                var $this = this;
                if ($this.__issetFB()) {
                    FB.api('/me/friends?fields=name,picture.width(40).height(40)', function(response) {
                        console.log(response);
                        $this.__friends = response.data;
                        $this.__renderFriends();
                    });
                }
            },

            /**
             * Remove friends list from document
             *
             * @private
             */
            __removeFriendList: function() {
                $(this.settings.friendsBlock).html('');
            },

            /**
             * Check if FB object exist
             *
             * @returns {boolean}
             * @private
             */
            __issetFB: function() {
                if (_.isUndefined(FB) || _.isNaN(FB)) {
                    console.warn('Oops! No FB object defined.');
                    return false;
                }
                return true;
            }

        };

        return EventInviteFriend;

    });