<?php

class modExtraLayoutHomeManagerController extends modExtraManagerController
{
    /**
     * @var string $page
     */
    public $page = 'home';
    /**
     * @var modExtraLayout $mel
     */
    public $mel;

    /**
     *
     */
    public function initialize()
    {
        $this->mel = $this->modx->getService('modextralayout', 'modExtraLayout',
            $this->modx->getOption('mel_core_path', null, MODX_CORE_PATH . 'components/modextralayout/') . 'model/modextralayout/');

        parent::initialize();
    }

    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['modextralayout:default'];
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
        return $this->modx->lexicon('mel_title_' . $this->page);
    }

    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->mel->loadManagerScripts($this->page);
    }

    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->mel->config['templatesPath'] . $this->page . '.tpl';
    }
}