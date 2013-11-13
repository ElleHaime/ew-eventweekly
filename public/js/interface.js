$( document ).ready(function() {
    $( "#event-join" ).click(function() {
        $('.categ_yellow').hide();
        $('.categ_green').show();
        $.post("/event/answer", { answer: 'JOIN', event_id : $('#event_id').val() });
        FB.ui({
            method: 'feed',
            link:   window.location.href,
            caption: 'You are joined event'
        }, function(response){});
    });
    $( "#event-maybe" ).click(function() {
        $('.categ_green').hide();
        $('.categ_yellow').show();
        $.post("/event/answer", { answer: 'MAYBE', event_id : $('#event_id').val() });
    });
    $( "#event-decline" ).click(function() {
        $('.categ_green').hide();
        $('.categ_yellow').hide();
        $.post("/event/answer", { answer: 'DECLINE', event_id : $('#event_id').val() });
    });
});