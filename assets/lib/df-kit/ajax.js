define([
    'jquery',
    'df-kit/core'
], function($, Core) {
    var ajax;
    return ajax = Core.component({
        _clients: {},

        init: function() {
            $(document).on('click', '.ajax-content button', function(e) {
                var event = $(this).val(), name = $(this).attr('name');
                $('#form-activeButton').remove();
                $(this).closest('form').append('<input type="hidden" name="'+name+'" id="form-activeButton" value="'+event+'" />');
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
                Core.rootUrl+'content/element?element='+slug,
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
                        //request.originalUrl = request.url;
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
            request.url = request.originalUrl = url;

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
            lastResponse: null,


            construct: function(element) {
                var _this = this;
                this.clear();
                this.id = _.uniqueId('a');
                this.$element = $(element);
                this.$element.attr('data-ajax-client', this.id);

                this.$element.on('click', 'a[href]:not([class*=\'-close\'],.local,.local a,[target],.pushy,.modal)', function(e) {
                    if(!Core.isUrlExternal($(this).attr('href'))) {
                        _this.onLinkClick(e);
                    }
                });

                this.$element.on('submit', '.w-form', function(e) {
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
                this.lastRequest = null;
                return this;
            },

            destroy: function() {
                this.clear();
                delete ajax._clients[this.id];
            },

            _normalizeUrl: function(url) {
                return url.replace('.ajax', '').replace(/(\?|\&)\_\=[0-9]+/, '');
            },

            sendRequest: function(request, callback) {
                var _this = this,
                    lastRequest = this.getLastRequest();

                if(!lastRequest
                || (lastRequest.originalUrl !== request.originalUrl
                 && _this._normalizeUrl(lastRequest.originalUrl) !== _this._normalizeUrl(request.originalUrl))) {
                    this.requestStack.push(request);
                }

                ajax.sendRequest(request, function(response, request) {
                    _this.lastResponse = response;
                    response = _this.normalizeResponse(response, request);

                    if(!_this.initialUrl) {
                        _this.initialUrl = _this._normalizeUrl(request.originalUrl);
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
                            _this.trigger('form:forceRedirect', response);
                            _this.initialUrl = null;
                            return _this.get(response.redirect, response.request);
                        }

                        if(response.request.formEvent === 'cancel') {
                            _this.trigger('form:cancel', response);
                        }

                        _this.trigger('form:complete', response);

                        var isInitial = _this._normalizeUrl(response.request.originalUrl) === _this._normalizeUrl(_this.initialUrl);

                        if(isInitial) {
                            _this.trigger('form:completeInitial', response);
                        }
                    }

                    if(response.reload === true) {
                        location.reload();
                    }

                    if(response.handled) {
                        return;
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

                    _this.trigger('content:load', response);

                    _this.$element
                        .addClass('ajax-content')
                        .html(response.content);

                    _this.trigger('content:show', response);
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
                this.get(url, request);
            },

            onFormSubmit: function(e) {
                e.preventDefault();

                var _this = this,
                    $form = $(e.target),
                    formEvent,
                    lastRequest = this.getLastRequest(),
                    request = {},
                    $trigger;

                if(lastRequest) {
                    request = _.clone(lastRequest);
                }

                request.data = $form.serializeArray();
                request.formEvent = null;

                if(e.originalEvent && e.originalEvent.explicitOriginalTarget && e.originalEvent.explicitOriginalTarget.tagName == 'BUTTON') {
                    $trigger = $(e.originalEvent.explicitOriginalTarget);
                } else if(document.activeElement && document.activeElement.tagName == 'BUTTON') {
                    $trigger = $(document.activeElement);
                } else {
                    $trigger = $('#form-activeButton');
                }

                if($trigger) {
                    var triggerName = $trigger.attr('name'),
                        triggerVal = $trigger.val();

                    if(triggerName == 'formEvent') {
                        request.formEvent = formEvent = triggerVal;
                    } else {
                        request.data.push({name:triggerName, value:triggerVal});
                    }
                }

                if(formEvent && !$form.find('[name=formEvent]').length) {
                    request.data.push({name:'formEvent', value:formEvent});
                }

                this.post($form.attr('action'), request);
            }
        })
    });
});