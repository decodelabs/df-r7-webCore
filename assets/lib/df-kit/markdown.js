define([
    'jquery',
    'assets/lib/simplemde/simplemde.min.js'
], function ($, mde) {
    $('textarea[data-editor=markdown]').each(function () {
        new mde({
            element: $(this)[0]
        });
    });
});
