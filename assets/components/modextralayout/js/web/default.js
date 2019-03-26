(function () {
    function modExtraLayout(options) {
        let self = this;
        self['initialized'] = false;
        self['running'] = false;
        self['fatal'] = false;
        ['actionUrl'].forEach(function (val, i, arr) {
            if (typeof(options[val]) === 'undefined' || options[val] === '') {
                console.error('[modExtraLayout] Bad config.', arr);
                self['fatal'] = true;
            }
        });
        if (self['fatal']) {
            return;
        }

        /**
         * @type {object}
         */
        self.Base = {
            /**
             * Инициализирует класс.
             * @returns {boolean}
             */
            initialize: function (options) {
                if (!self['initialized']) {
                    //
                    self['config'] = {
                        postTimeout: 500, // замираем на пол секунды перед отсылкой запроса
                    };
                    self['classes'] = {};
                    self['selectors'] = {
                        form: '.js-mel-form',
                    };
                    self['sendDataTemplate'] = {
                        $element: null,
                        params: null,
                    };
                    self['sendData'] = $.extend({}, self['sendDataTemplate']);

                    //
                    Object.keys(options).forEach(function (key) {
                        if (['selectors'].indexOf(key) !== -1) {
                            return;
                        }
                        self.config[key] = options[key];
                    });
                    ['selectors'].forEach(function (key) {
                        if (options[key]) {
                            Object.keys(options[key]).forEach(function (i) {
                                self.selectors[i] = options.selectors[i];
                            });
                        }
                    });
                }
                self['initialized'] = true;

                return self['initialized'];
            },

            /**
             * Запускает основные действия.
             * @returns {boolean}
             */
            run: function () {
                if (self['initialized'] && !self['running']) {
                    /**
                     *
                     */
                    $(document).on('submit', self.selectors['form'], function (e) {
                        e.preventDefault();

                        let data = $form.serializeArray();
                        data.push({
                            name: 'action',
                            value: 'get/results',
                        });
                        // console.log('data', data);

                        // Готовим параметры запроса
                        let sendData = $.extend({}, self['sendDataTemplate']);
                        sendData['$element'] = $form;
                        sendData['params'] = data;
                        // console.log(sendData);

                        // Колбеки
                        let callbackBefore = function (response) {
                            console.log('callbackBefore response', response);
                        };
                        let callbackAfter = function (response) {
                            console.log('callbackAfter response', response);
                        };

                        // Шлём запрос
                        self.sendData = $.extend({}, sendData);
                        self.Submit.post(callbackBefore, callbackAfter);
                    });
                }
                self['running'] = true;

                return self['running'];
            },
        };

        /**
         * Отсылает запрос на сервер.
         * @type {object}
         */
        self.Submit = {
            timestamp: 0,
            post: function (beforeCallback, afterCallback) {
                if (!self.sendData['params']) {
                    return;
                }

                //
                let _post = function (beforeCallback, afterCallback) {
                    // Запускаем колбек перед отсылкой запроса
                    if (beforeCallback && $.isFunction(beforeCallback)) {
                        beforeCallback.call(this, self.sendData['params']);
                    }

                    $.post(self.config['actionUrl'], self.sendData['params'], function (response) {
                        // Запускаем колбек после отсылки запроса
                        if (afterCallback && $.isFunction(afterCallback)) {
                            afterCallback.call(this, response, self.sendData['params']);
                        }

                        if (response['success']) {
                            //
                        } else {
                            self.Message.error(response['message']);
                        }
                    }, 'json')
                        .fail(function () {
                            console.error('[modExtraLayout] Bad request.', self['sendData']);
                        })
                        .done(function () {
                        });
                };

                //
                if (self.config['postTimeout']) {
                    // Записываем текущий timestamp и через 0.5 секунды проверяем его
                    // Если он не изменён, то посылаем запрос на сервер
                    // Нужно для того, чтобы не слать кучу запросов
                    // Шлём только последний запрос
                    let timestamp = (new Date().getTime());
                    self.Submit['timestamp'] = timestamp;
                    window.setTimeout(function () {
                        if (self.Submit['timestamp'] === timestamp) {
                            _post(beforeCallback, afterCallback);
                        }
                    }, self.config['postTimeout']);
                } else {
                    _post(beforeCallback, afterCallback);
                }
            },
        };

        /**
         * Сообщения.
         * @type {object}
         */
        self.Message = {
            success: function (message) {
            },
            error: function (message) {
                alert(message);
            }
        };

        /**
         * Инструменты.
         * @type {object}
         */
        self.Tools = {};

        /**
         * Initialize && Run!
         */
        if (self.Base.initialize(options)) {
            self.Base.run();
        }
    }

    window['modExtraLayout'] = modExtraLayout;
})();