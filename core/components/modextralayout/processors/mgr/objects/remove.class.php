<?php

class melObjectRemoveProcessor extends modObjectProcessor
{
    public $objectType = 'melObject';
    public $classKey = 'melObject';
    public $languageTopics = ['modextralayout:default'];
    public $permission = 'remove';

    /**
     * @return array|string
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        if ($ids = $this->getProperty('id')) {
            $ids = [$ids];
        } else {
            $ids = $this->modx->fromJSON($this->getProperty('ids'));
            if (empty($ids)) {
                return $this->failure($this->modx->lexicon('mel_err_ns'));
            }
        }

        foreach ($ids as $id) {
            /** @var melObject $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('mel_err_nf'));
            }
            $object->remove();
        }

        return $this->success();
    }
}

return 'melObjectRemoveProcessor';