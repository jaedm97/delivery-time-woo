/**
 * Front Script
 */

(function ($, window, document, pluginObject) {
    "use strict";

    $(document).on('click', '.dtwoo-message-wrap.has-delivery-desc p', function () {

        let DTWooMessage = $(this),
            DTWooMessageWrap = DTWooMessage.parent(),
            productID = DTWooMessageWrap.data('product-id');

        if (typeof productID === 'undefined') {
            return;
        }

        if (DTWooMessageWrap.hasClass('delivery-desc-loaded')) {

            DTWooMessageWrap.find('.dtwoo-delivery-desc').slideUp();

            setTimeout(function () {
                DTWooMessageWrap.removeClass('delivery-desc-loaded');
                DTWooMessageWrap.find('.dtwoo-delivery-desc').remove();
            }, 500);

            return false;
        }

        DTWooMessageWrap.addClass('loading');

        $.ajax({
            type: 'POST',
            context: this,
            url: pluginObject.ajaxURL,
            data: {
                'action': 'dtwoo_get_delivery_desc',
                'product_id': productID,
            },
            success: function (response) {
                if (response.success) {

                    DTWooMessageWrap.append(response.data).find('.dtwoo-delivery-desc').hide();

                    setTimeout(function () {
                        DTWooMessageWrap.removeClass('loading');
                        DTWooMessageWrap.addClass('delivery-desc-loaded').find('.dtwoo-delivery-desc').slideDown();
                    }, 500);
                }
            }
        });

        return false;
    });

})(jQuery, window, document, dtwoo_object);







