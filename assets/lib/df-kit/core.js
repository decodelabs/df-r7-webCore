define([
    'jquery',
    'underscore'
], function($, _) {
    var core;
    return core = {
        baseUrl: null,
        cts: null,
        location: null,
        layout: null,

        init: function() {
            if(!this.cts) {
                var viewData = document.getElementById('custom-view-data');
                this.baseUrl = viewData.getAttribute('data-base');
                this.cts = viewData.getAttribute('data-cts');
            }

            var $body = $(document.body);
            this.location = $body.data('location');
            this.layout = $body.data('layout');
        },

        call: function(callback, data) {
            if(typeof(callback) == 'function') {
                callback.call(this, data);
            }
        },

        component: function(obj) {
            obj = _.extend(obj, this.Events);

            if(typeof obj.init == 'function') {
                obj.init.apply(obj);
            }

            return obj;
        },

        isUrlExternal: function(url) {
            var domain = function(url) {
                return url.replace('http://','').replace('https://','').split('/')[0];
            };

            return domain(location.href) !== domain(url);
        },

        openWindow: function(url, title, width, height) {
            var left = (screen.width / 2) - (width / 2),
                top = (screen.height / 2) - (height / 2);
            window.open(url, title, 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height='+height+',width='+width+',top='+top+',left='+left);
        },




        // Events
        Events: {
            on: function(name, callback, context) {
                if(this._multiplexEvents(this, 'on', name, [callback, context]) || !callback) {
                    return this;
                }

                this._events || (this._events = {});
                var events = this._events[name] || (this._events[name] = []);
                events.push({callback: callback, context: context, ctx: context || this});
                return this;
            },

            once: function(name, callback, context) {
                if(this._multiplexEvents(this, 'on', name, [callback, context]) || !callback) {
                    return this;
                }

                var _this = this,
                    once = _.once(function() {
                        _this.off(name, once);
                        callback.apply(this, arguments);
                    });

                once._callback = callback;
                return this.on(name, once, context);
            },

            _multiplexEvents: function(obj, action, name, args) {
                if(!name) return false;

                if(typeof name === 'object') {
                    for(var key in name) {
                        obj[action].apply(obj, [key, name[key]].concat(args));
                    }

                    return true;
                }

                var splitter = /\s+/;

                if(splitter.test(name)) {
                    var names = name.split(splitter);

                    for(var i = 0, length = names.length; i < length; i++) {
                        obj[action].apply(obj, [names[i]].concat(args));
                    }

                    return true;
                }

                return false;
            },

            off: function(name, callback, context) {
                var retain, ev, events, names, i, l, j, k;
                
                if(!this._events || this._multiplexEvents(this, 'off', name, [callback, context])) {
                    return this;
                }

                if(!name && !callback && !context) {
                    this._events = void 0;
                    return this;
                }

                names = name ? [name] : _.keys(this._events);

                for(i = 0, l = names.length; i < l; i++) {
                    name = names[i];

                    if(events = this._events[name]) {
                        this._events[name] = retain = [];

                        if(callback || context) {
                            for(j = 0, k = events.length; j < k; j++) {
                                ev = events[j];

                                if((callback && callback !== ev.callback && callback !== ev.callback._callback) ||
                                    (context && context !== ev.context)) {
                                    retain.push(ev);
                                }
                            }
                        }

                        if(!retain.length) delete this._events[name];
                    }
                }

                return this;
            },

            trigger: function(name) {
                if(!this._events) return this;
                var args = Array.prototype.slice.call(arguments, 1);
                if(this._multiplexEvents(this, 'trigger', name, args)) return this;
                var events = this._events[name];
                var allEvents = this._events.all;
                if(events) this._triggerEvents(events, args);
                if(allEvents) this._triggerEvents(allEvents, arguments);
                return this;
            },

            _triggerEvents: function(events, args) {
                var ev, i = -1, l = events.length, a1 = args[0], a2 = args[1], a3 = args[2];
                switch (args.length) {
                    case 0: while (++i < l) (ev = events[i]).callback.call(ev.ctx); return;
                    case 1: while (++i < l) (ev = events[i]).callback.call(ev.ctx, a1); return;
                    case 2: while (++i < l) (ev = events[i]).callback.call(ev.ctx, a1, a2); return;
                    case 3: while (++i < l) (ev = events[i]).callback.call(ev.ctx, a1, a2, a3); return;
                    default: while (++i < l) (ev = events[i]).callback.apply(ev.ctx, args); return;
                }
            },


            listenTo: function(obj, name, callback) {
                var listeningTo = this._listeningTo || (this._listeningTo = {});
                var id = obj._listenId || (obj._listenId = _.uniqueId('l'));
                listeningTo[id] = obj;
                if(!callback && typeof name === 'object') callback = this;
                obj.on(name, callback, this);
                return this;
            },

            listenToOne: function(obj, name, callback) {
                var listeningTo = this._listeningTo || (this._listeningTo = {});
                var id = obj._listenId || (obj._listenId = _.uniqueId('l'));
                listeningTo[id] = obj;
                if(!callback && typeof name === 'object') callback = this;
                obj.once(name, callback, this);
                return this;
            },

            stopListening: function(obj, name, callback) {
                var listeningTo = this._listeningTo;
                if(!listeningTo) return this;
                var remove = !name && !callback;
                if(!callback && typeof name === 'object') callback = this;
                if(obj) (listeningTo = {})[obj._listenId] = obj;

                for(var id in listeningTo) {
                    obj = listeningTo[id];
                    obj.off(name, callback, this);
                    if(remove || _.isEmpty(obj._events)) delete this._listenId[id];
                }

                return this;
            }
        }
    };
});