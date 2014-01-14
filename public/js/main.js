    /*-------Svetlana script -----*/
    $('.btn-click').click(function () {
        $('.warning-box').slideToggle('2000');
        setTimeout(function () {
            $('.warning-box').slideToggle('')
        }, 5000);
    });



    $('#user-down-caret').click(function () {
        $('#user-down').slideToggle('2000');
    });

    $('#user-down-caret').click(function () {
        $('.user-box').toggleClass('active-box');
    });
    $('.locationCity').click(function () {
        $('.searchCityBlock ').slideToggle('2000');
    });
    $('.locationCity').click(function () {
        $('.location-place_country ').toggleClass('active-box');
    });
    $('.change-box .btn').click(function () {
        $('.form-horizontal').slideToggle('slow');
    });

    $('.switch button.on').click(function () {
        $('.on').toggleClass('active');
        $('.off').removeClass('active');
    });
    $('.switch button.off').click(function () {
        $('.off').toggleClass('active');
        $('.on').removeClass('active');
    });


//$('.tooltip-text').tooltip();
$('#show-popover').popover('toggle');
$('.settings-box-one .checkbox').click(function () {
    $(this).parent().toggleClass('active-box');
});

$(function() {
    $( "#from" ).datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function( selectedDate ) {
            $( "#to" ).datepicker( "option", "minDate", selectedDate );
        }
    });
    $( "#to" ).datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function( selectedDate ) {
            $( "#from" ).datepicker( "option", "maxDate", selectedDate );
        }
    });
});