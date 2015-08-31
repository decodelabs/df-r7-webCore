if(typeof require == 'undefined') {
    throw new Error('Require.js has not been loaded');
}

(function(require) {
    var viewData = document.getElementById('custom-view-data'),
        baseUrl = viewData.getAttribute('data-base'),
        cts = viewData.getAttribute('data-cts');

    require.config({
        baseUrl: baseUrl + 'assets/',
        paths: {
            'app': './app',
            'theme': '../theme',
            'df-kit': './lib/df-kit'
        },
        urlArgs: cts ? 'cts=' + cts : null
    });

    require(['require.config'], function(config) {
        if(!config.paths || !config.paths.jquery || !config.paths.underscore) {
            throw new Error('df-kit requires jquery and underscore');
        }

        if(!config.shim) config.shim = {};
        config.shim.underscore = {exports: '_'};

        require.config(config);

        require([
            'df-kit/core',
            'df-kit/modal'
        ], function(core, modal) {
            core.baseUrl = baseUrl;
            core.cts = cts;
            core.init();

            var modules = document.body.getAttribute('data-require');
            if(modules) require(modules.split(' '));
        });
    });
})(require);