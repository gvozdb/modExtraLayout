<?php

class melObjectCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'melObject';
    public $classKey = 'melObject';
    public $languageTopics = ['modextralayout:default'];
    public $permission = 'create';
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
        if (($tmp = $this->prepareProperties()) !== true) {
            return $tmp;
        }
        unset($tmp);

        // Проверяем на заполненность
        $required = [
            // 'group',
            'parent',
            'name:mel_err_required_name',
        ];
        $this->mel->tools->checkProcessorRequired($this, $required, 'mel_err_required');

        // Проверяем на уникальность
        $unique = [
            'name:mel_err_unique_name',
        ];
        $this->mel->tools->checkProcessorUnique('', 0, $this, $unique, 'mel_err_unique');

        return parent::beforeSet();
    }

    /**
     * @return string|bool
     */
    private function prepareProperties()
    {
        $properties = $this->getProperties();
        // return print_r($properties, 1);

        // Вычисляем позицию
        // $properties['idx'] = $this->modx->getCount($this->classKey, array(
        //     'object_id' => (int)$this->getProperty('object_id')
        // )); // Для группировки по родителю
        $properties['idx'] = $this->modx->getCount($this->classKey, ['id:!=' => 0]);
        ++$properties['idx'];

        // // Files
        // $properties['files'] = @($this->mel->tools->isJSON($properties['files'])
        //     ? $this->modx->fromJSON($properties['files']) : $properties['files']) ?: [];
        // foreach ($properties['files'] as &$file) {
        //     $file = $file['urlpath'];
        // }
        // unset($file);

        // Создано
        $properties['createdon'] = $properties['createdon'] ?: time();

        $this->setProperties($properties);

        // return print_r($properties, 1);

        return true;
    }
}

return 'melObjectCreateProcessor';