define([
    'jquery'
], function($) {
    return {
        baseUrl: null,
        cts: null,
        location: null,
        layout: null,

        init: function() {
            if(!this.cts) {
                var viewData = document.getElementById('custom-view-data');
                this.baseUrl = viewData.getAttribute('data-base');
                this.cts = viewData.getAttribute('data-cts');
            }

            var $body = $(document.body);
            this.location = $body.data('location');
            this.layout = $body.data('layout');
        },

        call: function(callback, data) {
            if(typeof(callback) == 'function') {
                callback.call(this, data);
            }
        },

        component: function(obj) {
            if(typeof obj.init == 'function') {
                this.call(obj.init);
            }

            return obj;
        },

        isUrlExternal: function(url) {
            var domain = function(url) {
                return url.replace('http://','').replace('https://','').split('/')[0];
            };

            return domain(location.href) !== domain(url);
        },

        openWindow: function(url, title, width, height) {
            var left = (screen.width / 2) - (width / 2),
                top = (screen.height / 2) - (height / 2);
            window.open(url, title, 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height='+height+',width='+width+',top='+top+',left='+left);
        }
    };
});