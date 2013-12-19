require([
    'jquery',
    'frontTopPanel',
    'fb',
    'frontMemberEditControl',
    'noti',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
    ],
    function($, frontTopPanel, fb, frontMemberEditControl) {

        frontTopPanel.init({
            searchCityBlock: '.searchCityBlock'
        });
        fb.init();

        frontMemberEditControl.init();

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
