define([
    'jquery',
    'underscore',
    'df-kit/core',
    '{mediaelement}/build/mediaelement-and-player.min'
], function($, _, Core, mejs) {


    return Core.component({
        options: {
            pluginPath: 'https://cdnjs.com/libraries/mediaelement/',
            shimScriptAccess: 'always',
            stretching: 'responsive'
        },

        vimeoOnPage: false,
        _pluginDeferred: null,

        init: function() {
            this.load($('audio.w.embed[data-mejs], video.w.embed[data-mejs]'));
        },

        load: function($el) {
            var _this = this, loaded = false, deferred = $.Deferred();

            if(!$el.length) {
                return deferred.promise();
            }


            if(!_this.vimeoOnPage) {
                _this.vimeoOnPage = $('video.w.embed[data-provider=vimeo]').length;

                if(_this.vimeoOnPage) {
                    _this._pluginDeferred = $.Deferred();

                    require(['https://player.vimeo.com/api/player.js'], function(Vimeo) {
                        window.Vimeo = {Player: Vimeo};

                        require(['{mediaelement}/build/renderers/vimeo'], function() {
                            _this._pluginDeferred.resolve();
                        });
                    });
                }
            }


            if(_this._pluginDeferred) {
                _this._pluginDeferred.done(function() {
                    _this._load($el, deferred);
                });
            } else {
                _this._load($el, deferred);
            }

            return deferred.promise();
        },

        _load: function($el, deferred) {
            if(!$el.length) {
                return;
            }

            $el.mediaelementplayer(_.extend(this.options, {
                success: function(mediaElement, originalNode, instance) {
                    deferred.resolve(mediaElement, originalNode, instance);
                }
            }));
        }
    });
});
