<?php

class melObjectUpdateProcessor extends modObjectUpdateProcessor
{
    public $objectType = 'melObject';
    public $classKey = 'melObject';
    public $languageTopics = array('modextralayout:default');
    public $permission = 'save';
    /** @var modExtraLayout $mel */
    protected $mel;

    /**
     * @return bool
     */
    public function initialize()
    {
        $this->mel = $this->modx->getService('modextralayout', 'modExtraLayout',
            $this->modx->getOption('mel_core_path', null, MODX_CORE_PATH . 'components/modextralayout/') . 'model/modextralayout/');
        $this->mel->initialize($this->modx->context->key);

        return parent::initialize();
    }

    /**
     * @return bool|string
     */
    public function beforeSave()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::beforeSave();
    }

    /**
     * @return bool
     */
    public function beforeSet()
    {
        if (!$id = (int)$this->getProperty('id')) {
            return $this->modx->lexicon('mel_err_ns');
        }
        if (($tmp = $this->prepareProperties()) !== true) {
            return $tmp;
        }
        unset($tmp);

        // Проверяем на заполненность
        $required = array(
            // 'group',
            'parent',
            'name:mel_err_required_name',
        );
        $this->mel->tools->checkProcessorRequired($this, $required, 'mel_err_required');

        // Проверяем на уникальность
        $unique = array(
            'name:mel_err_unique_name',
        );
        $this->mel->tools->checkProcessorUnique('', 0, $this, $unique, 'mel_err_unique');

        return parent::beforeSet();
    }

    /**
     * @return string|bool
     */
    public function prepareProperties()
    {
        $properties = $this->getProperties();
        // return print_r($properties, 1);

        // Создано
        $properties['createdon'] = $properties['createdon'] ?: time();

        $this->setProperties($properties);

        // return print_r($properties, 1);

        return true;
    }
}

return 'melObjectUpdateProcessor';