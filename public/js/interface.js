$( document ).ready(function() {
    //https://developers.facebook.com/docs/reference/dialogs/send/
    $('#fb-invite').click(function() {
        $('.categ_green').show();
        FB.ui({
            method: 'send',
            //link: window.location.href
            link: 'http://events.apppicker.com/index.php'
        });
    });
    $( "#event-join" ).click(function() {
        $('#event-join').hide();
        $('#event-maybe').hide();
        $('#event-decline').hide();
        $('.categ_green').show();
        $.post("/event/answer", { answer: 'JOIN', event_id : $('#event_id').val() });
        FB.ui({
            method: 'feed',
            link:   window.location.href,
            caption: 'You are joined event'
        }, function(response){});
    });
    $( "#event-maybe" ).click(function() {
        $('#event-join').hide();
        $('#event-maybe').hide();
        $('#event-decline').hide();
        $('.categ_yellow').show();
        console.log($('#event_id').val());
        $.post("/event/answer", { answer: 'MAYBE', event_id : $('#event_id').val() });
    });
    $( "#event-decline" ).click(function() {
        $('#event-join').hide();
        $('#event-maybe').hide();
        $('#event-decline').hide();
        $.post("/event/answer", { answer: 'DECLINE', event_id : $('#event_id').val() });
    });
});