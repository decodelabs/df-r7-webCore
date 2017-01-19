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
                    overlayAction = $(this).data('overlay-action')
                    closeButton = $(this).data('modal-close-button');

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
                    overlayAction: overlayAction,
                    closeButton: closeButton
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

        _importOptions: function(options, response) {
            var keys = {
                class: ['dialogClass', 'modalClass'],
                overlayClass: ['dialogOverlayClass', 'modalOverlayClass'],
                overlayAction: ['dialogOverlayAction', 'modalOverlayAction'],
                closeButton: ['dialogCloseButton', 'modalCloseButton']
            };

            $.each(keys, function(option, importKeys) {
                $.each(importKeys, function(i, importKey) {
                    if(typeof response[importKey] !== typeof undefined) {
                        options[option] = response[importKey];
                    }
                });
            });

            return options;
        },

        load: function(href, options) {
            options = options || {};

            var _this = this,
                deferred = $.Deferred();

            _this.open(null, options).done(function($content) {
                Ajax.embedInto(_this.attr.content, href, {
                    source: 'modal',
                    live: options.live !== false
                }).progress(function(client) {
                    _this.client = client;

                    _this.client.once('content:show', function() {
                        Core.trigger('dialog.load', 'modal');
                        $(_this.attr.container).removeClass('loading');
                    });

                    _this.client.on('form:completeInitial', function(response) {
                        response.handled = true;
                        _this.close();
                    });

                    deferred.notify(client);
                }).done(function(response, request) {
                    _this._importOptions(options, response);
                    _this._applyOptions(options);
                    deferred.resolve(response, request);
                });
            });

            return deferred.promise();
        },

        open: function(html, options) {
            var _this = this,
                $overlay = $(_this.attr.overlay),
                deferred = $.Deferred();

            options = options || {};
            Core.trigger('dialog.open', 'modal');


            // Prepare
            if($overlay.length) {
                $(_this.attr.container).fadeOut(200, function() {
                    _this.trigger('clear');

                    $(_this.attr.container).attr('class', '');
                    $overlay.attr('class', '');

                    deferred.notify();
                });
            } else {
                $('html').addClass('modal-open');
                $overlay = $('<div id="modal-overlay"><div id="modal-scroll"><div id="modal-container" class="loading"><a class="modal-close">✕</a><div id="modal-content">loading...</div></div></div></div>').hide().appendTo('body');
                $(_this.attr.container).hide();

                $overlay.fadeIn(_this._overlayFadeTime, function() {
                    deferred.notify();
                });
            }

            // Render
            deferred.progress(function() {
                var $container = $(_this.attr.container),
                    $content = $(_this.attr.content);

                $container.hide();

                // Use options
                _this._applyOptions(options);

                // Load content
                if(html !== null) {
                    $content.html(html);
                    $container.removeClass('loading');
                }

                deferred.resolve($content);

                // Fade in
                $container.fadeIn(_this._contentFadeTime);
            });

            return deferred.promise();
        },

        _applyOptions: function(options) {
            var $overlay = $(this.attr.overlay),
                $container = $(this.attr.container),
                $combined = $(this.attr.overlay + ',' + this.attr.scroll),
                $content = $(this.attr.content);


            if(options.class) $container.attr('class', options.class);

            if(options.overlayClass) {
                $overlay.attr('class', options.overlayClass);
            } else if(typeof options.overlayClass !== typeof undefined) {
                $overlay.attr('class', '');
            }

            switch(options.overlayAction) {
                case 'none':
                    $combined.removeClass('modal-close');
                    break;

                case 'close':
                default:
                    $combined.addClass('modal-close');
                    break;
            }

            $container.find('> a.modal-close').toggleClass('hidden', options.closeButton === false);
        },


        close: function() {
            var _this = this,
                deferred = $.Deferred();

            if(this.client) {
                this.client.close('modal').done(deferred.notify);
            } else {
                deferred.notify();
            }

            deferred.progress(function() {
                $(_this.attr.overlay + ',' + _this.attr.scroll).removeClass('modal-close');

                if(!$(_this.attr.overlay).length) {
                    deferred.resolve();
                } else {
                    $(_this.attr.container).fadeOut(_this._contentFadeTime, function() {
                        $(_this.attr.overlay).fadeOut(_this._overlayFadeTime, function() {
                            $(this).remove();
                            $('html').removeClass('modal-open');
                            deferred.resolve();
                        });
                    });
                }
            });

            deferred.done(function() {
                if(_this.client) _this.client.destroy();
                _this.trigger('clear');
                _this.trigger('close');
            });

            return deferred.promise();
        }
    });
});