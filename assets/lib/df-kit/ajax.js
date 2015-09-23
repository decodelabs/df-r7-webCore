define([
    'jquery',
    'df-kit/core'
], function($, Core) {
    var ajax;
    return ajax = Core.component({
        _clients: {},

        init: function() {
            $(document).on('click', '.ajax-content .widget-eventButton', function(e) {
                var event = $(this).val();
                $('#form-hidden-activeFormEvent').remove();
                $(this).closest('form').append('<input type="hidden" name="formEvent" id="form-hidden-activeFormEvent" value="'+event+'" />');
            });
        },

        loadElement: function(element, url, options) {
            var client, clientId = $(element).attr('data-ajax-client');

            if(clientId && this._clients[clientId]) {
                client = this._clients[clientId];
            } else {
                client = new this.Client(element);
                this._clients[client.id] = client;
            }

            client.get(url, options);
            return client;
        },

        get: function(url, options, callback) {
            return this.sendRequest(
                this.normalizeRequest('GET', url, options), 
                callback
            );
        },

        getElement: function(slug, callback) {
            return this.get(
                Core.baseUrl+'content/element?element='+slug,
                null, callback
            );
        },

        post: function(url, options, callback) {
            return this.sendRequest(
                this.normalizeRequest('POST', url, options), 
                callback
            );
        },

        sendRequest: function(request, callback) {
            var _this = this;
            if(!request.method) request.method = 'GET';
            if(!request.data) request.data = {};

            if(!request.url) {
                throw new Error('No URL defined for ajax request');
            }

            $.ajax({
                url: request.url,
                data: request.data,
                type: request.method,
                headers: {
                    'x-ajax-request-type' : request.type,
                    'x-ajax-request-source': request.source
                },
                success: function(response, status, xhr) {
                    var url = xhr.getResponseHeader('X-Response-Url');

                    if(url) {
                        request.originalUrl = request.url;
                        request.url = url;
                    }

                    if(callback) callback(response, request);
                }
            }).fail(function(e) {
                _this.trigger('fail', request);
                console.log('Ajax call failed...', request.url, e);
            });
        },

        normalizeRequest: function(method, url, request) {
            request || (request = {});
            request.method = String(method).toUpperCase();
            request.url = url;

            if(!request.data) request.data = {};
            if(!request.type) request.type = 'ajax';
            if(!request.source) request.source = 'ajax';

            return request;
        },

        Client: Core.class({
            id: null,
            $element: null,
            initialUrl: null,
            requestStack: null,


            construct: function(element) {
                var _this = this;
                this.clear();
                this.id = _.uniqueId('a');
                this.$element = $(element);
                this.$element.attr('data-ajax-client', this.id);

                this.$element.on('click', 'a[href]:not([class*=\'-close\'],.local,[target],.pushy,.modal)', function(e) {
                    if(!Core.isUrlExternal($(this).attr('href'))) {
                        _this.onLinkClick(e);
                    }
                });

                this.$element.on('submit', '.widget-form', function(e) {
                    _this.onFormSubmit(e);
                });
            },


            getFirstRequest: function() {
                if(!this.requestStack.length) return null;
                return this.requestStack[0];
            },

            getLastRequest: function() {
                if(!this.requestStack.length) return null;
                return this.requestStack[this.requestStack.length - 1];
            },


            get: function(url, options) {
                return this.sendRequest(ajax.normalizeRequest('get', url, options));
            },

            post: function(url, options) {
                return this.sendRequest(ajax.normalizeRequest('post', url, options));
            },

            home: function() {
                var request = this.getFirstRequest();
                this.requestStack = [];
                return this.sendRequest(request);
            },

            back: function() {
                this.requestStack.pop();
                var request = this.getLastRequest();

                if(request) {
                    return this.sendRequest(request);
                }
            },

            clear: function() {
                this.requestStack = [];
                this.initialUrl = null;
                return this;
            },

            destroy: function() {
                this.clear();
                delete ajax._clients[this.id];
            },

            sendRequest: function(request, callback) {
                var _this = this,
                    lastRequest = this.getLastRequest();

                if(!lastRequest
                || (lastRequest.url !== request.url
                 && lastRequest.url.replace('.ajax', '') !== request.url.replace('.ajax', ''))) {
                    this.requestStack.push(request);
                }

                ajax.sendRequest(request, function(response, request) {
                    response = _this.normalizeResponse(response, request);

                    if(!_this.initialUrl) {
                        _this.initialUrl = request.url.replace('.ajax', '');
                    }

                    _this.trigger('response', response);

                    ajax.trigger('elementResponse', {
                        client: this,
                        response: response
                    });

                    if(response.isComplete) {
                        if(response.request.formEvent !== 'cancel'
                        && response.forceRedirect
                        && response.redirect !== null) {
                            _this.trigger('form:cancel', response);
                            return _this.get(response.redirect, response.request);
                        }

                        _this.trigger('form:complete', response);

                        if(response.request.url.replace('.ajax', '') === _this.initialUrl) {
                            var request = _this.getFirstRequest();

                            if(request && request.formComplete) {
                                return request.formComplete(response);
                            }
                        }

                        if(response.request.formComplete) {
                            return request.formComplete(response);
                        }
                    }

                    if(response.redirect !== null) {
                        _this.trigger('redirect', response);
                        return _this.get(response.redirect, _.clone(response.request));
                    }

                    if(typeof callback === 'function') {
                        if(false === callback(response)) {
                            return;
                        }
                    }

                    _this.$element
                        .addClass('ajax-content')
                        .html(response.content);

                    if(typeof response.request.onLoad === 'function') {
                        response.request.onLoad(response);
                    }
                });
            },

            normalizeResponse: function(response, request) {
                if(typeof response !== 'object') {
                    response = {content: response};
                }

                response.request = request;
                if(!response.redirect) response.redirect = null;

                return response;
            },



            onLinkClick: function(e) {
                var _this = this,
                    url = $(e.target).closest('a').attr('href'),
                    lastRequest = this.getLastRequest();
                    request = {};

                if(!url) return;
                if(lastRequest) request = _.clone(lastRequest);

                e.preventDefault();

                request.formComplete = function(response) {
                    if(response.request.url.replace('.ajax', '') === _this.initialUrl) {
                        var firstRequest = _this.getFirstRequest();

                        if(firstRequest && firstRequest.formComplete) {
                            return firstRequest.formComplete(response);
                        }
                    }

                    return _this.back();
                };

                this.get(url, request);
            },

            onFormSubmit: function(e) {
                e.preventDefault();

                var _this = this,
                    $form = $(e.target),
                    formEvent,
                    lastRequest = this.getLastRequest();
                    request = {};

                if(lastRequest) request = _.clone(lastRequest);
                request.data = $form.serializeArray();
                request.formEvent = null;

                if(e.originalEvent && e.originalEvent.explicitOriginalTarget && e.originalEvent.explicitOriginalTarget.tagName == 'BUTTON') {
                    request.formEvent = formEvent = $(e.originalEvent.explicitOriginalTarget).val();
                } else if(document.activeElement && document.activeElement.tagName == 'BUTTON') {
                    request.formEvent = formEvent = $(document.activeElement).val();
                } else {
                    request.formEvent = $('#form-hidden-activeFormEvent').val();
                }

                if(formEvent) {
                    request.data.push({name:'formEvent', value:formEvent});
                }

                this.post($form.attr('action'), request);
            }
        })
    });
});