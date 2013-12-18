define('frontMemberEditControl',
    ['jquery', 'utils', 'domReady'],
    function($, utils) {

        function frontMemberEditControl($, utils)
        {
            var self = this;

            self.settings = {
                //btnImg: '#add-img-btn',
                btnImg: '#file',
                btnImgUpload: '#logo',

                inpAddress: '#address',
                listAddress: '#address-list'
            },

            self.init = function()
            {
                self.bindEvents();

                // process address
                $(self.settings.inpAddress).keyup(function() {
                    self.__inputFillList(self.settings.inpAddress, self.settings.listAddress);
                });
            }

            self.bindEvents = function()
            {
                $(self.settings.btnImg).click(function() {
                    self.__imitateUpload();
                });
            }

            self.__imitateUpload = function()
            {
                $(self.settings.btnImgUpload).click();
            }

            // input -- input element (usualy type == text)
            // list -- destination element (found values will be rendered here)
            self.__inputFillList = function(input, list)
            {
                if (self.__checkInputFill(input, list))	{
                    var locs = utils.addressAutocomplete($(input)[0]);
                }
            }

            self.__checkInputFill = function(input, list)
            {
                if($(input).val() == '') {
                    $(list).parent('div').addClass('hidden');
                    return false;
                }

                return true;
            }
        }

        return new frontMemberEditControl($, utils);
    }
);

