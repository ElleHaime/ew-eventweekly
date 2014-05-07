require([
    'jquery',
    'fb',
    'noty',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, fb, noty) {
        fb.init();

        if (window.opener) {
            $("#createAcc").click(function(){
                //window.opener.$('#popupRedirect').val('signup');
                window.opener.location.href = "/signup";
                window.close();
            });

            $("#restorePass").click(function(){
                //window.opener.$('#popupRedirect').val('restore');
                window.opener.location.href = "/restore";
                window.close();
            });

            $("#fb-login").click(function(){
                window.opener.$('#popupRedirect').val('reload');
            });
        }

        $("#loginBtn").click(function(){
            $.post( "/login", { email: $("input[name=email]").val(), password: $("input[name=password]").val() })
                .done(function( data ) {
                    data = JSON.parse(data);

                    if (data.error != undefined) {
                        noty({text: data.error, type: 'error'});
                    } else if (data.success != undefined) {
                        if (window.opener) {
                            window.opener.location.href = '/map';
                            window.close();
                        } else {
                            window.location.href = '/map';
                        }
                    }
                });
        });

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
        }
    }
);
