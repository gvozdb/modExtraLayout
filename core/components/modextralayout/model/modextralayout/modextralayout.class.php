<?php

class modExtraLayout
{
    public $version = '1.0.0';
    public $config = [];
    public $initialized = [];
    /**
     * @var modX $modx
     */
    public $modx;
    /**
     * @var melTools $tools
     */
    public $tools;
    /**
     * @var pdoTools $pdoTools
     */
    public $pdoTools;
    /**
     * @var pdoFetch $pdoFetch
     */
    public $pdoFetch;


    /**
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = [])
    {
        $this->modx = &$modx;

        $corePath = $this->modx->getOption('mel_core_path', $config, MODX_CORE_PATH . 'components/modextralayout/');
        $assetsUrl = $this->modx->getOption('mel_assets_url', $config, MODX_ASSETS_URL . 'components/modextralayout/');
        $assetsPath = $this->modx->getOption('mel_assets_path', $config, MODX_ASSETS_PATH . 'components/modextralayout/');

        $this->config = array_merge([
            'assetsUrl' => $assetsUrl,
            'assetsPath' => $assetsPath,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $assetsUrl . 'connector.php',
            'actionUrl' => $assetsUrl . 'action.php',

            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'vendorPath' => $corePath . 'vendor/',
            'pluginsPath' => $corePath . 'plugins/',
            'handlersPath' => $corePath . 'handlers/',
            // 'chunksPath' => $corePath . 'elements/chunks/',
            'templatesPath' => $corePath . 'elements/templates/',
            // 'chunkSuffix' => '.chunk.tpl',
            // 'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',

            'prepareResponse' => false,
            'jsonResponse' => false,
        ], $config);

        $this->modx->addPackage('modextralayout', $this->config['modelPath']);
        $this->modx->lexicon->load('modextralayout:default');
    }

    /**
     * @param string $ctx
     * @param array  $sp
     *
     * @return boolean
     */
    public function initialize($ctx = 'web', $sp = [])
    {
        $this->config = array_merge($this->config, $sp, ['ctx' => $ctx]);

        $this->getTools();
        if ($pdoTools = $this->getPdoTools()) {
            // $pdoTools->setConfig($this->config);
        }
        if ($pdoFetch = $this->getPdoFetch()) {
            // $pdoFetch->setConfig($this->config);
        }

        if (empty($this->initialized[$ctx])) {
            switch ($ctx) {
                case 'mgr':
                    break;
                default:
                    // if (!defined('MODX_API_MODE') || !MODX_API_MODE) {
                    //     $this->loadFrontendScripts();
                    // }
                    break;
            }
        }

        return ($this->initialized[$ctx] = true);
    }


    /**
     * @param array $services
     *
     * @return bool
     */
    public function loadFrontendScripts(array $services = [])
    {
        $flag = false;
        if (empty($services)) {
            $services = [
                'main' => [],
            ];
        }

        $version = time(); // $this->version;
        foreach ([
            'main',
        ] as $service_key) {
            if (!isset($services[$service_key])) {
                continue;
            }
            $service_name = 'modExtraLayout' . ucfirst($service_key);
            $service_properties = is_array($services[$service_key]) ? $services[$service_key] : [];

            if (empty($this->modx->loadedjscripts[$service_name]) && (!defined('MODX_API_MODE') || !MODX_API_MODE)) {
                $pls = $this->tools->makePlaceholders($this->config);
                if ($css = trim($this->modx->getOption('mel_frontend_' . $service_key . '_css'))) {
                    $this->modx->regClientCSS(str_replace($pls['pl'], $pls['vl'], $css . '?v=' . $version));
                }
                if ($js = trim($this->modx->getOption('mel_frontend_' . $service_key . '_js'))) {
                    $this->modx->regClientScript(str_replace($pls['pl'], $pls['vl'], $js . '?v=' . $version));
                }
                unset($pls, $css, $js);

                $params = $this->modx->toJSON(array_merge([
                    // 'assetsUrl' => $this->config['assetsUrl'],
                    'actionUrl' => $this->config['actionUrl'],
                ], $service_properties));

                $this->modx->regClientScript('<script>
                    if (typeof(' . $service_name . 'Cls) === "undefined") {
                        var ' . $service_name . 'Cls = new ' . $service_name . '(' . $params . ');
                    }
                </script>', true);

                $this->modx->loadedjscripts[$service_name] = true;
            }

            $flag = empty($flag) ? !empty($this->modx->loadedjscripts[$service_name]) : $flag;
        }

        return $flag;
    }


    /**
     * @param string $page
     *
     * @return bool
     */
    public function loadManagerScripts($page)
    {
        /** @var modManagerController $controller */
        $controller = $this->modx->controller;
        if (!(is_object($controller) && ($controller instanceof modManagerController))) {
            return false;
        }
        $version = $this->version;

        // Lexicon
        $controller->addLexiconTopic('modextralayout:default');

        // CSS
        $controller->head['css'][] = $this->config['cssUrl'] . 'mgr/main.css?v=' . $version;
        $controller->head['css'][] = $this->config['cssUrl'] . 'mgr/bootstrap.buttons.css?v=' . $version;

        // Vendors
        $controller->head['js'][] = $this->config['jsUrl'] . 'vendor/strftime.min.js';

        // JS
        $controller->head['js'][] = $this->config['jsUrl'] . 'mgr/modextralayout.js?v=' . $version;

        $controller->head['js'][] = $this->config['jsUrl'] . 'mgr/misc/default/grid.js?v=' . $version;
        $controller->head['js'][] = $this->config['jsUrl'] . 'mgr/misc/default/gridlocal.js?v=' . $version;
        $controller->head['js'][] = $this->config['jsUrl'] . 'mgr/misc/default/window.js?v=' . $version;
        $controller->head['js'][] = $this->config['jsUrl'] . 'mgr/misc/default/formpanel.js?v=' . $version;
        $controller->head['js'][] = $this->config['jsUrl'] . 'mgr/misc/default/panel.js?v=' . $version;
        $controller->head['js'][] = $this->config['jsUrl'] . 'mgr/misc/default/combo.js?v=' . $version;

        $controller->head['js'][] = $this->config['jsUrl'] . 'mgr/misc/ux.js?v=' . $version;
        $controller->head['js'][] = $this->config['jsUrl'] . 'mgr/misc/utils.js?v=' . $version;
        $controller->head['js'][] = $this->config['jsUrl'] . 'mgr/misc/renderer.js?v=' . $version;
        $controller->head['js'][] = $this->config['jsUrl'] . 'mgr/misc/combo.js?v=' . $version;

        $controller->head['js'][] = $this->config['jsUrl'] . 'mgr/widgets/subobjects/gridlocal.js?v=' . $version;
        $controller->head['js'][] = $this->config['jsUrl'] . 'mgr/widgets/files/gridlocal.js?v=' . $version;

        $controller->head['js'][] = $this->config['jsUrl'] . 'mgr/widgets/objects/grid.js?v=' . $version;
        $controller->head['js'][] = $this->config['jsUrl'] . 'mgr/widgets/objects/window.js?v=' . $version;

        $controller->head['js'][] = $this->config['jsUrl'] . 'mgr/pages/' . $page . '.panel.js?v=' . $version;
        $controller->head['js'][] = $this->config['jsUrl'] . 'mgr/pages/' . $page . '.js?v=' . $version;

        // Config
        $controller->addHtml('
            <script>
                modExtraLayout.config = ' . json_encode($this->config) . ';
                modExtraLayout.config[\'connector_url\'] = "' . $this->config['connectorUrl'] . '";
                Ext.onReady(function() {
                    MODx.load({
                        xtype: "modextralayout-page-' . $page . '",
                    });
                });
            </script>
        ');

        return true;
    }


    /**
     * @param array $config
     *
     * @return melTools
     */
    public function getTools(array $config = [])
    {
        if (!is_object($this->tools)) {
            if ($class = $this->modx->loadClass('tools.melTools', $this->config['handlersPath'], true, true)) {
                $this->tools = new $class($this, $config);
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

    /**
     * @return pdoFetch
     */
    public function getPdoFetch()
    {
        if (class_exists('pdoFetch') && !is_object($this->pdoFetch)) {
            $this->pdoFetch = $this->modx->getService('pdoFetch');
        }

        return $this->pdoFetch;
    }
}