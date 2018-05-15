;(function ($, window) {
    'use strict';

    $.plugin('mojProductSuggest', {
        defaults: {
            'searchUrl': '/widgets/DashProductSearch/searchProduct?search=',
            'suggestSelector': '.suggest--container',
            'hiddenClass': 'is--hidden'
        },

        init: function () {
            var me = this;

            me.applyDataAttributes();

            me._$searchBox = $(me.$el[0]);
            me._$suggestBox = $(me.opts.suggestSelector);

            me.registerEvents();
        },

        registerEvents: function () {
            var me = this;

            me._on(me._$searchBox, 'keyup', $.proxy(me.onChange, me));
        },

        registerSuggestEvent: function () {
            var me = this;

            me._on($('div', me._$suggestBox), 'click', $.proxy(me.onClick, me));
        },

        onClick: function(event){
            var me = this, $target = $(event.target), number = $target.attr('data-number');

            me._$searchBox.val(number);
        },

        onChange: function (event) {
            var me = this,
                searchValue = me._$searchBox.val();

            console.log(searchValue);

            if(searchValue.length >= 3){
                $.ajax({
                    url: me.opts.searchUrl + searchValue,
                    success: function (response) {
                        me._$suggestBox.empty();
                        me._$suggestBox.addClass(me.opts.hiddenClass);

                        if(response.ordernumbers.length > 0){

                            response.ordernumbers.forEach(function (number) {
                                me._$suggestBox.append($('<div data-number="'+number+'">'+number+'</div>'))
                            });

                            me.registerSuggestEvent();
                            me._$suggestBox.removeClass(me.opts.hiddenClass);
                        }
                    }
                })
            }
        },

        /**
         * Remove all listeners, classes and values from this plugin.
         */
        destroy: function () {
            var me = this;

            me._destroy();
        }
    });
})(jQuery, window);

/**
 * Call the plugin when the shop is ready
 */
$(function () {
    window.StateManager.addPlugin(
        '*[data-product-suggest="true"]',
        'mojProductSuggest'
    );
});