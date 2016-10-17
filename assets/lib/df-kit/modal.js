define([
    'jquery',
    'df-kit/core',
    'df-kit/ajax'
], function($, Core, Ajax) {
    return Core.component({
        attr: {
            trigger: '.modal, [data-modal]',
            container: '#modal-container',
            wrapper: '#modal-wrapper',
            content: '#modal-content'
        },

        _closeCallback: null,
        _overlayAction: 'close',
        _currentOptions: null,
        _containerFadeTime: 200,
        _contentFadeTime: 0,
        client: null,

        init: function() {
            var _this = this;

            $(document).on('click', this.attr.trigger, function(e) {
                e.preventDefault();

                var modalClass = $(this).data('modal-class'),
                    containerClass = $(this).data('modal-container-class'),
                    href = $(this).data('modal-href'),
                    overlayAction = $(this).data('overlay-action');

                if(!href) {
                    href = $(this).data('modal');
                }

                if(!href) {
                    href = $(this).attr('href');
                }

                _this.load(href, {
                    class: modalClass,
                    containerClass: containerClass,
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

            var $container = $(_this.attr.container),
                $modal, $overlay,
                builder = function() {
                    _this._currentOptions = options;
                    $modal = $(_this.attr.content).hide().html(html);
                    $overlay = $(_this.attr.container + ',' + _this.attr.wrapper).removeClass('modal-close');
                    _this._closeCallback = options.closeCallback;

                    if(options.class) $modal.addClass(options.class);
                    if(options.containerClass) $container.addClass(options.containerClass);

                    Core.call(options.callback, options.callbackData);
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
                    Core.call(_this._closeCallback);
                    _this._closeCallback = null;

                    if(_this._currentOptions.class) $(_this.attr.content).removeClass(_this._currentOptions.class);
                    if(_this._currentOptions.containerClass) $container.removeClass(_this._currentOptions.containerClass);

                    builder();
                });
            } else {
                $('html').addClass('modal-open');
                $container = $('<div id="modal-container"><div id="modal-wrapper"><div id="modal-content"></div></div></div>').hide().appendTo('body');
                $(_this.attr.content).hide();

                $container.fadeIn(_this._containerFadeTime, function() {
                    builder();
                });
            }
        },

        close: function(callback, data) {
            var _this = this;

            if($('.w-form', this.attr.container).length) {
                var $form = $('.w-form', this.attr.container).first();

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

            $(_this.attr.container + ',' + _this.attr.wrapper).removeClass('modal-close');

            if(!$(_this.attr.container).length) {
                callbackRunner();
            } else {
                $(_this.attr.content).fadeOut(_this._contentFadeTime, function() {
                    _this._overlayAction = 'close';

                    $(_this.attr.container).fadeOut(_this._containerFadeTime, function() {
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