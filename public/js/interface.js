$( document ).ready(function() {
    //https://developers.facebook.com/docs/reference/dialogs/send/
    $('#fb-invite').click(function() {
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
            picture: window.location.host+'/img/logo200.png',
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
        $.post("/event/answer", { answer: 'MAYBE', event_id : $('#event_id').val() });
    });
    $( "#event-decline" ).click(function() {
        $('#event-join').hide();
        $('#event-maybe').hide();
        $('#event-decline').hide();
        $.post("/event/answer", { answer: 'DECLINE', event_id : $('#event_id').val() });
    });

    if($.fn.datetimepicker) {
        $('#date-picker').datetimepicker({
            pickTime: false
        });
        $('#time-picker').datetimepicker({
            pickDate: false
        });
    }

    $('#date-picker').on('changeDate', function(e) {
        //var date = e.localDate.toString();
        console.log(e.localDate.getDate());
        //console.log(e.localDate.toString('MM/dd/yyyy'));
    });

});