<?php

/**
 * Расширяем возможности Fenom
 */
class melPdoToolsOnFenomInit extends melPlugin
{
    /** @var fenom $fenom */
    protected $fenom;

    public function run()
    {
        $this->fenom = &$this->sp['fenom'];

        $this->allowedFunctions();
        $this->functions();
        $this->modifiers();

        // Добавляем класс modExtraLayout
        $this->fenom->mel = &$this->mel;
        $this->fenom->addAccessorSmart('mel', 'mel', Fenom::ACCESSOR_PROPERTY);
    }

    /**
     * Включаем доступные PHP функции
     */
    public function allowedFunctions()
    {
        $this->fenom->addAllowedFunctions(array(
            'array_merge',
            'array_diff',
            'array_map',
            // 'md5',
            // 'http_build_query',
        ));
    }

    /**
     * Добавляем функции
     */
    public function functions()
    {
        // usage: {chunk 'site/nav' ['param' => 'value']}
        $this->fenom->addFunction('chunk', function (array $params) {
            $this->mel->pdoTools->debugParserModifier($params[0], 'chunk', (!empty($params[1]) ? $params[1] : array()));
            $result = $this->mel->tools->getChunk(('@FILE ' . 'chunks/' . $params[0] . '.tpl'), (!empty($params[1]) ? $params[1] : array()));
            $this->mel->pdoTools->debugParserModifier($params[0], 'chunk', (!empty($params[1]) ? $params[1] : array()));

            return $result;
        });

        // usage: {snippet 'pdoResources' ['param' => 'value']}
        $this->fenom->addFunction('snippet', function (array $params) {
            $this->mel->pdoTools->debugParserModifier($params[0], 'snippet', (!empty($params[1]) ? $params[1] : array()));
            $result = $this->mel->pdoTools->runSnippet($params[0], (!empty($params[1]) ? $params[1] : array()));
            $this->mel->pdoTools->debugParserModifier($params[0], 'snippet', (!empty($params[1]) ? $params[1] : array()));

            return $result;
        });

        // usage: {open 'path_to_file'}
        $this->fenom->addFunction('open', function (array $params) {
            $result = '';
            $this->mel->pdoTools->debugParserModifier($params[0], 'open');
            if (file_exists($params[0])) {
                $result = file_get_contents($params[0]);
            }
            $this->mel->pdoTools->debugParserModifier($params[0], 'open');

            return $result;
        });
    }

    /**
     * Добавляем модификаторы
     */
    public function modifiers()
    {
        /** @var MobileDetect $md */
        $path = MODX_CORE_PATH . 'components/mobiledetect/';
        if (is_dir($path)) {
            if ($md = $this->modx->getService('mobiledetect', 'MobileDetect', $path)) {
                $key = $md->config['force_browser_variable'];
                $device = !empty($_GET) && array_key_exists($key, $_GET) ? $this->modx->stripTags($_GET[$key]) : '';

                if (empty($device)) {
                    $detector = $md->getDetector();
                    $device = ($detector->isMobile() ? ($detector->isTablet() ? 'tablet' : 'mobile') : 'standard');
                    $md->saveSettings($device);
                }

                // usage: {if ('standard' | detector) || ('tablet' | detector) || ('mobile' | detector)}{/if}
                $this->fenom->addModifier('detector', function ($value) use ($device) {
                    return $value == $device;
                });
            }
        }

        // usage: {'value_to_log' | log}
        $this->fenom->addModifier('log', function ($value) {
            $this->modx->log(1, print_r($value, 1));
        });
    }
}