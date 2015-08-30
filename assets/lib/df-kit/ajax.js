define([
    'jquery',
    'df-kit/core'
], function($, core) {
    return core.component({
        _lastRequest: null,

        load: function(element, url, data) {
            var _this = this;
            data = data ? data : {};
            data.$element = $(element);

            this.get(url, data, function(output) {
                _this.handleResponse(output);
            });
        },

        get: function(url, data, callback) {
            var _this = this;
            data = this.normalizeRequest(url, data);

            $.ajax({
                url: url,
                type: 'GET',
                headers: {
                    'x-ajax-request-type' : data.requestType,
                    'x-ajax-request-source': data.requestSource
                },
                success: function(response) {
                    _this.normalizeResponse(response, data, callback);
                }
            }).fail(function(e) {
                console.log('Ajax GET failed...', url, e);
            });
        },

        post: function(url, data, callback) {
            var _this = this;
            data = this.normalizeRequest(url, data);

            $.ajax({
                data: $.param(data.form),
                url: url,
                type: 'POST',
                headers: {
                    'x-ajax-request-type' : data.requestType,
                    'x-ajax-request-source': data.requestSource
                },
                success: function(response) {
                    _this.normalizeResponse(response, data, callback);
                }
            }).fail(function(e) {
                console.log('Ajax POST failed...', url, e);
            });
        },

        normalizeRequest: function(url, data) {
            data = data ? data : {};
            data.url = url;

            if(!data.requestType) {
                data.requestType = 'ajax';
            }

            if(!data.requestSource) {
                data.requestSource = 'ajax';
            }

            this._lastRequest = data;
            return data;
        },

        normalizeResponse: function(response, request, callback) {
            if(typeof response !== 'object') {
                response = {
                    content: response
                };
            }

            response.request = request;

            if(response.isComplete && request.formComplete) {
                response.request.formComplete(response);
                return;
            }

            if(response.redirect && response.redirect !== null) {
                return this.get(response.redirect, request, callback);
            }

            core.call(callback, response);
        },

        handleResponse: function(response) {
            if(!response) {
                return;
            }
            
            response.request.$element.html(response.content);

            if(response.request.onLoad && response.request.onLoad !== null) {
                response.request.onLoad(response);
            }
        }
    });
});