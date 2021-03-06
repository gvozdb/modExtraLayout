(function () {
    function modExtraLayoutMain(options) {
        let self = this;
        self['initialized'] = false;
        self['running'] = false;
        self['fatal'] = false;
        ['actionUrl'].forEach(function (val, i, arr) {
            if (typeof(options[val]) === 'undefined' || options[val] === '') {
                console.error('[modExtraLayoutMain] Bad config.', arr);
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
                    };
                    self['classes'] = {
                        loading: 'is-loading',
                        active: 'is-active',
                    };
                    self['selectors'] = {
                        form: '.js-mel-form',
                    };
                    self['messages'] = {
                    };
                    self['sendDataTemplate'] = {
                        formData: null,
                    };
                    self['sendData'] = $.extend({}, self['sendDataTemplate']);

                    //
                    Object.keys(options).forEach(function (key) {
                        if (['selectors', 'messages'].indexOf(key) !== -1) {
                            return;
                        }
                        self.config[key] = options[key];
                    });
                    ['selectors', 'messages'].forEach(function (key) {
                        if (options[key]) {
                            Object.keys(options[key]).forEach(function (i) {
                                self[key][i] = options[key][i];
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

                        // Prepare sending data
                        let sendData = $.extend({}, self['sendDataTemplate']);
                        sendData['formData'] = data;
                        // console.log(sendData);

                        // Submit
                        self.sendData = $.extend({}, sendData);
                        self.Submit.post(
                            function (response) {
                                console.log('callbackBefore response', response);
                            },
                            function (response) {
                                console.log('callbackAfter response', response);
                            }
                        );
                    });
                }
                self['running'] = true;

                return self['running'];
            },
        };

        /**
         * Отсылает запрос на сервер.
         *
         * @type {{post: post, timeoutInstance: *, timeout: number}}
         */
        self.Submit = {
            timeout: 500, // замираем на N секунд перед отсылкой запроса
            timeoutInstance: undefined,
            post: function (beforeCallback, afterCallback, options, timeout) {
                if (!self.sendData['formData']) {
                    return;
                }
                if (typeof(options) === 'undefined') {
                    options = {};
                }
                if (typeof(timeout) === 'undefined') {
                    timeout = self.Submit['timeout'];
                }
                timeout = parseInt(timeout) || 0;

                //
                self.Submit['timeoutInstance'] && window.clearTimeout(self.Submit['timeoutInstance']);
                self.Submit['timeoutInstance'] = window.setTimeout(function () {
                    // Запускаем колбек перед отсылкой запроса
                    if (beforeCallback && $.isFunction(beforeCallback)) {
                        beforeCallback.call(this, self.sendData['formData']);
                    }

                    $.ajax($.extend({
                        type: 'POST',
                        url: self.config['actionUrl'],
                        data: self.sendData['formData'],
                        success: function (response) {
                            // Запускаем колбек после отсылки запроса
                            if (afterCallback && $.isFunction(afterCallback)) {
                                afterCallback.call(this, response, self.sendData['formData']);
                            }

                            if (response['success']) {
                                //
                            } else {
                                self.Message.error(response['message']);
                            }
                        },
                        dataType: 'json',
                    }, options))
                        .fail(function () {
                            console.error('[modExtraLayoutMain] Bad request.', self['sendData']);
                        })
                        .done(function () {
                        });
                }, timeout);
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
        self.Base.initialize(options) && self.Base.run();
    }

    window['modExtraLayoutMain'] = modExtraLayoutMain;
})();