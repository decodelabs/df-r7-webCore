(function() {
    var listeners = {}, resolved = {};

    function listen(listener, name) {
        if(name in resolved) {
            listener(name, resolved[name]);
        } else if(listeners[name]) {
            listeners[name].push(listener);
        } else {
            listeners[name] = [listener];
            var split = name.split('!');

            req(['js!' + split[0]], function(loader) {
                resolve(split[1], loader(split[1]));
            });
        }
    }

    function resolve(name, value) {
        resolved[name] = value;
        var awaiting = listeners[name];

        if(awaiting) {
            awaiting.forEach(function(listener) {
                listener(name, value);
            });

            listeners[name] = 0;
        }
    }

    function req(deps, definition) {
        if(!definition && typeof deps === 'function') {
            definition = deps;
            deps = [];
        }

        var length = deps.length;

        if(!length) {
            definition(req);
        } else {
            var values = [], loaded = 0;

            deps.forEach(listen.bind(0, function(name, value) {
                values[deps.indexOf(name)] = value;

                if(++loaded >= length) {
                    definition.apply(0, values);
                }
            }));
        }
    }

    /** @export */
    require = req;

    /** @export */
    define = function(name, deps, definition) {
        if(typeof name !== 'string') {
            definition = deps;
            deps = name;
            name = null;
        }

        if(!definition) {
            definition = deps;
            deps = [];
        }

        req(deps, function() {
            if(typeof definition === 'function') {
                definition = definition.apply(0, arguments);
            }

            if(name) {
                resolve(name, definition);
            }
        });
    }


    // Js loader
    define('js!js', function() {
        return function(name) {
            if(name == 'test') {
                return {test: 'hello'};
            } else {
                throw 'Could not load: ' + name;
            }
        };
    });


    // Css loader
    define('js!css', function() {
        return function(name) {
            console.log(name);
        };
    });
}());


define('jam', function() {
    return function() {
        return 'jam';
    };
});

require(['test', 'jam'], function(test, jam) {
    console.log(test, jam);
});
