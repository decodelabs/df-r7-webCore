define([
    'jquery',
    'df-kit/core',
    '{mediaelement}/build/mediaelement-and-player.min'
], function($, Core, mejs) {
    var options = {
        pluginPath: 'https://cdnjs.com/libraries/mediaelement/',
        shimScriptAccess: 'always',
        stretching: 'responsive'
    };

    //var hasVimeo = $('video.w.embed[data-mejs][data-provider=vimeo]').length;
    var load = function() {
        $('audio.w.embed[data-mejs], video.w.embed[data-mejs]').mediaelementplayer(options);
    };

    //if(hasVimeo) {
        require(['https://player.vimeo.com/api/player.js'], function(Vimeo) {
            window.Vimeo = {Player: Vimeo};
            require(['{mediaelement}/build/renderers/vimeo'], load);
        });
    //} else {
        //load();
    //}

    return {
        load: function($el) {
            $el.mediaelementplayer(options);
        }
    };
});
