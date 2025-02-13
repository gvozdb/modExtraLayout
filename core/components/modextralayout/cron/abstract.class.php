<?php

abstract class melCron
{
    /**
     * @var array $initialized
     */
    public $initialized = [];
    /**
     * @var modX $modx
     */
    public $modx;
    /**
     * @var modExtraLayout $mel
     */
    public $mel;
    /**
     * @var int $percent
     */
    protected $percent = 0;
    /**
     * @var null $microtime
     */
    protected $microtime = null;


    /**
     *
     */
    function __construct()
    {
        $this->time();
    }


    /**
     * @param array $config
     *
     * @return bool
     * @throws Exception
     */
    public function initialize(array $config = [])
    {
        $props = $this->getProperties(['silent']);
        $props['silent'] = !empty($props['silent']) && $props['silent'] !== 'false';
        if ($props['silent'] === true) {
            error_reporting(E_ERROR);
        }

        // $config = array_merge($config, $props);

        $this->getMODX($config);
        $this->getModExtraLayout($config);

        return ($this->initialized[$this->modx->context->get('key')] = true);
    }


    /**
     * Запуск основных действий
     */
    public function run()
    {
        //
    }


    /**
     * @param array $config
     *
     * @return null|modX
     * @throws Exception
     */
    protected function getMODX(array $config = [])
    {
        if (!is_object($this->modx)) {
            define('MODX_API_MODE', true);
            while (!isset($modx) && ($i = isset($i) ? --$i : 10)) {
                if (($file = dirname(!empty($file) ? dirname($file) : __FILE__) . '/index.php') AND !file_exists($file)) {
                    continue;
                }
                require_once $file;
            }
            if (!is_object($modx)) {
                $this->fatal('Access denied.');
            }
            $modx->getService('error', 'error.modError');
            $modx->getRequest();
            $modx->setLogLevel(modX::LOG_LEVEL_ERROR);
            $modx->setLogTarget('FILE');
            $modx->error->message = null;
            $modx->lexicon->load('default');
            if (!empty($config['ctx']) && $config['ctx'] !== $modx->context->get('key')) {
                $modx->switchContext($config['ctx']);
            }
            $this->modx = &$modx;
        }
        return $this->modx;
    }


    /**
     * @param array $config
     *
     * @return null|modExtraLayout
     * @throws Exception
     */
    protected function getModExtraLayout(array $config = [])
    {
        if (!is_object($this->mel)) {
            if (!$this->mel = $this->modx->getService('modextralayout', 'modExtraLayout', $this->modx->getOption('mel_core_path', null, MODX_CORE_PATH . 'components/modextralayout/') . 'model/modextralayout/')) {
                $this->fatal('Class modExtraLayout not found');
                exit();
            }
            $this->mel->initialize($this->modx->context->key, $config);
        }
        return $this->mel;
    }


    /**
     * Возвращает параметры запуска скрипта
     *
     * @param string|array $keys
     *
     * @return array
     */
    protected function getProperties($keys)
    {
        if (!is_array($keys)) {
            $keys = array_map('trim', explode(',', $keys));
        }
        if ($this->isCli()) {
            $params = getopt('', array_map(function ($v) {
                return $v . '::';
            }, $keys));
        } else {
            $params = array_intersect_key($_GET, array_flip($keys));
        }

        return $params;
    }

    /**
     * @return bool
     */
    protected function isCli()
    {
        return defined('STDIN') || (substr(PHP_SAPI, 0, 3) == 'cgi' && getenv('TERM'));
    }

    /**
     * Печатает прогресс-бар для командной строки/лог файла
     *
     * @param int $actual
     * @param int $total
     * @param string $format
     *
     * @return int
     */
    protected function progressBar($actual, $total, $format = '')
    {
        $params = $this->getProperties(['silent']);
        $params['silent'] = !empty($params['silent']) && $params['silent'] !== 'false';

        if ($this->percent == 100) {
            $this->percent = 0;
        }
        $width = 25;
        $percent = floor(($actual * 100) / $total);
        $bar_percent = ceil(($width * $percent) / 100);

        if ($this->isCli() && $params['silent'] === false) {
            $output = sprintf("%s%%[%s>%s] %s\r", $percent, str_repeat("=", $bar_percent), str_repeat(" ", $width - $bar_percent), $format);
            fwrite(STDOUT, $output);
        }

        return ($this->percent = $percent);
    }

    /**
     * Печатает текст на экран
     *
     * @param string $msg
     */
    protected function log($msg)
    {
        $params = $this->getProperties(['silent']);
        $params['silent'] = !empty($params['silent']) && $params['silent'] !== 'false';

        if ($params['silent'] === false) {
            $status = '';
            $status .= "[{$this->status()}] ";

            if ($this->isCli()) {
                fwrite(STDOUT, $status . $msg . PHP_EOL);
            } else {
                echo '<pre style="margin:4px 0;">';
                echo "<b>{$status}</b>";
                echo $msg . PHP_EOL;
                echo '</pre>';
            }
        }
    }

    /**
     * Выбрасывает исключение
     *
     * @param $msg
     *
     * @throws Exception
     */
    protected function fatal($msg)
    {
        // $this->deinitialize();
        // $this->modx->log(XPDO::LOG_LEVEL_ERROR, $msg);

        exit($msg . PHP_EOL); // throw new Exception($msg);
    }

    /**
     * Возвращает время и память, затраченные скриптом
     *
     * @return string
     */
    protected function status()
    {
        $datetime = date('H:i:s'); // d.m.Y H:i:s
        $memory = number_format(memory_get_usage() / 1024 / 1024, 0, '', '');
        $time = $this->time();

        return "{$datetime} / {$time}s / {$memory}Mb";
    }

    /**
     * Возвращает время выполнения скрипта
     *
     * @return float
     */
    protected function time()
    {
        $time = ($this->microtime !== null) ? microtime(true) - $this->microtime : 0;
        if ($this->microtime === null) {
            $this->microtime = microtime(true); // Время старта
        }

        return (float)number_format($time, 0, '', '');
    }
}