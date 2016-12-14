(function () {
    function modExtraLayout(options) {
        //
        ['assetsUrl', 'actionUrl'].forEach(function (val, i, arr) {
            if (typeof(options[val]) == 'undefined' || options[val] == '') {
                console.error('[modExtraLayout] Bad config', arr);
                return;
            }
        });

        //
        var self = this;
        self.initialized = false;
        self.running = false;

        /**
         * Инициализирует класс.
         * @returns {boolean}
         */
        self.initialize = function (options) {
            if (!self.initialized) {
                //
                self.config = {};
                self.selectors = {};

                //
                Object.keys(options).forEach(function (key) {
                    if (['selectors'].indexOf(key) !== -1) {
                        return;
                    }
                    self.config[key] = options[key];
                });

                //
                ['selectors'].forEach(function (key) {
                    if (options[key]) {
                        Object.keys(options[key]).forEach(function (i) {
                            self.selectors[i] = options.selectors[i];
                        });
                    }
                });
            }
            self.initialized = true;

            return self.initialized;
        };

        /**
         * Запускает основные действия.
         * @returns {boolean}
         */
        self.run = function () {
            if (self.initialized && !self.running) {
                //
            }
            self.running = true;

            return self.running;
        };

        // Initialize && Run!
        if (self.initialize(options)) {
            self.run();
        }
    }

    window.modExtraLayout = modExtraLayout;
})();