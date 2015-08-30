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
        _initialUrl: null,
        _contentFadeTime: 200,

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

            $(document).on('click', _this.attr.container + ' a:not(.pushy-close,.local,[target],'+ _this.attr.trigger + ')', function(e) {
                if(!core.isUrlExternal($(this).attr('href'))) {
                    ajax.onLinkClick(e, _this.attr.content, _this._initialUrl);
                }
            });

            $(document).on('submit', _this.attr.container + ' .widget-form', function(e) {
                ajax.onFormSubmit(e, _this.attr.content);
            });

            $(document).on('click', _this.attr.container + ' .widget-eventButton', function(e) {
                var event = $(this).val();
                $('#form-hidden-activeFormEvent').remove();
                $(this).closest('form').append('<input type="hidden" name="formEvent" id="form-hidden-activeFormEvent" value="'+event+'" />');
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
            var _this = this,
                $body = $(document.body);
            options = options || {};

            var $container = $(_this.attr.container),
                builder = function() {
                    $body.addClass('active');

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

            if(!$(_this.attr.container).length) {
                callbackRunner();
            } else {
                var $body = $(document.body);
                $body.removeClass('active');

                setTimeout(function() {
                    $body.removeClass('push-left push-right');
                    _this._initialUrl = null;
                    $(_this.attr.container).remove();
                    callbackRunner();
                }, 500);
            }
        }
    });
});