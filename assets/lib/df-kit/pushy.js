define([
    'jquery',
    'df-kit/core',
    'df-kit/ajax'
], function($, core, ajax) {
    return core.component({
        attr: {
            trigger: '.pushy, [data-pushy]',
            container: '#pushy-container',
            content: '#pushy-content'
        },

        _closeCallback: null,
        _contentFadeTime: 200,
        client: null,

        init: function() {
            var _this = this;

            $(document).on('click', this.attr.trigger, function(e) {
                e.preventDefault();

                var pushyClass = $(this).data('pushy-class'),
                    href = $(this).data('pushy-href'),
                    side = $(this).data('pushy-side');

                if(!href) {
                    href = $(this).data('pushy');
                }
                
                if(!href) {
                    href = $(this).attr('href');
                }

                if(!side) {
                    side = 'right';
                }

                _this.ajax(href, {
                    class: pushyClass,
                    side: side
                });
            });

            $(document).on('click touchstart', '.pushy-close', function(e) {
                if(e.target != e.currentTarget) return;
                e.preventDefault();
                _this.close();
            });

            $(document).keyup(function(e) {
                if(e.which == 27) {
                    _this.close();
                }
            });
        },

        ajax: function(href, options) {
            var _this = this,
                callback = options.callback;

            options = options || {};

            options.callback = function(data) {
                _this.client = ajax.loadElement(_this.attr.content, href, {
                    source: 'pushy',

                    formComplete: function(response) {
                        _this._close();
                    },

                    onLoad: function(response) {
                        core.call(callback);
                    }
                });
            };

            _this.open('', options);
        },

        open: function(html, options) {
            var _this = this,
                $body = $(document.body);

            options = options || {};

            var $container = $(_this.attr.container),
                builder = function() {
                    $body.addClass('pushy-active');

                    if(!$body.hasClass('push-'+options.side)) {
                        $body.removeClass('push-left push-right').addClass('push-'+options.side);
                    }

                    $pushy = $(_this.attr.content).hide().html(html);
                    _this._closeCallback = options.closeCallback;

                    if(options.class) $container.addClass(options.class);

                    core.call(options.callback, options.callbackData);
                    $pushy.fadeIn(_this._contentFadeTime);
                };

            if($container.length) {
                $(_this.attr.content).fadeOut(200, function() {
                    core.call(_this._closeCallback);
                    _this._closeCallback = null;
                    builder();
                });
            } else {
                $container = $('<aside id="pushy-container"><a class="pushy-close">x</a><div id="pushy-content"></div></aside>').appendTo('body');
                $(_this.attr.content).hide();
                builder();
            }
        },

        close: function(callback, data) {
            var _this = this;

            if($('.widget-form', this.attr.container).length) {
                var $form = $('.widget-form', this.attr.container).first();

                ajax.post($form.attr('action'), {
                    form: [{name:'formEvent', value:'cancel'}],
                    $element: $form,
                    source: 'pushy',
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

                if(_this.client) {
                    _this.client.destroy();
                }
            };

            if(!$(_this.attr.container).length) {
                callbackRunner();
            } else {
                var $body = $(document.body);
                $body.removeClass('pushy-active');

                setTimeout(function() {
                    $body.removeClass('push-left push-right');
                    $(_this.attr.container).remove();
                    callbackRunner();
                }, 500);
            }
        }
    });
});