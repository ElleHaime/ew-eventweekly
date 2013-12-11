$( document ).ready(function() {

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
                                                    username: facebookData[0].username,
                                                    token: access_token},
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