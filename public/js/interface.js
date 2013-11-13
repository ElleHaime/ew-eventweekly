$( document ).ready(function() {
    $( "#event-join" ).click(function() {
        $('.categ_yellow').hide();
        $('.categ_green').show();
        FB.ui({
            method: 'feed',
            link:   window.location.href,
            caption: 'You are joined event'
        }, function(response){});

    });
    $( "#event-maybe" ).click(function() {
        $('.categ_green').hide();
        $('.categ_yellow').show();
    });
    $( "#event-decline" ).click(function() {
        $('.categ_green').hide();
        $('.categ_yellow').hide();
    });
});