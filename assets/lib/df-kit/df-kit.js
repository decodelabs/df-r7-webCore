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

        normalizeResponse: function(response, requestData, callback) {
            if(typeof response !== 'object') {
                response = {
                    content: response
                };
            }

            response.requestData = requestData;

            if(response.isComplete && requestData.formComplete) {
                response.requestData.formComplete(response);
                return;
            }

            if(response.redirect && response.redirect !== null) {
                return this.get(response.redirect, requestData, callback);
            }

            dfKit.call(callback, response);
        },

        handleResponse: function(response) {
            if(!response) {
                return;
            }
            
            response.requestData.$element.html(response.content);

            if(response.requestData.onLoad && response.requestData.onLoad !== null) {
                response.requestData.onLoad(response);
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

        init: function() {
            if(this._isInit) return;
            var _this = this;

            $(document).on('click', this.attr.trigger, function(e) {
                e.preventDefault();

                var modalClass = $(this).data('modal-class');
                var href = $(this).data('modal-href');

                if(!href) {
                    href = $(this).attr('href');
                }

                _this.ajax(href, modalClass);
            });

            $(document).on('click touchstart', _this.attr.container + ', ' + _this.attr.wrapper + ', .modal-close', function(e) {
                if(e.target != e.currentTarget) return;
                e.preventDefault();
                _this.close();
            });

            $(document).on('click', _this.attr.container + ' a:not(.modal-close)', function(e) {
                _this.onLinkClick(e);
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

        ajax: function(href, modalClass, callback, closeCallback) {
            var _this = this;
            _this.init();

            _this.open('', modalClass, function(data) {
                _this._initialUrl = href;

                dfKit.ajax.load('#modal-content', href, {
                    requestSource: 'modal',

                    formComplete: function(response) {
                        _this.close();
                    },
                    onLoad: function(response) {
                        dfKit.call(callback);
                    }
                });
            }, closeCallback);
        },

        open: function(html, modalClass, callback, closeCallback, callbackData) {
            var _this = this;
            _this.init();
            _this.close();
            _this._closeCallback = closeCallback;

            $('body').addClass('modal-open');
            var $container = $('<div id="modal-container"><div id="modal-wrapper"><div id="modal-content"></div></div></div>').hide().appendTo('body');
            var $modal = $(_this.attr.content).hide().html(html);

            if(modalClass) {
                $modal.addClass(modalClass);
            }

            $container.fadeIn(200, function() {
                $modal.fadeIn(200);
                dfKit.call(callback, callbackData);
            });
        },

        close: function(callback, data) {
            var _this = this;
            _this.init();

            $(_this.attr.content).fadeOut(200, function() {
                $(_this.attr.container).fadeOut(200, function() {
                    _this._initialUrl = null;
                    $(this).remove();
                    $('body').removeClass('modal-open');
                    dfKit.call(_this._closeCallback, data);
                    _this._closeCallback = null;
                    dfKit.call(callback, data);
                });
            });
        },

        

        onLinkClick: function(e) {
            var _this = this,
                href = $(e.target).closest('a').attr('href'),
                data = dfKit.ajax._lastRequest ? dfKit.ajax._lastRequest : {};
            if(!href) return;

            e.preventDefault();

            dfKit.ajax.load('#modal-content', href, {
                requestType: 'ajax',
                requestSource: 'modal',
                formComplete: function(response) {
                    dfKit.ajax.load('#modal-content', _this._initialUrl);
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