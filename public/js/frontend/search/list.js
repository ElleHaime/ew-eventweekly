/**
*updated
 * Created by Slava Basko on 12/26/13 <basko.slava@gmail.com>.
 */
require([
    'jquery',
    'fb',
    'frontEventLike',
    'noty',
    'lazyLoader',//new  !! eventList.volt has <scriptjquery    
    'utils',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, fb, frontEventLike, noty, lazyLoader) {

        fb.init();
        frontEventLike.init();
        lazyLoader.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
        }

        /*
        **********************
        * =new code
        **********************
        */

/*



        var self = this;

        self.settings = 
        {
            loadMoreButton: '#load_more',
            divToUpdate: '.page__wrapper',
            divToOverlay: '.page',
            preloader: '#preloader',
            pageNumber: 1,
            url: window.location.href,
            nextPageUrl: '',
            totalPagesJs: 0
        }
        $(self.settings.loadMoreButton).hide();

        self.settings.totalPagesJs = document.getElementById("totalPagesJs").textContent;
        
    
        if ( self.settings.totalPagesJs>1 ) {
            $(self.settings.loadMoreButton).show();
        }       

        $("img.lazy").lazyload();

        
        $(self.settings.loadMoreButton).click(function(){
            
            $("img.lazy").lazyload();
        
            //overlay before request done
            $(self.settings.divToOverlay)
                .css({
                    'opacity' : 0.4,
                    'background-color': 'black',
                });
            $(self.settings.preloader)
                .css({
                    'position' : 'fixed',
                    'top': '30%',
                    'left': $(window).width()/2-128/2,
                    'display': 'inline'
                });


            self.settings.pageNumber++;

            if ( self.settings.pageNumber>=self.settings.totalPagesJs ) {
                $(self.settings.loadMoreButton).hide();
            }


            
            nextPageUrl = self.settings.url + '&page=' + self.settings.pageNumber;
            $.ajax({url:nextPageUrl,success:function(result){
                $(self.settings.divToUpdate).html(result);
                $(self.settings.divToOverlay)
                    .css({
                        'opacity' : 1,
                        'background-color': 'transparent',
                    });
            }});

        });*/

    }
);



 /*
 **********************
 * =1 variant
 **********************
 */
// require([
//     'jquery',
//     'fb',
//     'frontEventLike',
//     'noty',
//     'utils',
//     'domReady',
//     'underscore',
//     'jCookie'
// ],
//     function($, fb, frontEventLike, noty) {
//         /*
//         **********************
//         * =old code
//         **********************
//         */
//         fb.init();

//         frontEventLike.init();
//         if ($('#splash_messages').length > 0) {
//             var fMessage = $('#splash_messages');
//             noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
//         }
//         //--------------------

//         /*
//         **********************
//         * =new code
//         **********************
//         */
//         var self = this;

//         self.settings = 
//         {
//             loadMoreButton: '#load_more',
//             divToUpdate: '.page__wrapper',
//             divToOverlay: '.page'
//         }

//         self.init = function()
//         {
//             // initialize clicks
//             self.__bindClicks();
//             //alert('ololo');
//         }

//         self.__bindClicks = function() 
//         {
//             $(self.settings.loadMoreButton).click(function() {
//                  alert('ololo');
//                 self.__someFunc();
//             });
//             //alert('ololo');
//         }

//         self.__someFunc = function()
//         {
//             alert('ololo');
//         }




//     }
// );



/*
**********************
* =2 variant
**********************
*/
// define('mainFunc',
//     ['jquery', 'utils', 'noty', 'domReady'],
//     function($, utils, noty) {

//         function mainFunc($, utils, noty)
//         {
//             var self = this;

//             self.settings = 
//             {
//                 loadMoreButton: '#load_more',
//                 divToUpdate: '.page__wrapper',
//                 divToOverlay: '.page'
//             }

//             self.init = function()
//             {
//                 // initialize clicks
//                 self.__bindClicks();
//                 //alert('ololo');
//             }

//             self.__bindClicks = function() 
//             {
//                 $(self.settings.loadMoreButton).click(function() {
//                     self.__someFunc();
//                 });
//                 //alert('ololo');
//             }

//             self.__someFunc = function()
//             {
//                 alert('ololo');
//             }
//         }

//         return new mainFunc($, utils, noty);
//     }
// );
