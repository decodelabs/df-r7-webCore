$(function() {
    // Form event
    $('input[data-formevent]:enabled').bind('keypress.formEvent', function(e) {
        if(e.keyCode == '13') {
            var s = $('#form-hidden-activeFormEvent');
            var f = $(this).parents('form');
            var event = $(this).attr('data-formevent');

            if(!s.length && event != 'default') {
                f.prepend('<input type="hidden" id="form-hidden-activeFormEvent" name="formEvent" />');
                s = $('#form-hidden-activeFormEvent');
            }

            if(s.length) {
                s.val(event);
            }

            e.preventDefault();
            f.submit();
        }
    });

    // Scroll to first error
    if($(".w.field .list.errors").length) {
        $('html, body').animate({
            scrollTop: $(".w.field .list.errors").first().parent().offset().top
        }, 200);
    }
});
