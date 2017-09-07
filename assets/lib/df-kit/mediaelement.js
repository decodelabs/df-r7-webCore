define([
    'jquery',
    'df-kit/core',
    '{mediaelement}/build/mediaelement-and-player.min'
], function($, Core, me) {
    $('audio.w.embed').mediaelementplayer({
        pluginPath: 'https://cdnjs.com/libraries/mediaelement/',
        shimScriptAccess: 'always',
        stretching: 'responsive'
    });
});
