define([
    'jquery',
    'df-kit/core',
    'df-kit/ajax'
], function($, Core, Ajax) {
    return Core.component({
        attr: {
            trigger: '.pushy, [data-pushy]',
            leftContainer: '.pushy-container.push-left',
            rightContainer: '.pushy-container.push-right',
            content: '.pushy-content'
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

                _this.load(href, {
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

            Core.on('dialog.open', function(source) {
                if(source !== 'pushy') {
                    //_this.close();
                }
            });
        },

        load: function(href, options) {
            var _this = this,
                callback = options.callback;

            options = options || {};

            options.callback = function(data) {
                _this.client = Ajax.loadElement(_this.attr.content, href, {
                    source: 'pushy',

                    formComplete: function(response) {
                        _this._close();
                    },

                    onLoad: function(response) {
                        Core.call(callback);
                    }
                });
            };

            _this.open('', options);
        },

        open: function(html, options) {
            var _this = this,
                $body = $(document.body);

            options = options || {};
            Core.trigger('dialog.open', 'pushy');

            var $leftContainer = $(_this.attr.leftContainer),
                $rightContainer = $(_this.attr.rightContainer),
                builder = function() {
                    $body.addClass('pushy-active');
                    $('.pushy-container').removeClass('pushy-active');

                    if(!$body.hasClass('push-'+options.side)) {
                        $body.removeClass('push-left push-right').addClass('push-'+options.side);
                    }

                    if(options.side == 'left') {
                        $content = $leftContainer.addClass('pushy-active').find('.pushy-content');
                        //$rightContainer.find('.pushy-content').html('');
                    } else {
                        $content = $rightContainer.addClass('pushy-active').find('.pushy-content');
                        //$leftContainer.find('.pushy-content').html('');
                    }

                    $content.html(html);

                    _this._closeCallback = options.closeCallback;

                    if(options.class) {
                        $leftContainer.addClass(options.class);
                        $rightContainer.addClass(options.class);
                    }

                    Core.call(options.callback, options.callbackData);
                    //$content.show();
                    $content.fadeIn(_this._contentFadeTime);
                };

            if($leftContainer.length) {
                var $active = $(_this.attr.content, '.pushy-container.pushy-active'),
                    runner = function() {
                        Core.call(_this._closeCallback);
                        _this._closeCallback = null;
                        builder();
                    };

                if($active.length && !$body.hasClass('push-'+options.side)) {
                    runner();
                } else {
                    $('.pushy-content').hide();
                    runner();
                }
            } else {
                $leftContainer = $('<aside class="pushy-container push-left"><a class="pushy-close">x</a><div class="pushy-content"></div></aside>').appendTo('body');
                $rightContainer = $('<aside class="pushy-container push-right"><a class="pushy-close">x</a><div class="pushy-content"></div></aside>').appendTo('body');
                $(_this.attr.content).hide();
                builder();
            }
        },

        close: function(callback, data) {
            var _this = this;

            if($('.w-form', this.attr.content).length) {
                var $form = $('.w-form', this.attr.content).first();

                Ajax.post($form.attr('action'), {
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
                Core.call(_this._closeCallback, data);
                _this._closeCallback = null;
                Core.call(callback, data);

                if(_this.client) {
                    _this.client.destroy();
                }
            };

            if(!$(_this.attr.content).length) {
                callbackRunner();
            } else {
                var $body = $(document.body);
                $body.removeClass('pushy-active');

                setTimeout(function() {
                    $body.removeClass('push-left push-right');
                    $('.pushy-container').remove();
                    callbackRunner();
                }, 500);
            }
        }
    });
});