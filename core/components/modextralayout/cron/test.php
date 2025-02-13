<?php
/**
 * Скрипт демонстрирующий реализацию крон-скрипта.
 *
 * Запускается через крон.
 *
 * Пример задания для крон, запуск раз в минуту (после #):
 * # * * * * * sudo -u{user} php /path_to_site/core/components/modextralayout/cron/test.php
 *
 * где `{user}` - это имя пользователя в системе, под которым крутится сайт, в случае если вы зашли в терминал от рута или судо юзера, в ином случае конструкцию `sudo -u{user}` нужно удалить.
 */

include_once dirname(__FILE__) . '/abstract.class.php';

class melCronRoutine extends melCron
{
    /**
     * @var array $services
     */
    public $services = [];


    /**
     *
     */
    function __construct()
    {
        parent::__construct();

        $this->services = [
            'service1' => [],
            'service2' => [],
        ];

        $this->checkDuplicateRuns();
    }


    /**
     * Запуск основных действий
     *
     * @throws Exception
     */
    public function run()
    {
        // Get script properties
        $properties = $this->getProperties();

        //
        // Run actions
        $this->log('Старт!');
        if (!empty($properties['debug'])) {
            $this->log('* Запущен в дебаг режиме');
        }

        $max = 16;
        for ($i = 1; $i <= $max; ++$i) {
            // $this->log('Итерация номер ' . $i);
            $this->progressBar($i, $max, ($i . ' / ' . $max));
            sleep(1);
        }
        $this->log("Пройдено {$max} итераций...");

        $this->log('Завершено!');
    }


    /**
     * @param null|string|array $keys
     * @param bool $validation
     *
     * @throws Exception
     */
    protected function getProperties($keys = null, $validation = false)
    {
        $keys = @$keys ?: [
            'service',
            'debug',
        ];
        $properties = parent::getProperties($keys);

        if ($validation === true) {
            if (!in_array($properties['service'], array_keys($this->services))) {
                $this->fatal('Укажите корректный параметр --service! Допустимые значения: ' . join(', ', array_keys($this->services)));
            }
        }

        return $properties;
    }


    /**
     * Checking duplicate runs
     *
     * @throws Exception
     */
    public function checkDuplicateRuns()
    {
        $properties = $this->getProperties(null, true);

        $check_duplicates = array_diff(explode(PHP_EOL, shell_exec('ps xu -j | grep ' . __FILE__ . ' | grep -v grep')), ['']);

        // Фильтруем только те, что с текущим параметром --service={$properties['service']}
        $check_duplicates = array_filter($check_duplicates, function ($v) use ($properties) {
            return !!stristr($v, "--service={$properties['service']}");
        });

        if (count($check_duplicates) > 1) {
            $this->fatal('There is already a running copy of this script!');
        }
    }
}

$cron = new melCronRoutine();
$cron->initialize(); // ['ctx' => @$_REQUEST['ctx']]
$cron->run();
