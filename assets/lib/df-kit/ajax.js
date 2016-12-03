define([
    'jquery',
    'df-kit/core'
], function($, Core) {
    var Ajax;
    return Ajax = Core.component({
        _clients: {},

        init: function() {
            $(document).on('click', '.ajax-content button', function(e) {
                if($(this).closest('form.global').length && $(this).closest('form.global').closest('.ajax-content').length) {
                    return;
                }

                var event = $(this).val(), name = $(this).attr('name');
                $('#form-activeButton').remove();
                $(this).closest('form').append('<input type="hidden" name="'+name+'" id="form-activeButton" value="'+event+'" />');
            });
        },


    // Basic requests
        get: function(url, options) {
            return this.send(this.normalizeRequest('GET', url, options));
        },

        getElement: function(slug) {
            return this.get(Core.rootUrl+'content/element?element='+slug);
        },

        post: function(url, options) {
            return this.send(this.normalizeRequest('POST', url, options));
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

        send: function(request) {
            var _this = this;
            if(!request.method) request.method = 'GET';
            if(!request.data) request.data = {};

            if(!request.url) {
                throw new Error('No URL defined for ajax request');
            }

            var deferred = $.Deferred();

            $.ajax({
                url: request.url,
                data: request.data,
                type: request.method,
                headers: {
                    'x-ajax-request-type' : request.type,
                    'x-ajax-request-source': request.source
                }
            }).done(function(response, status, xhr) {
                var url = xhr.getResponseHeader('X-Response-Url');
                if(url) request.url = url;
                deferred.resolve(response, request);
            }).fail(deferred.reject);

            return deferred.promise();
        },





    // Client shortcuts
        loadInto: function(element, url, options) {
            options = options || {}
            options.live = false;

            return this.embedInto(element, url, options);
        },

        embedInto: function(element, url, options) {
            var client = this.getClient(element),
                deferred = $.Deferred();

            client.clear();
            deferred.notify(client);

            client.get(url, options)
                .fail(deferred.reject)
                .done(deferred.resolve);

            return deferred.promise();
        },

        getClient: function(element) {
            var client,
                clientId = $(element).attr('data-ajax-client')

            if(clientId && this._clients[clientId]) {
                client = this._clients[clientId];
            } else {
                client = new this.Client(element);
                this._clients[client.id] = client;
            }

            return client;
        },



    // Client
        Client: Core.class({
            id: null,
            $element: null,
            initialUrl: null,
            requestStack: null,
            lastResponse: null,

            attr: {
                link: 'a[href]:not([class*=\'-close\'],.global,.global *,[target],.pushy,.modal),[data-href]:not(.global,.global *)',
                form: 'form:not(.global,.global form)'
            },


            construct: function(element) {
                var _this = this;
                this.clear();
                this.id = _.uniqueId('a');
                this.$element = $(element);
                this.$element.attr('data-ajax-client', this.id);
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
                return this.send(Ajax.normalizeRequest('get', url, options));
            },

            post: function(url, options) {
                return this.send(Ajax.normalizeRequest('post', url, options));
            },

            home: function() {
                var request = this.getFirstRequest();
                this.requestStack = [];
                return this.send(request);
            },

            back: function() {
                this.requestStack.pop();
                var request = this.getLastRequest();

                if(request) {
                    return this.send(request);
                }
            },

            refresh: function() {
                var request = this.requestStack.pop();

                if(request) {
                    return this.send(request);
                }
            },

            clear: function() {
                this.requestStack = [];
                this.initialUrl = null;
                this.lastResponse = null;
                return this;
            },

            destroy: function() {
                this.clear();
                this.disableLive();
                delete Ajax._clients[this.id];
            },

            _normalizeUrl: function(url) {
                return url.replace('.ajax', '').replace(/(\?|\&)\_\=[0-9]+/, '');
            },

            send: function(request) {
                var _this = this,
                    lastRequest = this.getLastRequest(),
                    deferred = $.Deferred();

                if(!lastRequest
                || (lastRequest.originalUrl !== request.originalUrl
                 && _this._normalizeUrl(lastRequest.originalUrl) !== _this._normalizeUrl(request.originalUrl))) {
                    this.requestStack.push(request);
                }

                Ajax.send(
                    request
                ).fail(
                    deferred.reject
                ).done(function(response, request) {
                    _this.lastResponse = response;
                    response = _this.normalizeResponse(response, request);

                    if(!_this.initialUrl) {
                        _this.initialUrl = _this._normalizeUrl(request.originalUrl);
                    }

                    _this.trigger('response', response);

                    Ajax.trigger('elementResponse', {
                        client: this,
                        response: response
                    });

                    deferred.notify(response, request);


                    // Form complete
                    if(response.isComplete) {
                        // Forced redirect
                        if(response.request.formEvent !== 'cancel'
                        && response.forceRedirect
                        && response.redirect !== null) {
                            _this.trigger('form:forceRedirect', response);
                            _this.initialUrl = null;
                            return _this.get(response.redirect, response.request);
                        }

                        // Form cancel
                        if(response.request.formEvent === 'cancel') {
                            _this.trigger('form:cancel', response);
                        }

                        // Complete
                        _this.trigger('form:complete', response);

                        var isInitial = _this._normalizeUrl(response.request.originalUrl) === _this._normalizeUrl(_this.initialUrl);

                        if(isInitial) {
                            _this.trigger('form:completeInitial', response);
                        }
                    }


                    // Refresh everything
                    if(response.reload === true) {
                        location.reload();
                    }

                    // Handled by event
                    if(response.handled) {
                        deferred.resolve(response);
                        return;
                    }

                    // Simple redirect
                    if(response.redirect !== null) {
                        _this.trigger('redirect', response);
                        return _this.get(response.redirect, _.clone(response.request));
                    }

                    // Handle it
                    _this.trigger('content:load', response);
                    _this.disableLive();

                    if(request.live !== false) {
                        _this.enableLive();
                    }

                    _this.$element
                        .addClass('ajax-content')
                        .html(response.content);

                    _this.trigger('content:show', response);
                    deferred.resolve(response, request);
                });

                return deferred;
            },


            enableLive: function() {
                var _this = this;

                this.$element.on('click', this.attr.link, function(e) {
                    _this.onLinkClick(e);
                });

                this.$element.on('submit', this.attr.form, function(e) {
                    _this.onFormSubmit(e);
                });
            },

            disableLive: function() {
                this.$element.off('click', this.attr.link);
                this.$element.off('submit', this.attr.form);
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

                if(!url) url = $(e.target).closest('[data-href]').attr('data-href');
                if(!url || Core.isUrlExternal(url)) return;
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