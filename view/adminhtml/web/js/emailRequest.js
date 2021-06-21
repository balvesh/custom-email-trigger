define([
    "jquery",
    "Magento_Ui/js/modal/alert",
    "mage/translate",
    "jquery/ui"
], function ($, alert, $t) {
    "use strict";

    $.widget('Learning.customEmail', {
        options: {
            ajaxUrl: '',
            emailButton: '#row_triggercustomemail_general_sent',
            template: '#triggercustomemail_general_template_identifier',
        },
        _create: function () {
            var self = this;

            $(this.options.emailButton).click(function (e) {
                e.preventDefault();
                if (self.element.val()) {
                    self._ajaxSubmit();
                }
            });
        },

        _ajaxSubmit: function () {
            $.ajax({
                url: this.options.ajaxUrl,
                data: {
                    from: 'general',
                    template: $(this.options.template).val(),
                    to: this.element.val(),
                },
                dataType: 'json',
                showLoader: true,
                success: function (result) {
                    alert({
                        title: result.status ? $t('Success') : $t('Error'),
                        content: result.content
                    });
                }
            });
        }
    });

    return $.Learning.customEmail;
});