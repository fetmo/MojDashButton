;(function ($, window) {
    'use strict';

    $.plugin('mojAddProductPosition', {
        defaults: {
            'triggerSelector': '.add--trigger',
            'productSelector': '.dash--product-box',
            'containerSelector': '.dash--products',
            'hiddenClass': 'is--hidden',
            'productMode': 1
        },

        position: 0,

        init: function () {
            var me = this;

            me.applyDataAttributes();

            me._$addButton = $(me.opts.triggerSelector, me.$el);
            me._$productBoxes = $(me.opts.productSelector, me.$el);
            me._$productContainer = $(me.opts.containerSelector, me.$el);

            me.position = me._$productBoxes.length;

            me._boxtemplate = window.addtemplate;

            me.hideButton();
            me.registerEvents();
        },

        registerEvents: function () {
            var me = this;

            me._on(me._$addButton, 'click', $.proxy(me.onClick, me));
        },

        onClick: function(){
            var me = this,
                box = me._boxtemplate,
                position = me.position;

            console.log(me.position);

            box = box.replace(new RegExp('###INDEX###', 'g'), position +1);
            box = box.replace(new RegExp('###POSITION###', 'g'), position);

            var $box = $(box);

            me._$productContainer.append($box);
            // $box.insertAfter(me._$productBoxes);
            me.position = position + 1;

            window.StateManager.updatePlugin(
                '*[data-product-suggest="true"]',
                'mojProductSuggest'
            );

            me.hideButton();
        },

        hideButton: function(){
            var me = this;

            if(me.opts.productMode == 1 && me.position > 0){
                me._$addButton.addClass(me.opts.hiddenClass);
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
        '*[data-add-product-position="true"]',
        'mojAddProductPosition'
    );
});