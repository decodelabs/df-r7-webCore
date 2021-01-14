define([
    'jquery',
    'df-kit/core'
], function ($, Core) {
    // Form event
    $('input[data-formevent]:enabled').bind('keypress.formEvent', function (e) {
        if (e.keyCode == '13') {
            var s = $('#form-hidden-activeFormEvent');
            var f = $(this).parents('form');
            var event = $(this).attr('data-formevent');

            if (!s.length && event != 'default') {
                f.prepend('<input type="hidden" id="form-hidden-activeFormEvent" name="formEvent" />');
                s = $('#form-hidden-activeFormEvent');
            }

            if (s.length) {
                s.val(event);
            }

            e.preventDefault();
            f.submit();
        }
    });

    // Scroll to first error
    if ($(".w.field .list.errors").length) {
        $('html, body').animate({
            scrollTop: $(".w.field .list.errors").first().parent().offset().top
        }, 200);
    }


    // Record admin selects
    $(document).on('click', 'th.field-select', function (e) {
        var on = !$(this).hasClass('checked');
        $(this).closest('table').find('td.field-select input[type=checkbox]').prop('checked', on);
        $(this).toggleClass('checked', on);
    });

    $(document).on('click', '.scaffold.with-selected a', function (e) {
        if ($(this).hasClass('disabled')) {
            e.preventDefault();
            return;
        }

        var url = $(this).attr('data-href'),
            $fs = $(this).closest('fieldset'),
            ids = $fs.data('selectIds');

        if (!url) {
            $(this).attr('data-href', url = $(this).attr('href'));
        }

        url = Core.updateQueryStringParameter(url, 'selected', ids.join(','));
        $(this).attr('href', url);
    });

    var updateSelection = function () {
        var $list = $(this).closest('.list.collection'),
            $checked = $list.find('input.checkbox.selection:checked'),
            $fs = $list.siblings('.scaffold.with-selected'),
            ids = [];

        $fs.find('a').toggleClass('disabled', !$checked.length);

        $checked.each(function (i) {
            ids[i] = $(this).val();
        });

        $fs.data('selectIds', ids);
    };

    $('.list.collection > table').each(updateSelection);
    $(document).on('change', '.list.collection input.checkbox.selection', updateSelection);


    // Action tooltips
    $('.field-actions a.hasIcon').each(function () {
        $(this).attr('title', $(this).text());
    });
});
