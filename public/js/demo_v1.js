$(document).bind('pageinit', function(){
    $('#fb-login').click(function() {
        FB.login(
            function(response) {
                if (response.authResponse) {
                    uid=response.authResponse.userID;
                    access_token=response.authResponse.accessToken;
                     $.post("/public/demov1/getToken", { uid: uid, access_token: access_token },
                     function(data) {
                         data = jQuery.parseJSON(data);
                         if (data.status=='OK')
                         {
                             FB.api({
                                     method: 'fql.query',
                                     query: 'SELECT first_name,last_name FROM user WHERE uid='+uid},
                                 function(facebookData) {
                                     if(facebookData) {
                                         var message = '<div>Hello, '+facebookData[0]['first_name']+'</div>'+
                                             '<div>Now you can go to <a href="Events">event list</a> or to <a href="Map" rel="external">map</a> tabs</div>';
                                         $('#login_message').html(message);
                                         $('#login_message').show();
                                     }
                                     else
                                     {
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
});


