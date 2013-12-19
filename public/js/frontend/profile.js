require([
    'jquery',
    'frontTopPanel',
    'fb',
    'frontMemberEditControl',
    'frontMemberChangeLocation',
    'noti',
    'utils',
    'domReady',
    'underscore',
    'jCookie',
    'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places',
    'http://connect.facebook.net/en_US/all.js#xfbml=1&appId=166657830211705'
],
    function($, frontTopPanel, fb, frontMemberEditControl, frontMemberChangeLocation) {

        frontTopPanel.init({
            searchCityBlock: '.searchCityBlock'
        });
        fb.init({
            appId: '166657830211705',
            status: true
        });

        frontMemberEditControl.init();

        frontMemberChangeLocation.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
        }

        $('.settings-box-one .checkbox').click(function () {
            $(this).parent().toggleClass('active-box');
        });

        $('.settings-box-one').click(function () {
            var val = $(this).find('.fieldId').attr('value');

            var el = $("#filters input[value='" + val + "']");

            el.prop('checked', !el.prop('checked'));
        });

        $('#saveFilter').click(function(){
            $('#filters').submit();
        });
    }
);
