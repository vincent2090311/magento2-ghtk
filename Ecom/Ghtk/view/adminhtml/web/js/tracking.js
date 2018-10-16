define([
    "jquery",
    'mage/translate'
], function ($, $t) {
    "use strict";

    $.widget('ghtk.tracking', {
        options: {
            ajaxUrl: null
        },

        /**
         * Bind a click handler on the widget's element.
         * @private
         */
        _create: function() {
            this.element.on('click', $.proxy(this.trackingAction, this));
        },

        trackingAction: function(event) {
            $.ajax({
                url: this.options.ajaxUrl,
                data: this.getDataForAjaxRequest(),
                cache: true,
                dataType: 'json',
                beforeSend: $.proxy(this.beforeSend, this),
                success: $.proxy(this.success, this),
                error: $.proxy(this.displayResponse, this, $t('Sorry, something went wrong.')),
            });

        },

        getDataForAjaxRequest: function() {
            var data = {};
            data.tracking_label = this.options.tracking_label;
            data.form_key = window.FORM_KEY;
            return data;
        },

        beforeSend: function () {
            $('body').loader('show');
        },

        success: function (response) {
            if (response.success == true) {
                var result = '<table class="admin__table-secondary">';
                result += '<tbody>';
                $.each(response.data, function(key, value) {
                    result += '<tr>';
                    result += '<td>' + key + '</td>';
                    result += '<td>' + value + '</td>';
                    result += '</tr>';
                });
                result += '</tbody>';
                result += '</table>';
                this.displayResponse(result);
            } else {
                this.displayResponse(response.message);
            }
        },

        displayResponse: function (message) {
            $('body').loader('hide');
            $(this.options.resultContainter).html(message);
        }
    });

    return $.ghtk.tracking;
});