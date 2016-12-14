<?php

class FindIt
{
    /** @var null|string $str */
    public $str;
    /** @var array $config */
    public $config;
    /** @var bool $loadStyles */
    protected $loadStyles;

    /**
     * @param null|string $str
     * @param array       $config
     *
     * @throws Exception
     */
    public function __construct($str = null, array $config = array())
    {
        if (empty($str)) {
            if (!empty($argv)) {
                $str = !empty($argv[1]) ? $argv[1] : null;
            } else {
                $str = !empty($_REQUEST['str']) ? $_REQUEST['str'] : null;
            }
        }
        if (empty($str)) {
            throw new Exception('Не найдена строка для поиска.' . PHP_EOL, 0);
        }
        $this->str = $str;
        $this->config = array_merge(array(), $config);
    }

    /**
     * @param null $path
     *
     * @return bool
     */
    public function run($path = null)
    {
        $this->loadStyles();
        if (empty($path)) {
            $path = dirname(__FILE__);
        }
        $this->find($path);

        return true;
    }

    /**
     * @param string $path
     */
    protected function find($path)
    {
        foreach (scandir($path) as $file) {
            if (strpos($file, '.') === 0) {
                continue;
            }

            $target = preg_replace('/\/+/', '/', ($path . '/' . $file));
            if (is_dir($target)) {
                $this->find($target);
            } elseif (is_file($target)) {
                $content = file_get_contents($target);
                if (!$positions = $this->strPosAll($content, $this->str)) {
                    continue;
                }

                foreach ($positions as $pos) {
                    // Получаем файл разбитый по строкам
                    $strings = file($target);

                    // Считаем кол-во строк от начала файла
                    $number_str = count(explode(PHP_EOL, substr($content, 0, $pos)));

                    //
                    $str_prepare = '<span class="find-it__data-highlight">' . htmlspecialchars($this->str) . '</span>';

                    //
                    $data = $strings[$number_str - 1];
                    $data = str_replace($this->str, '{{strrepl}}', $data);
                    $data = nl2br(htmlspecialchars($data));
                    $data = str_replace('{{strrepl}}', $str_prepare, $data);

                    //
                    $filepath = preg_replace('/^' . addcslashes(dirname(__FILE__), '/') . '(.+)/u', '.$1', $target);

                    //
                    print "
                        <div class=\"find-it\">
                            <div class=\"find-it__file\">{$filepath} <span class=\"find-it__number-str\">: {$number_str}</span></div>
                            <div class=\"find-it__data\">{$data}</div>
                        </div>
                    ";
                }
            }
        }
    }

    /**
     * @return bool
     */
    protected function loadStyles()
    {
        if (!$this->loadStyles) {
            print "<style>
                .find-it {
                    background-color: rgba(0, 0, 0, .1);
                    padding: 20px;
                    margin: 20px 0;
                    font-size: 1.2em;
                    line-height: 1.4em;
                }
                .find-it__file {
                    margin: 0 0 10px;
                    
                    font-style: italic;
                    
                }
                .find-it__str-count {
                    font-weight: 700;
                }
                .find-it__data {
                    background-color: rgba(0, 0, 0, .1);
                    padding: 20px;
                }
                .find-it__data-highlight {
                    display: inline-block;
                    background-color: rgba(255, 220, 97, .7);
                    padding: 0 2px;
                }
            </style>";
        }

        return ($this->loadStyles = true);
    }

    /**
     * @param $haystack
     * @param $needle
     *
     * @return array|bool
     */
    protected function strPosAll($haystack, $needle)
    {
        $s = $i = 0;
        $positions = array();

        while (is_integer($i)) {
            $i = strpos($haystack, $needle, $s);
            if (is_integer($i)) {
                $positions[] = $i;
                $s = $i + strlen($needle);
            }
        }

        if (!empty($positions)) {
            return $positions;
        } else {
            return false;
        }
    }
}

try {
    $find = new FindIt();
    $find->run();
}
catch (Exception $e) {
    exit('(' . $e->getCode() . ') ' . $e->getMessage());
}