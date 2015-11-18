define([
    'jquery',
    'df-kit/core'
], function($, Core) {
    return Core.component({
        init: function() {
            $('.widget-flashList .widget-flashMessage').each(function() {
                $(this).prepend('<a class="close"><span data-icon="&#xe00b;" aria-hidden="true"></span></a>');
            }).find('a.close').click(function() {
                $(this).parent().addClass('out').fadeOut(200, function() {
                    $(this).remove();
                });
            });
        }
    });
});