;(function ($, window) {
    'use strict';

    $.plugin('mojProductSuggest', {
        defaults: {
            'searchUrl': '/widgets/DashProductSearch/searchProduct',
            'suggestSelector': '.suggest--container',
            'hiddenClass': 'is--hidden'
        },

        init: function () {
            var me = this;

            me.applyDataAttributes();

            me._$searchBox = $('input', me.$el);
            me._$suggestBox = $(me.opts.suggestSelector, me.$el);

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

        onClick: function (event) {
            var me = this, $target = $(event.target), number = $target.attr('data-number');

            me._$searchBox.val(number);
            me.hideSuggest();
        },

        onChange: function (event) {
            var me = this,
                searchValue = me._$searchBox.val();

            if (searchValue.length >= 3) {
                $.ajax({
                    url: me.opts.searchUrl + '?search=' + searchValue,
                    success: function (response) {
                        me.hideSuggest();

                        if (response.ordernumbers.length > 0) {

                            response.ordernumbers.forEach(function (number) {
                                me._$suggestBox.append($('<div data-number="' + number + '">' + number + '</div>'))
                            });

                            me.registerSuggestEvent();
                            me._$suggestBox.removeClass(me.opts.hiddenClass);
                        }
                    }
                })
            }
        },

        hideSuggest: function () {
            var me = this;

            me._$suggestBox.empty();
            me._$suggestBox.addClass(me.opts.hiddenClass);
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