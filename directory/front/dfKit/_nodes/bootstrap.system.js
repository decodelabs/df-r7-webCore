define([
    'jquery',
    'df-kit/core'
], function ($, core) {
    var modules = $('body').data('import');
    if (!modules) return;

    modules = modules.split(' ');

    let viewData = document.getElementById('custom-view-data'),
        baseUrl = viewData.getAttribute('data-base'),
        rootUrl = viewData.getAttribute('data-root'),
        cts = viewData.getAttribute('data-cts');

    for (let i in modules) {
        System.import(modules[i] + '.js?cts=' + cts);
    }
});
