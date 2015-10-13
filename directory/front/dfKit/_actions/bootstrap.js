(function(require) {
    var viewData = document.getElementById('custom-view-data'),
        baseUrl = viewData.getAttribute('data-base'),
        cts = viewData.getAttribute('data-cts');

    require.config({
        baseUrl: baseUrl + 'assets/',
        paths: {
            'app': './app',
            'theme': '../theme',
            'df-kit': './lib/df-kit',
            'df-kit-control': '../df-kit'
        },
        urlArgs: cts ? 'cts=' + cts : null
    });

    require(['require.config'], function(config) {
        if(!config || !config.paths || !config.paths.jquery || !config.paths.underscore) {
            throw new Error('df-kit requires jquery and underscore');
        }

        if(!config.shim) config.shim = {};
        config.shim.underscore = {exports: '_'};
        require.config(config);

        require([
            'jquery',
            'df-kit/core'
        ], function(Core) {
            $(function() {
                var modules = $('body').data('require');
                if(modules) require(modules.split(' '));
            });
        });
    });
})(require);