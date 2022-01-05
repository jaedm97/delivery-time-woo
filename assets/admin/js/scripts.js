/**
 * Admin Scripts
 */

(function ($, window, document) {
    "use strict";

    $(document).on('ready', function () {

        let DTWooDisplayColor = $('#dtwoo_display_color');

        if (DTWooDisplayColor.length > 0) {
            DTWooDisplayColor.wpColorPicker();
        }
    });

})(jQuery, window, document);







