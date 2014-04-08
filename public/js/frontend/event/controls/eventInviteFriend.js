/**
 * Created by Slava Basko on 12/19/13 <basko.slava@gmail.com>.
 */

define('frontEventInviteFriend', ['jquery', 'noty',  'fb', 'domReady'],
    function($, noty, fb) {

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
                externalLogged: '#external_logged',
                fieldBlockId: 'fbFriendList',
                fieldSearchFiledId: 'friendSearchInListInput'
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

            __bindSearch: function() {
                var $this = this;
                $('#'+$this.settings.fieldSearchFiledId).keyup(function() {
                    var typed = $(this).val();
                    if (typed.length > 1) {
                        setTimeout(function() {
                            var searchRes = _.filter($this.__friends, function(friend){
                                var patt = eval('/.*'+typed+'+./i');
                                return patt.test(friend.name);
                            });

                            $('li.'+$this.settings.friendClass).hide();

                            _.each(searchRes, function(searchedFriend) {
                                $('#friend_'+searchedFriend.id).show();
                            });
                        }, 0);
                    }else {
                        $('li.'+$this.settings.friendClass).show();
                    }
                });
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
                        noty({text: 'Please <a href="#" class="fb-login-popup" onclick="return false;">login via Facebook</a> to be able to invite your friends to event', type: 'warning'});
                    } else {
                        event.preventDefault();

                        // open or close invite friend panel
                        if ($($this.settings.friendsBlock + ' ul').length == 0) {
                            $this.__getFriends();
                        } else {
                            // remove events
                            $($this.settings.friendsBlock).empty();

                            // hide button Invite all
                            $($this.settings.inviteAllBtn).css('display', 'none');
                        }
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
                        var friendId = $(this).attr('data-id');
                        window.location = 'http://www.facebook.com/dialog/feed?app_id='+window.fbAppId+'&link=' +
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

                            $($this.settings.inviteAllBtn).css('display', 'none');
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
                        //console.log('post wall success');
                    }else {
                        //console.log('post wall fail!');
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
                mfsForm.id = $this.settings.fieldBlockId;
                mfsForm.setAttribute('style', 'overflow-y: scroll; height: 300px');

                // generate search filed
                var friendSearchItem = document.createElement('li');
                friendSearchItem.id = 'friendSearchInList';
                friendSearchItem.innerHTML = '<input id="'+$this.settings.fieldSearchFiledId+'" placeholder="Type Name" />';
                mfsForm.appendChild(friendSearchItem);
                //

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

                // show button Invite all
                $($this.settings.inviteAllBtn).css('display', 'block');

                _.once($this.__bindSearch());
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
                        $this.__friends = response.data;
                        $this.__friends.sort(function(a, b) {
                            if(a.name < b.name) return -1;
                            if(a.name > b.name) return 1;
                            return 0;
                        });
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