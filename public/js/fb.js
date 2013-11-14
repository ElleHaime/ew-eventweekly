$(window).load(function() {

    checkProfile();

    function checkProfile() {
        if ($('#check_ext_profile').length) {
            FB.api({
                 method: 'fql.query',
                 query: 'SELECT current_address, email, username, pic FROM user WHERE uid=' + $('#member_uid').val() },
                 function(facebookData) {
                    if(facebookData) {
                        var fbState = new Array();
                        for (var i in facebookData[0]) {
                            if (facebookData[0][i] === null || facebookData[0][i] === undefined) {
                                fbState[i] = '';
                            } else {
                                fbState[i] = facebookData[0][i];
                            }
                        }

                       for (var state in fbState) {
                            if (fbState[state] === $('#acc_' + state).val() || 
                                    (state == 'current_location' && fbState[state] == '') ||
                                    (state == 'email' && fbState[state] == '')) {
                                delete fbState[state];
                                $('#acc_' + state).remove();
                            }  
                        }

                        if (Object.keys(fbState).length != 0) {
                            for (state in fbState) {
                                $('#acc_' + state).val(fbState[state]);
                            }
                            $('#do_update_profile').toggle();
                        } 
                    }
            });
        }
    }

    $('#sync_profiles').click(function() {
        var changeParams = $('[name=acc_difference]');

        if (changeParams.length != 0) {
            var states = {};
            changeParams.each(function() {
                states[$(this).attr('ew_val')] = $(this).val();
            });

            //console.log(states);
            $.post("profile/refresh", 
                    states,
                    function(data) {
                        data = jQuery.parseJSON(data);
                       //console.log(data);
                        if (data.status == 'OK') {
                            for (var item in data.updated) {
                                elem = $('#' + item).get(0).tagName;
                                if (elem == 'IMG') {
                                    $('#' + item).attr('src', data.updated[item]);
                                    if (item == 'member_logo') {
                                    	console.log(item);
                                        $('#user-down-logo').attr('src', data.updated[item]);
                                    }
                                } else {
                                    $('#' + item).val(data.updated[item]);
                                    $('#' + item).text(data.updated[item]);

                                    if (item == 'name') {
                                        $('#user-down-name').text(data.updated[item]);
                                    }
                                }
                            }
                        }
                        $('#do_update_profile').hide();
                    });
        }
    });

    $('#no_sync_profiles').click(function() {
        $('#do_update_profile').hide();
    });

    $('#he_is_nervous').click(function() {
        $('#do_update_profile').hide();
    });
});

$( document ).ready(function() {

//https://developers.facebook.com/docs/reference/dialogs/feed/
    $('#event_going').click(function() {
        FB.ui({
            method: 'feed',
            link: window.location.href,
            caption: 'You are joined event'
        }, function(response){});
    });


    $('#event_share').click(function() {
        FB.ui({
            method: 'feed',
            link: window.location.href,
            caption: 'User are shared this event'
        }, function(response){});
    });


    function showEvent(event)
    {
        if (typeof(event.venue.latitude)!='undefined' && typeof(event.venue.longitude)!='undefined')
        {
        	 var contentString = '<div class="info-win" id="content">' +
             '<div class="venue-name">'+event.name+'</div><div>'+event.anon+'</div>' +
             '<div>' +
             '<a target="_blank" href="https://www.facebook.com/events/'+event.eid+'">Facebook link</a> ' +
             '<a target="_blank" href="'+window.location.origin+'/event/show/'+event.id+'">Eventweekly link</a></div>' +
             '</div>';
	         //contentString+='<div>Lat: '+event.venue.latitude+'</div><div>Lng: '+event.venue.longitude+'</div>';
	         var infowindow = new google.maps.InfoWindow({
	             content: contentString
	         })
	         myLatlng = new google.maps.LatLng(event.venue.latitude,event.venue.longitude);

            var marker = new google.maps.Marker({
                position: myLatlng,
                map: map,
                title:event.name
            });
            markers.push(marker);
            google.maps.event.addListener(marker, 'click', function() {
                infowindow.open(map,marker);
            });
        }
    }

    var lat = $('#lat').val(),
        lng = $('#lng').val();
    if ( (typeof(lat)!=='undefined') && (typeof(lng)!=='undefined') )
    {
        var mapOptions = {
            center: new google.maps.LatLng(lat, lng),
            zoom: 8,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
        var mc = new MarkerClusterer(map);
        var markers = [];
        var totalEvents=0;

        $.post("/search",
            function(data) {
                data = jQuery.parseJSON(data);
                if (data.status == "OK") {
                    if (data.message[0].length > 0) //own events
                    {
                        totalEvents+=data.message[0].length;
                        $.each(data.message[0], function(index,event) {
                            showEvent(event);
                        });
                    }
                    if (data.message[1].length>0) //friend events
                    {
                        totalEvents+=data.message[1].length;
                        $.each(data.message[1], function(index,event) {
                            showEvent(event);
                        });
                    }
                } else {
                	console.log(data.message);
                }
            }).done(function (){
            	$('#events_count').html(totalEvents);
                var mcOptions = { gridSize: 50, maxZoom: 15};
                var mc = new MarkerClusterer(map, markers, mcOptions);
            });
    }

    $('#fb-login').click(function() {
        FB.login(
            function(response) {
                if (response.authResponse) {
                    access_token=response.authResponse.accessToken;
                    $.post("fblogin", { uid: response.authResponse.userID, access_token: access_token },
                        function(data) {
                            data = jQuery.parseJSON(data);
                            if (data.status=='OK')
                            {
                                 FB.api({
                                     method: 'fql.query',
                                     query: 'SELECT first_name,last_name, email,current_location, current_address, username, pic FROM user WHERE uid='+response.authResponse.userID},
                                     function(facebookData) {
                                        if(facebookData) {
                                        	console.log(facebookData);
                                            $.post("fbregister", { uid: response.authResponse.userID,
                                                              address: facebookData[0].current_address,
                                                              location: facebookData[0].current_location,
                                                              email: facebookData[0].email,
                                                              logo: facebookData[0].pic,
                                                              first_name: facebookData[0].first_name,
                                                              last_name: facebookData[0].last_name,
                                                              username: facebookData[0].username },
                                                    function(registered) {
                                                        session = jQuery.parseJSON(registered);
                                                        if (session.status == 'OK') {
                                                            window.location.href='/map';
                                                        }
                                                    });                                            
                                        } else {
                                            $('#login_message').html('Facebook return empty result :(');
                                            $('#login_message').show();
                                        }
                                 });
                                
                            }
                        });
                }
                else {
                    alert('You need to be logged in.');
                }
            },
            {scope: 'user_events,friends_events,email'}
        );
    });

    window.fbAsyncInit = function() {
        FB.init({
            appId      : '423750634398167',
            status     : true
        });
    };

    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=361888093918931";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

});


