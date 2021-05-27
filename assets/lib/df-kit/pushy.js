define([
    'jquery',
    'df-kit/core',
    'df-kit/ajax'
], function ($, Core, Ajax) {
    return Core.component({
        attr: {
            trigger: '.pushy, [data-pushy]',
            leftContainer: '.pushy-container.push-left',
            rightContainer: '.pushy-container.push-right',
            content: '.pushy-content'
        },

        _contentFadeTime: 200,
        client: null,

        init: function () {
            var _this = this;

            $(document).on('click', this.attr.trigger, function (e) {
                e.preventDefault();

                var pushyClass = $(this).data('pushy-class'),
                    href = $(this).data('pushy-href'),
                    side = $(this).data('pushy-side')
                closeButton = $(this).data('pushy-close-button');

                if (!href) {
                    href = $(this).data('pushy');
                    if (href === true) href = null;
                }

                if (!href) href = $(this).attr('href');
                if (!side) side = 'right';

                _this.load(href, {
                    class: pushyClass,
                    side: side,
                    closeButton: closeButton
                });
            });

            $(document).on('click', '.pushy-close', function (e) {
                if (e.target != e.currentTarget) return;
                e.preventDefault();
                _this.close();
            });

            $(document).keyup(function (e) {
                if (e.which == 27 && !$('html').hasClass('modal-open')) {
                    _this.close();
                }
            });

            Core.on('dialog.open', function (source) {
                if (source !== 'pushy') {
                    //_this.close();
                }
            });
        },


        _importOptions: function (options, response) {
            var keys = {
                class: ['dialogClass', 'pushyClass'],
                closeButton: ['dialogCloseButton', 'pushyCloseButton']
            };

            $.each(keys, function (option, importKeys) {
                $.each(importKeys, function (i, importKey) {
                    if (typeof response[importKey] !== typeof undefined) {
                        options[option] = response[importKey];
                    }
                });
            });

            return options;
        },

        load: function (href, options) {
            var _this = this,
                deferred = $.Deferred();

            _this.open('loading...', options).done(function ($content) {
                Ajax.embedInto($content, href, {
                    source: 'pushy',
                    live: options.live !== false
                }).progress(function (client) {
                    _this.client = client;

                    _this.client.once('content:show', function (response) {
                        Core.trigger('dialog.load', 'pushy');
                    });

                    _this.client.once('form:completeInitial', function (response) {
                        response.handled = true;
                        _this.close();
                    });

                    deferred.notify(client);
                }).done(function (response, request) {
                    _this._importOptions(options, response);
                    _this._applyOptions(options);
                    deferred.resolve(response, request);
                });
            });

            return deferred.promise();
        },

        open: function (html, options) {
            var _this = this,
                $doc = $('html'),
                $leftContainer = $(_this.attr.leftContainer),
                $rightContainer = $(_this.attr.rightContainer),
                deferred = $.Deferred();

            options = options || {};
            Core.trigger('dialog.open', 'pushy');


            // Prepare
            if ($leftContainer.length) {
                _this.trigger('clear');
                var $active = $(_this.attr.content, '.pushy-container.pushy-active');

                if (!($active.length && !$doc.hasClass('push-' + options.side))) {
                    $(_this.attr.content).hide();
                }
            } else {
                $leftContainer = $('<aside class="pushy-container push-left"><a class="pushy-close">✕</a><div class="pushy-content"></div></aside>').appendTo('body');
                $rightContainer = $('<aside class="pushy-container push-right"><a class="pushy-close">✕</a><div class="pushy-content"></div></aside>').appendTo('body');
                $(_this.attr.content).hide();
            }

            // Set active
            $('.pushy-container').removeClass('pushy-active');

            if (!$doc.hasClass('push-' + options.side)) {
                $doc.removeClass('push-left push-right').addClass('push-' + options.side);
            }


            // Load content
            if (options.side == 'left') {
                $content = $leftContainer.addClass('pushy-active').find('.pushy-content');
            } else {
                $content = $rightContainer.addClass('pushy-active').find('.pushy-content');
            }

            _this._applyOptions(options);
            $content.html(html);

            // Start anim
            setTimeout(function () {
                $doc.addClass('pushy-active');
            }, 1);

            deferred.resolve($content);

            // Fade in
            $content.fadeIn(_this._contentFadeTime);

            return deferred.promise();
        },

        _applyOptions: function (options) {
            var $container;

            if (options.side == 'left') {
                $container = $('.pushy-container.push-left');
            } else {
                $container = $('.pushy-container.push-right');
            }

            if (options.class) $container.attr('class', 'pushy-container push-' + options.side + ' ' + options.class);
            $container.find('> a.pushy-close').toggleClass('hidden', options.closeButton === false);
        },

        close: function () {
            var _this = this,
                deferred = $.Deferred();

            if (this.client) {
                this.client.close('modal').done(deferred.notify);
            } else {
                deferred.notify();
            }

            deferred.progress(function () {
                if (!$(_this.attr.content).length) {
                    deferred.resolve();
                } else {
                    var $body = $('html');
                    $body.removeClass('pushy-active');

                    setTimeout(function () {
                        $body.removeClass('push-left push-right');
                        $('.pushy-container').remove();
                        deferred.resolve();
                    }, 500);
                }
            });

            deferred.done(function () {
                if (_this.client) _this.client.destroy();
                _this.trigger('clear');
                _this.trigger('close');
            });

            return deferred.promise();
        }
    });
});
