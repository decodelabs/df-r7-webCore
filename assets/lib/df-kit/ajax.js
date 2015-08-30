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
        },



        onLinkClick: function(e, element, initialUrl) {
            var _this = this,
                href = $(e.target).closest('a').attr('href'),
                data = this._lastRequest ? this._lastRequest : {};
            if(!href) return;

            e.preventDefault();

            this.load(element, href, {
                requestType: 'ajax',
                requestSource: 'modal',
                formComplete: function(response) {
                    if(response.request.formEvent != 'cancel'
                    && response.forceRedirect
                    && response.redirect !== null) {
                        return this.load(element, response.redirect, {
                            requestSource: 'modal'
                        });
                    }

                    this.load(element, initialUrl);
                }
            });
        },

        onFormSubmit: function(e, element) {
            e.preventDefault();

            var $form = $(e.target),
                formEvent,
                data = this._lastRequest ? this._lastRequest : {};

            data.form = $form.serializeArray();
            data.formEvent = null;

            if(e.originalEvent && e.originalEvent.explicitOriginalTarget && e.originalEvent.explicitOriginalTarget.tagName == 'BUTTON') {
                data.formEvent = formEvent = $(e.originalEvent.explicitOriginalTarget).val();
            } else if(document.activeElement && document.activeElement.tagName == 'BUTTON') {
                data.formEvent = formEvent = $(document.activeElement).val();
            } else {
                data.formEvent = $('#form-hidden-activeFormEvent').val();
            }

            if(formEvent) {
                data.form.push({name:'formEvent', value:formEvent});
            }

            if(!data.$element) {
                data.$element = element ? $(element) : $form;
            }

            data.requestSource = 'modal';

            this.post($form.attr('action'), data, function(output) {
                this.handleResponse(output);
            });
        }
    });
});