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
        _overlayAction: 'close',
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

            core.on('dialog.open', function(source) {
                if(source !== 'modal') {
                    _this.close();
                }
            });
        },

        load: function(href, options) {
            var _this = this,
                callback = options.callback;

            options = options || {};

            options.callback = function(data) {
                _this.client = ajax.loadElement(_this.attr.content, href, {
                    source: 'modal',

                    formComplete: function(response) {
                        _this._close();
                    },
                    onLoad: function(response) {
                        core.call(callback);
                    }
                });
            };

            _this.open('loading...', options);
        },

        open: function(html, options) {
            var _this = this;
            options = options || {};
            core.trigger('dialog.open', 'modal');

            var $container = $(_this.attr.container),
                $modal, $overlay,
                builder = function() {
                    $modal = $(_this.attr.content).hide().html(html);
                    $overlay = $(_this.attr.container + ',' + _this.attr.wrapper).removeClass('modal-close');
                    _this._closeCallback = options.closeCallback;

                    if(options.class) $modal.addClass(options.class);
                    if(options.containerClass) $container.addClass(options.containerClass);

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
                core.call(_this._closeCallback, data);
                _this._closeCallback = null;
                core.call(callback, data);

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
                        $('body').removeClass('modal-open');
                        callbackRunner();
                    });
                });
            }
        }
    });
});