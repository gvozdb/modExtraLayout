<?php

class melObjectGetProcessor extends modObjectGetProcessor
{
    public $objectType = 'melObject';
    public $classKey = 'melObject';
    public $languageTopics = ['modextralayout:default'];
    public $permission = 'view';

    /**
     * @return mixed
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        return parent::process();
    }
}

return 'melObjectGetProcessor';