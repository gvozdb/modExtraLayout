(function () {
    function modExtraLayout(options) {
        ['actionUrl'].forEach(function (val, i, arr) {
            if (typeof(options[val]) == 'undefined' || options[val] == '') {
                console.error('[modExtraLayout] Bad config.', arr);
                return;
            }
        });
        var self = this;
        self['initialized'] = false;
        self['running'] = false;

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
                    self['config'] = {};
                    self['classes'] = {};
                    self['selectors'] = {};
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

                    //
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
                    // $(document).on('click', self.selectors['voteButton'], function (e) {
                    //     e.preventDefault();
                    //
                    //     var $button = $(this);
                    //     var $object = $button.closest(self.selectors['voteObject']);
                    //     if ($object.length && $button.length) {
                    //         var object = $object.data('nkn-vote-object');
                    //         var value = $button.data('nkn-vote-value');
                    //         var sendData = $.extend({}, self['sendDataTemplate']);
                    //
                    //         // Готовим параметры запроса
                    //         sendData['$element'] = $button;
                    //         sendData['params'] = {
                    //             action: 'object/vote',
                    //             object: object,
                    //             value: value,
                    //         };
                    //
                    //         // Если уже есть активная кнопка - запомним в параметры
                    //         var $last = $object.find(self.selectors['voteButton']).filter('.' + self.classes['voteButtonActive']);
                    //         if ($last.length && !$last.data('nkn-vote-tmp')) {
                    //             console.log('$last', $last);
                    //             sendData['$last'] = $last;
                    //         }
                    //
                    //         // console.log(sendData);
                    //
                    //         // Шлём запрос
                    //         self.sendData = $.extend({}, sendData);
                    //         self.Submit.post();
                    //
                    //         // // Шлём запрос
                    //         // if (!self.Submit['status']) {
                    //         //     self.sendData = $.extend({}, sendData);
                    //         //     self.Submit.post();
                    //         // } else {
                    //         //     // Пишем в очередь
                    //         //     self.Queue.push(sendData);
                    //         // }
                    //     }
                    // });
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
            before: function () {
                /**
                 *
                 */
                // if (self.sendData.params['action'] == 'object/vote') {
                //     // Реализуем оптимистичный интерфейс
                //     var $button = self.sendData['$element'];
                //     var $last = self.sendData['$last'];
                //     var $object = $button.closest(self.selectors['voteObject']);
                //     var $number = $button.find(self.selectors['voteNumber']);
                //     var number = $number.text();
                //     number = parseInt(number.replace(' ', '').replace(',', ''));
                //
                //     $object.find(self.selectors['voteButton']).each(function (idx, el) {
                //         var $btn = $(el);
                //         if ($button.get(0) != $btn.get(0)) {
                //             if ($btn.hasClass(self.classes['voteButtonActive'])) {
                //                 $btn.removeClass(self.classes['voteButtonActive']); // .removeAttr('data-nkn-vote-tmp')
                //                 var $num = $btn.find(self.selectors['voteNumber']);
                //                 var num = $num.text();
                //                 num = parseInt(num.replace(' ', '').replace(',', ''));
                //                 $num.text(self.Tools.number_format((num - 1), 0, '.', ' '));
                //             }
                //         }
                //     });
                //     $button.toggleClass(self.classes['voteButtonActive']).data('nkn-vote-tmp', 1);
                //     if ($button.hasClass(self.classes['voteButtonActive'])) {
                //         $number.text(self.Tools.number_format((number + 1), 0, '.', ' '));
                //     } else {
                //         $number.text(self.Tools.number_format((number - 1), 0, '.', ' '));
                //     }
                //
                //     // if (!!$last && $last.length && $last.get(0) != $button.get(0)) {
                //     //     console.log('before submit $last', $last);
                //     //     console.log('before submit $last.hasClass(self.classes[voteButtonActive]) 1', $last.hasClass(self.classes['voteButtonActive']));
                //     //     $last.toggleClass(self.classes['voteButtonActive']);
                //     //     console.log('before submit $last.hasClass(self.classes[voteButtonActive]) 2', $last.hasClass(self.classes['voteButtonActive']));
                //     // }
                // }
            },
            after: function (response) {
                /**
                 *
                 */
                // if (self.sendData.params['action'] == 'object/vote') {
                //     var $button = self.sendData['$element'];
                //     var $object = $button.closest(self.selectors['voteObject']);
                //
                //     // При безуспешном запросе
                //     if (!response['success']) {
                //         var $last = self.sendData['$last'];
                //
                //         // 1) Переключаем класс активной кнопки
                //         $button.toggleClass(self.classes['voteButtonActive']).removeAttr('data-nkn-vote-tmp');
                //
                //         // 2) Возвращаем класс активной кнопки последнему элементу
                //         if (!!$last && $last.length) {
                //             $last.toggleClass(self.classes['voteButtonActive']);
                //         }
                //     }
                //     // При успешном запросе
                //     else {
                //         var $rating = $object.find(self.selectors['voteRating']);
                //         var $stripe = $object.find(self.selectors['voteStripe']);
                //
                //         // 1) Удаляем обозначение временного назначения класса
                //         $button.removeAttr('data-nkn-vote-tmp');
                //
                //         // 2) Ставим новое значение рейтинга
                //         if (typeof(response.data['rating']) != 'undefined') {
                //             var rating_old = parseFloat($rating.text().replace(' ', ''));
                //             var rating_new = parseFloat(response.data['rating']);
                //             console.log('rating_old', rating_old);
                //             console.log('rating_new', rating_new);
                //
                //             // $rating.text(rating_new);
                //
                //             self.Tools.animateNumbers($rating, rating_old, rating_new, 700);
                //         }
                //
                //         // 3) Ставим новую длину полосы
                //         if (typeof(response.data['rating']) != 'undefined') {
                //             $stripe.css({minWidth: response.data['rating'] + '%'});
                //         }
                //     }
                // }
            },
            post: function (callback) {
                if (!self.sendData['params'] || !self.sendData.params['action']) {
                    return;
                }
                self.Submit.before();

                $.post(self.config['actionUrl'], self.sendData['params'], function (response) {
                    console.log('self.Submit.post() response', response);

                    self.Submit.after(response);

                    if (response['success']) {
                        self.Message.success(response['message']);

                        // Запускаем колбек
                        if (callback && $.isFunction(callback)) {
                            callback.call(this, response, self.sendData['params']);
                        }
                    } else {
                        self.Message.error(response['message']);
                    }
                }, 'json')
                    .fail(function () {
                        console.error('[modExtraLayout] Bad request.', self['sendData']);
                    })
                    .done(function () {
                    });
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