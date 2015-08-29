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
            'df-kit': './lib/df-kit'
        },
        urlArgs: cts ? 'cts=' + cts : null
    });

    require(['require.config'], function(config) {
        require.config(config);

        require(['df-kit/core'], function(core) {
            core.baseUrl = baseUrl;
            core.cts = cts;
            core.init();

            var modules = document.body.getAttribute('data-require');
            if(modules) require(modules.split(' '));
        });
    });
})(require);