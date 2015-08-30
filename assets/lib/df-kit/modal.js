define([
    'jquery',
    'df-kit/core',
    'df-kit/ajax'
], function($, core, ajax) {
    return core.component({
        attr: {
            trigger: '.modal, [data-modal]',
            container: '#modal-container',
            wrapper: '#modal-wrapper',
            content: '#modal-content'
        },

        _closeCallback: null,
        _initialUrl: null,
        _overlayAction: 'close',
        _containerFadeTime: 200,
        _contentFadeTime: 0,

        init: function() {
            var _this = this;

            $(document).on('click', this.attr.trigger, function(e) {
                e.preventDefault();

                var modalClass = $(this).data('modal-class'),
                    href = $(this).data('modal-href'),
                    overlayAction = $(this).data('overlay-action');

                if(!href) {
                    href = $(this).data('modal');
                }
                
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
                if(!core.isUrlExternal($(this).attr('href'))) {
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
        },

        ajax: function(href, options) {
            var _this = this,
                callback = options.callback;

            options = options || {};

            options.callback = function(data) {
                _this._initialUrl = href;

                ajax.load(_this.attr.content, href, {
                    requestSource: 'modal',

                    formComplete: function(response) {
                        if(response.request.formEvent != 'cancel'
                        && response.forceRedirect
                        && response.redirect !== null) {
                            return ajax.load(_this.attr.content, response.redirect, {
                                requestSource: 'modal'
                            });
                        }
                        
                        _this.close();
                    },
                    onLoad: function(response) {
                        core.call(callback);
                    }
                });
            };

            _this.open('', options);
        },

        open: function(html, options) {
            var _this = this;
            options = options || {};

            var $container = $(_this.attr.container),
                $modal, $overlay,
                builder = function() {
                    $modal = $(_this.attr.content).hide().html(html);
                    $overlay = $(_this.attr.container + ',' + _this.attr.wrapper).removeClass('modal-close');
                    _this._closeCallback = options.closeCallback;
                    if(options.class) $modal.addClass(options.class);

                    core.call(options.callback, options.callbackData);
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
                    core.call(_this._closeCallback);
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

            if($('.widget-form', this.attr.container).length) {
                var $form = $('.widget-form', this.attr.container).first();

                ajax.post($form.attr('action'), {
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
                core.call(_this._closeCallback, data);
                _this._closeCallback = null;
                core.call(callback, data);
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
                data = ajax._lastRequest ? ajax._lastRequest : {};
            if(!href) return;

            e.preventDefault();

            ajax.load(_this.attr.content, href, {
                requestType: 'ajax',
                requestSource: 'modal',
                formComplete: function(response) {
                    if(response.request.formEvent != 'cancel'
                    && response.forceRedirect
                    && response.redirect !== null) {
                        return ajax.load(_this.attr.content, response.redirect, {
                            requestSource: 'modal'
                        });
                    }

                    ajax.load(_this.attr.content, _this._initialUrl);
                }
            });
        },

        onFormSubmit: function(e) {
            e.preventDefault();

            var $form = $(e.target),
                formEvent,
                data = ajax._lastRequest ? ajax._lastRequest : {};

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

            ajax.post($form.attr('action'), data, function(output) {
                ajax.handleResponse(output);
            });
            
        }
    });
});