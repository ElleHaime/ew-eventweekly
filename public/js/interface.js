$( document ).ready(function() {
    //https://developers.facebook.com/docs/reference/dialogs/send/
    $('#fb-invite').click(function() {
        FB.ui({
            method: 'send',
            link: window.location.href
            //link: 'http://events.apppicker.com/index.php'
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

    $('#date-picker').datetimepicker({
        pickTime: false
    });
    $('#time-picker').datetimepicker({
        pickDate: false
    });

    $('#date-picker').on('changeDate', function(e) {
        redraw(e.localDate, null)
    });
    $('#time-picker').on('changeDate', function(e) {
        redraw(null, e.localDate)
    });

    var currDate = new Date(),
        currDateFormatted =  currDate.getDate()+'/'+(currDate.getMonth()+1)+'/'+currDate.getFullYear();

    $('#date-input').val(currDateFormatted);
    $('#date-start').html(convertDate(currDate));

    function redraw(startDate,startTime)
    {
        if (startDate != null)
        {
            startDate = convertDate(startDate);
            $('#date-start').html(startDate);
        }

        if (startTime != null)
        {
            var hours   = startTime.getHours(),
                minutes = startTime.getMinutes();
            if (hours<10) hours='0'+hours;
            if (minutes<10) minutes='0'+minutes;
            startTime = hours+':'+minutes;
            $('#time-start').html(startTime);
        }

        $('#time-string').show('fast');
    }

    /*
    1) можно ли тащить евенты из лицекниги без токена
    2) если чел не залогинен показать ему кнопку "show more", вытащить полный дескрипшн и сохранить в базу
    3) добавить og:url
    4) добавить og:description
    */

    function convertDate(startDate)
    {
        var m_names = new Array("Jan", "Feb", "Mar",
            "Apr", "May", "Jun", "Jul", "Aug", "Sep",
            "Oct", "Nov", "Dec");
        return startDate.getDate() + " " + m_names[startDate.getMonth()]+ " " + startDate.getFullYear();
    }

    function daysBetween(firstDate,secondDate)
    {
        var oneDay = 24*60*60*1000; // hours*minutes*seconds*milliseconds
        var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay)));
    }

});