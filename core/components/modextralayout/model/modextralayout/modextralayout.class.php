<?php

class modExtraLayout
{
    public $config = array();
    public $initialized = array();
    /** @var modX $modx */
    public $modx;
    /** @var melTools $tools */
    public $tools;
    /** @var pdoTools $pdoTools */
    public $pdoTools;

    /**
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx = &$modx;

        $corePath = $this->modx->getOption('mel_core_path', $config, MODX_CORE_PATH . 'components/modextralayout/');
        $assetsUrl = $this->modx->getOption('mel_assets_url', $config, MODX_ASSETS_URL . 'components/modextralayout/');
        $assetsPath = $this->modx->getOption('mel_assets_path', $config, MODX_ASSETS_PATH . 'components/modextralayout/');

        $this->config = array_merge(array(
            'assetsUrl' => $assetsUrl,
            'assetsPath' => $assetsPath,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $assetsUrl . 'connector.php',
            'actionUrl' => $assetsUrl . 'action.php',

            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'pluginsPath' => $corePath . 'plugins/',
            'handlersPath' => $corePath . 'handlers/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'templatesPath' => $corePath . 'elements/templates/',
            'chunkSuffix' => '.chunk.tpl',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',

            'prepareResponse' => false,
            'jsonResponse' => false,
        ), $config);

        $this->modx->addPackage('modextralayout', $this->config['modelPath']);
        $this->modx->lexicon->load('modextralayout:default');
    }

    /**
     * @param string $ctx
     * @param array  $sp
     *
     * @return boolean
     */
    public function initialize($ctx = 'web', $sp = array())
    {
        $this->config = array_merge($this->config, $sp, array('ctx' => $ctx));

        $this->getTools();
        if ($pdoTools = $this->getPdoTools()) {
            $pdoTools->setConfig($this->config);
        }

        if (empty($this->initialized[$ctx])) {
            switch ($ctx) {
                case 'mgr':
                    break;
                default:
                    if (!defined('MODX_API_MODE') || !MODX_API_MODE) {
                        // $this->loadFrontendScripts();
                    }
                    break;
            }
        }

        return ($this->initialized[$ctx] = true);
    }

    /**
     * @param string $objectName
     * @param array  $sp
     *
     * @return bool
     */
    public function loadFrontendScripts($objectName = '', array $sp = array())
    {
        if (empty($objectName)) {
            $objectName = 'modExtraLayout';
        }
        $objectName = trim($objectName);

        if (empty($this->modx->loadedjscripts[$objectName]) && (!defined('MODX_API_MODE') || !MODX_API_MODE)) {
            $pls = $this->tools->makePlaceholders($this->config);
            if ($css = trim($this->modx->getOption('mel_frontend_css'))) {
                $this->modx->regClientCSS(str_replace($pls['pl'], $pls['vl'], $css));
            }
            if ($js = trim($this->modx->getOption('mel_frontend_js'))) {
                $this->modx->regClientScript(str_replace($pls['pl'], $pls['vl'], $js));
            }

            $params = $this->modx->toJSON(array_merge(array(
                'assetsUrl' => $this->config['assetsUrl'],
                'actionUrl' => $this->config['actionUrl'],
            ), $sp));

            $this->modx->regClientScript('<script type="text/javascript">
                if (typeof(' . $objectName . 'Cls) == "undefined") {
                    var ' . $objectName . 'Cls = new ' . $objectName . '(' . $params . ');
                }
            </script>', true);

            $this->modx->loadedjscripts[$objectName] = true;
        }

        return !empty($this->modx->loadedjscripts[$objectName]);
    }

    /**
     * @return melTools
     */
    public function getTools()
    {
        if (!is_object($this->tools)) {
            if ($class = $this->modx->loadClass('tools.melTools', $this->config['handlersPath'], true, true)) {
                $this->tools = new $class($this->modx, $this->config);
            }
        }

        return $this->tools;
    }

    /**
     * @return pdoTools
     */
    public function getPdoTools()
    {
        if (class_exists('pdoTools') && !is_object($this->pdoTools)) {
            $this->pdoTools = $this->modx->getService('pdoTools');
        }

        return $this->pdoTools;
    }
}