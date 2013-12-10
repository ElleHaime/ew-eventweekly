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

    $('#date-picker-start').datetimepicker({
        pickTime: false
    });
    $('#date-picker-end').datetimepicker({
        pickTime: false
    });
    $('#time-picker-start').datetimepicker({
        pickDate: false
    });
    $('#time-picker-end').datetimepicker({
        pickDate: false
    });

    $('#date-picker-start').on('changeDate', function(e) {
        redraw(e.localDate, null, 'start')
    });
    $('#date-picker-end').on('changeDate', function(e) {
        redraw(e.localDate, null, 'end')
    });
    $('#time-picker-start').on('changeDate', function(e) {
        redraw(null, e.localDate, 'start')
    });

    $('#location-input').keyup(function() {
        if ($(this).val() == '') {
            $('#locations-list').parent('div').addClass('hidden');
            return;
        }

        $.post("/location/get/"+$(this).val(), function(data){
            data = jQuery.parseJSON(data);
            if (data.STATUS == 'OK'){
                data = data.MESSAGE;
                var locations='';
                $.each(data, function(index,location) {
                    locations+='<li data-id='+index+'>'+location+'</li>';
                });
                $('#locations-list').html(locations);
                $('#locations-list').parent('div').removeClass('hidden');
            }
        })
    });

    $('#address-input').keyup(function() {
        if ($(this).val() == '') {
            $('#addresses-list').parent('div').addClass('hidden');
            return;
        }
        $.post("/venue/getAddress/"+$(this).val(), function(data){
            data = jQuery.parseJSON(data);
            if (data.STATUS == 'OK'){
                data = data.MESSAGE;
                var addresses='';
                $.each(data, function(index,address) {
                    addresses+='<li data-id='+index+'>'+address+'</li>';
                });
                $('#addresses-list').html(addresses);
                $('#addresses-list').parent('div').removeClass('hidden');
            }
        })
    });

    $('#venue-input').keyup(function() {
        if ($(this).val() == '') {
            $('#venues-list').parent('div').addClass('hidden');
            return;
        }
        $.post("/venue/getVenue/"+$(this).val(), function(data){
            data = jQuery.parseJSON(data);
            if (data.STATUS == 'OK'){
                data = data.MESSAGE;
                var venues='';
                $.each(data, function(index,venue) {
                    venues+='<li data-id='+index+'>'+venue+'</li>';
                });
                $('#venues-list').html(venues);
                $('#venues-list').parent('div').removeClass('hidden');
            }
        })
    });

    $("ul#venues-list").on("click","li", function(){
        $('#venue-input').val($(this).text());
        $(this).parent('ul').parent('div').addClass('hidden');
    });

    $("ul#addresses-list").on("click","li", function(){
        $('#address-input').val($(this).text());
        $(this).parent('ul').parent('div').addClass('hidden');
    });

    $("ul#locations-list").on("click","li", function(){
        $('#location-input').val($(this).text());
        $(this).parent('ul').parent('div').addClass('hidden');
    });

    $('#event_category').change(function() {
        var category = '<div><label>'+$('#event_category :selected').text()+'</label><a href="#" class="icon-remove-sign" catid="' +$ ('#event_category :selected').val() + '"></div>';
        $('#event_category :selected').remove();
        $('#event-categories-selected').show();
        $('#event-categories-selected').append(category);
        if ($('select#event_category option').length == 0) {
            $('#event_category').hide();
        }
    });

    $("#event-categories-selected").on("click",".icon-remove-sign", function() {
        $('#event_category').append('<option value="' + $(this).attr('catid') + '">'+$(this).prev('label').html()+'</option>');
        $(this).parent('div').remove();
        if ($("#event-categories-selected").children('div').length == 0) {
            $('#event-categories-selected').hide();
        }
    });
    
    $('#add-web-site').click(function() {
        var url = $(this).prev('input').val();
        if (url.indexOf('http', 0) < 0) {
            url = 'http://'+url;
        }
        var link = '<div><a target="_blank" href="' + url + '">' + url + '</a>' + '<a href="#" class="icon-remove-sign"></a></div>';
        $('#event-sites').append(link);
        $('#event-sites').show();
        $(this).prev('input').val('');
    });

    $("#event-sites").on("click",".icon-remove-sign", function(){
        $(this).parent('div').remove();
        $(this).remove();
        if ($('#event-sites').children('div').length == 0) {
        	$('#event-sites').hide();
        }
    });

    //init
    var currDate = new Date(),
        currDateFormatted = currDate.getDate()+'/'+(currDate.getMonth()+1)+'/'+currDate.getFullYear();
    
    $('input[data-type="event_date"]').each(function() {
    	if ($(this).val() == '') {
    		$(this).val(currDateFormatted);
    		$(this).html(convertDate(currDate));
    	}
    });
    $('input[data-type="event_time"]').each(function() {
    	if ($(this).val() == '') {
    		$(this).val('00:00:00');
    	}
    });

    function convertDate(startDate){
        var m_names = new Array("Jan", "Feb", "Mar",
            "Apr", "May", "Jun", "Jul", "Aug", "Sep",
            "Oct", "Nov", "Dec");
        return startDate.getDate() + " " + m_names[startDate.getMonth()]+ " " + startDate.getFullYear();
    }

    function daysCount(firstDate,secondDate){
        var oneDay = 24*60*60*1000;
        return Math.ceil((firstDate.getTime() - secondDate.getTime())/(oneDay));
    }

    function redraw(startDate, startTime, elem){
        var now = new Date(),
            daysBetween = 0;

        if (startDate == null) {
            startDate = now;
        }
        daysBetween = daysCount(startDate,now);

        if (daysBetween < 0){
            $('#' + elem + '_date').val('');
            $('#' + elem + '_date').text('Incorrect date');
            $('#time-string').hide('fast');
            return false;
        }
        $('#time-string').show('fast');

        if (elem == 'start') {
	        if (daysBetween > 30) {
	            var count = Math.round(daysBetween/30),
	                text = 'Event happens more than in ' + count + ' month';
	            if (count>1) text+='s';
	            $('#days-count').html(text);
	            return;
	        }
	
	        if (daysBetween > 7) {
	            var count = Math.round(daysBetween/7),
	                text = 'Event happens more than in '+count+' week';
	            if (count>1) text+='s';
	            $('#days-count').html(text);
	            return;
	        }
	
	        if (daysBetween > 1){
	            $('#days-count').html('Event happens in '+daysBetween+' days');
	            return;
	        }
	
	        if (daysBetween == 1)
	            $('#days-count').html('Event happens tomorrow');
	
	        if (daysBetween == 0)
	            $('#days-count').html('Event happens today');
	
	        startDate = convertDate(startDate);
	        $('#date-start').html(startDate);
        }
        
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

/*
Choose location 	- выбираем адресс (улица, город....) (Чайковский пер - из своей базы)
Smock Alley Theatre	- список венуесов из базы к первому полю
change location  	- перекидывает на карту для установки нового локейшина
Event web site 		- добавляет сайты
Категории тащить из базы
Create promoter		- создает профайл промоутера
*/
