window.fbAsyncInit = function() {
    FB.init({
        appId      : '303226713112475',
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

$(document).ready(function() {

    /*
    if (navigator.geolocation)
    {
        navigator.geolocation.getCurrentPosition
            (
                function( position )
                {
                    alert( 'lat: ' + position.coords.latitude + ' long:' + position.coords.longitude );
                },
                function( error ){
                    console.log( "Something went wrong: ", error );
                },
                {
                    timeout: (5 * 1000),
                    maximumAge: (1000 * 60 * 15),
                    enableHighAccuracy: true
                }
            );
    }
    */

    $('body').on('click','#fb-login',function(e){
        FB.login(
            function(response) {
                if (response.authResponse) {
                    //console.log('logged in');
                    var access_token = response.authResponse.accessToken;
                    var query='SELECT current_location FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = me())';
                    FB.api({
                            method: 'fql.query',
                            query: query},
                        function(facebookData) {
                            if(facebookData) {
                                console.dir(facebookData);
                            }
                            else
                            {
                                console.log('error getting data');
                            }
                        });
                }
                else {
                    console.log('login filed');
                }
            },
            {scope: 'publish_stream,user_events,friends_events,email,user_likes,create_event,offline_access,read_stream,friends_birthday'}
        );
    });
});