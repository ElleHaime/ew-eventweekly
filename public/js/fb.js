$( document ).ready(function() {

    //https://developers.facebook.com/docs/reference/dialogs/feed/
    $('#event_going').click(function() {
        console.log('join');
        FB.ui({
            method: 'feed',
            link: window.location.href,
            caption: 'You are joined event'
        }, function(response){
            console.log(response);
        });
    });


    $('#event_share').click(function() {
        FB.ui({
            picture: window.location.host+'/img/logo200.png',
            method: 'feed',
            link: window.location.href,
            caption: 'User are shared this event'
        }, function(response){});
    });

    //https://developers.facebook.com/docs/reference/dialogs/send/
    $('#fb-invite').click(function() {
        FB.ui({
            method: 'send',
            link: window.location.href
        }, function(response){
            console.log(response);
        });
    });

    /*function showEvent(event)
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
                map: window.map,
                title:event.name
            });
            window.markers.push(marker);
            google.maps.event.addListener(marker, 'click', function() {
                infowindow.open(window.map,marker);
            });
        }
    }

    var lat = $('#lat').val(),
        lng = $('#lng').val();
    if ( (typeof(lat)!=='undefined') && (typeof(lng)!=='undefined') )
    {
        var mapOptions = {
            center: new google.maps.LatLng(lat, lng),
            zoom: 14,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        window.map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
        window.mc = new MarkerClusterer(window.map);
        window.markers = [];
        var totalEvents=0;

        $.post("/eventmap",
            function(data) {
                data = jQuery.parseJSON(data);
                if (data.status == "OK") {

                    if (data.message[0].length > 0) //own events
                    {
                        totalEvents=data.message[0].length;
                        console.log('My events count:'+data.message[0].length);
                        $.each(data.message[0], function(index,event) {
                            showEvent(event);
                        });
                    }
                    if (data.message[1].length>0) //friend events
                    {
                        totalEvents=data.message[1].length;
                        console.log('Friend events count:'+data.message[1].length);
                        $.each(data.message[1], function(index,event) {
                            showEvent(event);
                        });
                    }
                }
            }).done(function (){
                $('#events_count').html(totalEvents);
                var mcOptions = { gridSize: 50, maxZoom: 15};
                window.mc = new MarkerClusterer(window.map, window.markers, mcOptions);
            });
    }*/

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
                                     query: 'SELECT first_name,last_name, email,current_location, current_address, username, pic_square FROM user WHERE uid='+response.authResponse.userID},
                                     function(facebookData) {
                                        if(facebookData) {
                                            $.post("fbregister", { uid: response.authResponse.userID,
                                                              address: facebookData[0].current_address,
                                                              location: facebookData[0].current_location,
                                                              email: facebookData[0].email,
                                                              logo: facebookData[0].pic_square,
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

    /*
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '423750634398167',
            status     : true
        });
    };
    */

    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=423750634398167";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
});


