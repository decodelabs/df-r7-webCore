if(typeof require == 'undefined') {
    throw new Error('Require.js has not been loaded');
}

(function(require) {
    var viewData = document.getElementById('custom-view-data'),
        baseUrl = viewData.getAttribute('data-base'),
        cts = viewData.getAttribute('data-cts'),
        theme = document.body.getAttribute('data-theme');

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

    require([baseUrl + 'df-kit/require-config.js?theme=' + theme], function(config) {
        if(!config || !config.paths || !config.paths.jquery || !config.paths.underscore) {
            throw new Error('df-kit requires jquery and underscore');
        }

        if(!config.shim) config.shim = {};
        config.shim.underscore = {exports: '_'};

        require.config(config);

        require([
            'df-kit/core'
        ], function(Core) {
            Core.baseUrl = baseUrl;
            Core.cts = cts;
            Core.init();

            var modules = document.body.getAttribute('data-require');
            if(modules) require(modules.split(' '));
        });
    });
})(require);