window.dfKit = {

    baseUrl: null,
    cts: null,
    location: null,
    layout: null,

    _isInit: false,

    init: function() {
        if(this._isInit) return;
        $cvd = $('#custom-view-data');
        this.baseUrl = $cvd.data('base');
        this.cts = $cvd.data('cts');
        this.location = $(document.body).data('location');
        this.layout = $(document.body).data('layout');
        this._isInit = true;



        $(document).on('submit', '.widget-form', function(e) {
            $(this).find('.widget-eventButton').prop('disabled', true).addClass('disabled');
        });
    },

    ready: function(func) {
        this.init();

        $(function() {
            func();
        });
    },

    call: function(callback, data) {
        if(typeof(callback) == 'function') {
            callback.call(this, data);
        }
    },

    isUrlExternal: function(url) {
        var domain = function(url) {
            return url.replace('http://','').replace('https://','').split('/')[0];
        };

        return domain(location.href) !== domain(url);
    },

    openWindow: function(url, title, width, height) {
        var left = (screen.width / 2) - (width / 2);
        var top = (screen.height / 2) - (height / 2);
        window.open(url, title, 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height='+height+',width='+width+',top='+top+',left='+left);
    },

    ajax: {
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

            dfKit.call(callback, response);
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
    },

    modal: {
        attr: {
            trigger: '.modal',
            container: '#modal-container',
            wrapper: '#modal-wrapper',
            content: '#modal-content'
        },

        _closeCallback: null,
        _isInit: false,
        _initialUrl: null,
        _overlayAction: 'close',
        _containerFadeTime: 200,
        _contentFadeTime: 0,

        init: function() {
            if(this._isInit) return;
            var _this = this;

            $(document).on('click', this.attr.trigger, function(e) {
                e.preventDefault();

                var modalClass = $(this).data('modal-class'),
                    href = $(this).data('modal-href'),
                    overlayAction = $(this).data('overlay-action');

                if(!href) {
                    href = $(this).attr('href');
                }

                _this.ajax(href, {
                    class: modalClass,
                    overlayAction: overlayAction
                });
            });

            $(document).on('click touchstart', '.modal-close', function(e) {
                if(e.target != e.currentTarget) return;
                e.preventDefault();
                _this.close();
            });

            $(document).on('click', _this.attr.container + ' a:not(.modal-close,.local,[target],'+ _this.attr.trigger + ')', function(e) {
                if(!dfKit.isUrlExternal($(this).attr('href'))) {
                    _this.onLinkClick(e);
                }
            });

            $(document).on('submit', _this.attr.container + ' .widget-form', function(e) {
                _this.onFormSubmit(e);
            });

            $(document).on('click', _this.attr.container + ' .widget-eventButton', function(e) {
                var event = $(this).val();
                $('#form-hidden-activeFormEvent').remove();
                $(this).closest('form').append('<input type="hidden" name="formEvent" id="form-hidden-activeFormEvent" value="'+event+'" />');
            });



            _this._isInit = true;
        },

        ajax: function(href, options) {
            var _this = this,
                callback = options.callback;

            _this.init();
            options = options || {};

            options.callback = function(data) {
                _this._initialUrl = href;

                dfKit.ajax.load(_this.attr.content, href, {
                    requestSource: 'modal',

                    formComplete: function(response) {
                        if(response.request.formEvent != 'cancel'
                        && response.forceRedirect
                        && response.redirect !== null) {
                            return dfKit.ajax.load(_this.attr.content, response.redirect, {
                                requestSource: 'modal'
                            });
                        }
                        
                        _this.close();
                    },
                    onLoad: function(response) {
                        dfKit.call(callback);
                    }
                });
            };

            _this.open('', options);
        },

        open: function(html, options) {
            var _this = this;
            _this.init();
            options = options || {};

            var $container = $(_this.attr.container),
                $modal, $overlay,
                builder = function() {
                    $modal = $(_this.attr.content).hide().html(html);
                    $overlay = $(_this.attr.container + ',' + _this.attr.wrapper).removeClass('modal-close');
                    _this._closeCallback = options.closeCallback;
                    if(options.class) $modal.addClass(options.class);

                    dfKit.call(options.callback, options.callbackData);
                    $modal.fadeIn(_this._contentFadeTime);

                    switch(options.overlayAction) {
                        case 'none':
                            break;

                        case 'close':
                        default:
                            $overlay.addClass('modal-close');
                            break;
                    }
                };

            if($container.length) {
                $(_this.attr.content).fadeOut(200, function() {
                    dfKit.call(_this._closeCallback);
                    _this._closeCallback = null;
                    builder();
                });
            } else {
                $('body').addClass('modal-open');
                $container = $('<div id="modal-container"><div id="modal-wrapper"><div id="modal-content"></div></div></div>').hide().appendTo('body');
                $(_this.attr.content).hide();

                $container.fadeIn(_this._containerFadeTime, function() {
                    builder();
                });
            }
        },

        close: function(callback, data) {
            var _this = this;
            this.init();

            if($('.widget-form', this.attr.container).length) {
                var $form = $('.widget-form', this.attr.container).first();

                dfKit.ajax.post($form.attr('action'), {
                    form: [{name:'formEvent', value:'cancel'}],
                    $element: $form,
                    requestSource: 'modal',
                    formComplete: function() {
                        _this._close();
                    }
                });
            } else {
                this._close(callback, data);
            }
        },

        _close: function(callback, data) {
            var _this = this;

            var callbackRunner = function() {
                dfKit.call(_this._closeCallback, data);
                _this._closeCallback = null;
                dfKit.call(callback, data);
            };

            $(_this.attr.container + ',' + _this.attr.wrapper).removeClass('modal-close');

            if(!$(_this.attr.container).length) {
                callbackRunner();
            } else {
                $(_this.attr.content).fadeOut(_this._contentFadeTime, function() {
                    _this._overlayAction = 'close';

                    $(_this.attr.container).fadeOut(_this._containerFadeTime, function() {
                        _this._initialUrl = null;
                        $(this).remove();
                        $('body').removeClass('modal-open');
                        callbackRunner();
                    });
                });
            }
        },

        

        onLinkClick: function(e) {
            var _this = this,
                href = $(e.target).closest('a').attr('href'),
                data = dfKit.ajax._lastRequest ? dfKit.ajax._lastRequest : {};
            if(!href) return;

            e.preventDefault();

            dfKit.ajax.load(_this.attr.content, href, {
                requestType: 'ajax',
                requestSource: 'modal',
                formComplete: function(response) {
                    if(response.request.formEvent != 'cancel'
                    && response.forceRedirect
                    && response.redirect !== null) {
                        return dfKit.ajax.load(_this.attr.content, response.redirect, {
                            requestSource: 'modal'
                        });
                    }

                    dfKit.ajax.load(_this.attr.content, _this._initialUrl);
                }
            });
        },

        onFormSubmit: function(e) {
            e.preventDefault();

            var $form = $(e.target),
                formEvent,
                data = dfKit.ajax._lastRequest ? dfKit.ajax._lastRequest : {};

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
                data.$element = $form;
            }

            data.requestSource = 'modal';

            dfKit.ajax.post($form.attr('action'), data, function(output) {
                dfKit.ajax.handleResponse(output);
            });
            
        }
    }
};

$(function() { dfKit.init(); });