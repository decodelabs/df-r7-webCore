define([
    'jquery',
    'df-kit/core'
], function($, Core) {
    return Core.component({
        init: function() {
            $('div.w.list.flash .w.flashMessage').each(function() {
                $(this).prepend('<a class="close"><span data-icon="&#xe00b;" aria-hidden="true"></span></a>');
            }).find('a.close').click(function(e) {
                e.preventDefault();

                $(this).parent().addClass('out').fadeOut(200, function() {
                    var $parent = $(this).parent();
                    $(this).remove();

                    if(!$parent.find('.w.flashMessage').length) {
                        $parent.remove();
                    }
                });
            });
        }
    });
});
