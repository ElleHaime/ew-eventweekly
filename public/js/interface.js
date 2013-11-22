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

    //init
    var currDate = new Date(),
        currDateFormatted =  currDate.getDate()+'/'+(currDate.getMonth()+1)+'/'+currDate.getFullYear();
    $('#date-input').val(currDateFormatted);
    $('#date-start').html(convertDate(currDate));

    function convertDate(startDate)
    {
        var m_names = new Array("Jan", "Feb", "Mar",
            "Apr", "May", "Jun", "Jul", "Aug", "Sep",
            "Oct", "Nov", "Dec");
        return startDate.getDate() + " " + m_names[startDate.getMonth()]+ " " + startDate.getFullYear();
    }

    function daysCount(firstDate,secondDate)
    {
        var oneDay = 24*60*60*1000;
        return Math.ceil((firstDate.getTime() - secondDate.getTime())/(oneDay));
    }

            var now = new Date(),
                daysBetween = daysCount(startDate,now);
            if (daysBetween < 0)
            {
                $('#date-input').val('Incorrect date');
                $('#time-string').hide('fast');
                return;
            }

            if ( daysBetween%30 == 0)
            {
                var count = daysBetween/30,
                    text = 'Event happens - in '+count+' month';
                if (count>1)
                    text+='s';
                $('#days-count').html(text);
                return;
            }

            if ( daysBetween%7 == 0)
            {
                var count = daysBetween/7,
                    text = 'Event happens - in '+count+' week';
                if (count>1)
                    text+='s';
                $('#days-count').html(text);
                return;
            }

            if (daysBetween == 0)
                $('#days-count').html('Event happens - today');

            if (daysBetween == 1)
                $('#days-count').html('Event happens - tomorrow');

            if ( (daysBetween > 1) )
                $('#days-count').html('Event happens - in '+daysBetween+' days');

    }

    function daysCount(firstDate,secondDate){
        var oneDay = 24*60*60*1000;
        return Math.ceil((firstDate.getTime() - secondDate.getTime())/(oneDay));
    }

    function redraw(startDate,startTime){
        var now = new Date(),
            daysBetween = daysCount(startDate,now);
        if (daysBetween < 0){
            $('#date-input').val('Incorrect date');
            $('#time-string').hide('fast');
            return;
        }
        if ( daysBetween%30 == 0){
            var count = daysBetween/30,
                text = 'Event happens - in '+count+' month';
            if (count>1) text+='s';
            $('#days-count').html(text);
            return;
        }
        if ( daysBetween%7 == 0){
            var count = daysBetween/7,
                text = 'Event happens - in '+count+' week';
            if (count>1) text+='s';
            $('#days-count').html(text);
            return;
        }
        if ( (daysBetween > 1) ){
            $('#days-count').html('Event happens - in '+daysBetween+' days');
            return;
        }

        if (daysBetween == 1)
            $('#days-count').html('Event happens - tomorrow');

        if (daysBetween == 0)
            $('#days-count').html('Event happens - today');

        startDate = convertDate(startDate);
        $('#date-start').html(startDate);

        if (startTime != null)
        {
            var hours   = startTime.getHours(),
                minutes = startTime.getMinutes(),
                seconds = startTime.getSeconds();
            if (hours<10) hours='0'+hours;
            if (minutes<10) minutes='0'+minutes;
            if (seconds<10) seconds='0'+seconds;
            startTime = hours+':'+minutes+':'+seconds;
            $('#time-start').html(startTime);
        }
    }

});