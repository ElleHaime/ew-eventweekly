require([
    'jquery',
    'fb',
    'noty',
    'fbSdk',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, fb, noty, fbSdk) {
        fb.init();

        if (window.opener) {
            $("#createAcc").click(function(){
                window.opener.location.href = "/auth/signup";
                window.close();
            });

            $("#restorePass").click(function(){
                window.opener.location.href = "/auth/restore";
                window.close();
            });
        }

        $("#loginBtn").click(function(){
            $.post( "/auth/login", { email: $("input[name=email]").val(), password: $("input[name=password]").val() })
                .done(function( data ) {
                    data = JSON.parse(data);

                    if (data.error != undefined) {
                        noty({text: data.error, type: 'error'});
                    } else if (data.success != undefined) {
                        if (window.opener) {
                            //window.opener.location.href = '/search/map?searchTitle=&searchLocationField=&searchLocationLatMin=&searchLocationLngMin=&searchLocationLatMax=&searchLocationLngMax=&searchLocationType=country&searchStartDate=&searchEndDate=&searchCategoriesType=private&searchType=in_map';
                        	window.opener.location.href = '';
                            window.close();
                        } else {
                            //window.location.href = '/search/map?searchTitle=&searchLocationField=&searchLocationLatMin=&searchLocationLngMin=&searchLocationLatMax=&searchLocationLngMax=&searchLocationType=country&searchStartDate=&searchEndDate=&searchCategoriesType=private&searchType=in_map';
                            window.location.href = '';
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
