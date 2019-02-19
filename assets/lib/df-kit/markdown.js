define([
    'jquery',
    'lib/simplemde/simplemde.min'
], function($, mde) {
    $('textarea[data-editor=markdown]').each(function() {
        new mde({ element: $(this)[0] });
    });
});
