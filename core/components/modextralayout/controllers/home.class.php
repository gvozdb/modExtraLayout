<?php

class modExtraLayoutHomeManagerController extends modExtraManagerController
{
    /** @var modExtraLayout $mel */
    public $mel;

    /**
     *
     */
    public function initialize()
    {
        $path = MODX_CORE_PATH . 'components/modextralayout/model/modextralayout/';
        $this->mel = $this->modx->getService('modextralayout', 'modExtraLayout', $path);

        parent::initialize();
    }

    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('modextralayout:default');
    }

    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }

    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('modextralayout');
    }

    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->mel->config['cssUrl'] . 'mgr/main.css');
        $this->addCss($this->mel->config['cssUrl'] . 'mgr/bootstrap.buttons.css');

        $this->addJavascript($this->mel->config['jsUrl'] . 'mgr/modextralayout.js');

        $this->addJavascript($this->mel->config['jsUrl'] . 'mgr/misc/ux.js');
        $this->addJavascript($this->mel->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->mel->config['jsUrl'] . 'mgr/misc/combo.js');

        $this->addJavascript($this->mel->config['jsUrl'] . 'mgr/misc/default.grid.js');
        $this->addJavascript($this->mel->config['jsUrl'] . 'mgr/misc/default.window.js');

        $this->addJavascript($this->mel->config['jsUrl'] . 'mgr/widgets/objects.grid.js');
        $this->addJavascript($this->mel->config['jsUrl'] . 'mgr/widgets/objects.window.js');

        $this->addJavascript($this->mel->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->mel->config['jsUrl'] . 'mgr/sections/home.js');

        $this->addHtml('
            <script type="text/javascript">
                modExtraLayout.config = ' . json_encode($this->mel->config) . ';
                modExtraLayout.config[\'connector_url\'] = "' . $this->mel->config['connectorUrl'] . '";
                Ext.onReady(function() {
                    MODx.load({
                        xtype: "modextralayout-page-home",
                    });
                });
            </script>
        ');
    }

    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->mel->config['templatesPath'] . 'home.tpl';
    }
}