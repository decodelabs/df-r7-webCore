define([
    'jquery',
    'df-kit/core',
    'df-kit/ajax'
], function($, Core, Ajax) {
    return Core.component({
        attr: {
            trigger: '.modal, [data-modal]',
            overlay: '#modal-overlay',
            scroll: '#modal-scroll',
            container: '#modal-container',
            content: '#modal-content'
        },

        _closeCallback: null,
        _overlayAction: 'close',
        _currentOptions: null,
        _overlayFadeTime: 200,
        _contentFadeTime: 0,
        client: null,

        init: function() {
            var _this = this;

            $(document).on('click', this.attr.trigger, function(e) {
                e.preventDefault();

                var modalClass = $(this).data('modal-class'),
                    overlayClass = $(this).data('modal-overlay-class'),
                    href = $(this).data('modal-href'),
                    overlayAction = $(this).data('overlay-action');

                if(!href) {
                    href = $(this).data('modal');
                    if(href === true) href = null;
                }

                if(!href) {
                    href = $(this).attr('href');
                }

                _this.load(href, {
                    class: modalClass,
                    overlayClass: overlayClass,
                    overlayAction: overlayAction
                });
            });

            $(document).on('click touchstart', '.modal-close', function(e) {
                if(e.target != e.currentTarget) return;
                e.preventDefault();
                _this.close();
            });

            $(document).keyup(function(e) {
                if(e.which == 27) {
                    _this.close();
                }
            });

            Core.on('dialog.open', function(source) {
                if(source !== 'modal') {
                    _this.close();
                }
            });
        },

        load: function(href, options) {
            options = options || {};

            var _this = this,
                callback = options.callback;

            options.callback = function(data) {
                if(_this.client) {
                    _this.client.clear();
                }

                _this.client = Ajax.loadElement(_this.attr.content, href, {
                    source: 'modal'
                });

                _this.client.once('content:show', function() {
                    Core.call(callback);
                    Core.trigger('dialog.load', 'modal');
                });

                _this.client.on('form:completeInitial', function(response) {
                    response.handled = true;
                    _this.close();
                });
            };

            _this.open('loading...', options);
        },

        open: function(html, options) {
            var _this = this;
            options = options || {};
            Core.trigger('dialog.open', 'modal');

            var $overlay = $(_this.attr.overlay),
                builder = function() {
                    _this._currentOptions = options;
                    var $container = $(_this.attr.container).hide(),
                        $combined = $(_this.attr.overlay + ',' + _this.attr.scroll).removeClass('modal-close');

                    $(_this.attr.content).html(html);
                    _this._closeCallback = options.closeCallback;

                    if(options.class) $container.addClass(options.class);
                    if(options.overlayClass) $overlay.addClass(options.overlayClass);

                    Core.call(options.callback, options.callbackData);
                    $container.fadeIn(_this._contentFadeTime);

                    switch(options.overlayAction) {
                        case 'none':
                            break;

                        case 'close':
                        default:
                            $combined.addClass('modal-close');
                            break;
                    }
                };

            if($overlay.length) {
                $(_this.attr.container).fadeOut(200, function() {
                    Core.call(_this._closeCallback);
                    _this._closeCallback = null;

                    if(_this._currentOptions) {
                        if(_this._currentOptions.class) $(_this.attr.container).removeClass(_this._currentOptions.class);
                        if(_this._currentOptions.overlayClass) $overlay.removeClass(_this._currentOptions.overlayClass);
                    }

                    builder();
                });
            } else {
                $('html').addClass('modal-open');
                $overlay = $('<div id="modal-overlay"><div id="modal-scroll"><div id="modal-container"><a class="modal-close">✕</a><div id="modal-content"></div></div></div></div>').hide().appendTo('body');
                $(_this.attr.container).hide();

                $overlay.fadeIn(_this._overlayFadeTime, function() {
                    builder();
                });
            }
        },

        close: function(callback, data) {
            var _this = this,
                isComplete = _this.client && _this.client.lastResponse && _this.client.lastResponse.isComplete;

            if(!isComplete && $('.w-form', this.attr.overlay).length) {
                var $form = $('.w-form', this.attr.overlay).first();

                Ajax.post($form.attr('action'), {
                    data: [{name:'formEvent', value:'cancel'}],
                    source: 'modal'
                }, function() {
                    _this._close();
                });
            } else {
                this._close(callback, data);
            }
        },

        _close: function(callback, data) {
            var _this = this;

            var callbackRunner = function() {
                Core.call(_this._closeCallback, data);
                _this._closeCallback = null;
                Core.call(callback, data);

                if(_this.client) {
                    _this.client.destroy();
                }
            };

            $(_this.attr.overlay + ',' + _this.attr.scroll).removeClass('modal-close');

            if(!$(_this.attr.overlay).length) {
                callbackRunner();
            } else {
                $(_this.attr.content).fadeOut(_this._contentFadeTime, function() {
                    _this._overlayAction = 'close';

                    $(_this.attr.overlay).fadeOut(_this._overlayFadeTime, function() {
                        $(this).remove();
                        $('html').removeClass('modal-open');
                        callbackRunner();
                        _this.trigger('close');
                    });
                });
            }
        }
    });
});