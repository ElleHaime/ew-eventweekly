/**
 * Created by slav on 12/4/13.
 */

app.SuggestCategory = {

    settings: {
        btn: '#suggestCategoryBtn',
        categoriesBlock: '#suggestCategoriesBlock',
        uncategorizedLabel: '.uncategorized_label'
    },

    selectedCategory: null,

    init: function(options){
        var $this = this;

        $this.settings = _.extend($this.settings, options);

        $this.__bindClicks();
    },

    __bindClicks: function(){
        var $this = this;

        $('body').on('click', $this.settings.btn, function(){
            $this.__showCategories();
        });

        $('body').on('click', $this.settings.categoriesBlock+' a', function(e){
            e.preventDefault();
            $this.__setCategory($(e.target));
        });
    },

    __showCategories: function(){
        var $this = this;

        $($this.settings.categoriesBlock).show();
    },

    __setCategory: function(linkObj) {
        var $this = this, url = linkObj.attr('href');
        $this.selectedCategory = linkObj.text();
        $.when($this.__sendRequest(url)).then(function(response){
            $this.__responseHandler(response);
        });
    },

    __sendRequest: function(url){
        return $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json'
        });
    },

    __responseHandler: function(response){
        var $this = this;
        if (response.status == true) {
            $($this.settings.uncategorizedLabel).text($this.selectedCategory);
            $($this.settings.btn).remove();
            $($this.settings.categoriesBlock).remove();
        }else {
            alert('Oops! Some error occurred. Call to administrator');
        }
    }

};
