<?php

class melObjectUpdateProcessor extends modObjectUpdateProcessor
{
    public $objectType = 'melObject';
    public $classKey = 'melObject';
    public $languageTopics = ['modextralayout:default'];
    public $permission = 'save';
    /**
     * @var modExtraLayout $mel
     */
    protected $mel;
    /**
     * @var modMediaSource $source
     */
    protected $source;


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
        // Get source
        if (!$source = (int)$this->getProperty('source')) {
            return $this->modx->lexicon('mel_err_ns');
        }
        if (!$this->source = $this->modx->getObject('sources.modMediaSource', ['id' => $source])) {
            return $this->modx->lexicon('mel_err_ns');
        }
        $this->source->initialize();

        // Check object id
        if (!$id = (int)$this->getProperty('id')) {
            return $this->modx->lexicon('mel_err_ns');
        }

        // Prepare properties
        if (($tmp = $this->prepareProperties()) !== true) {
            return $tmp;
        }
        unset($tmp);

        // Check on required
        $required = [
            // 'group',
            'parent',
            'name:mel_err_required_name',
            'files:mel_err_required_file',
            'subobjects:mel_err_required_subobject',
        ];
        $this->mel->tools->checkProcessorRequired($this, $required, 'mel_err_required');

        // Check on unique
        $unique = [
            'name:mel_err_unique_name',
        ];
        $this->mel->tools->checkProcessorUnique('', 0, $this, $unique, 'mel_err_unique');

        return parent::beforeSet();
    }

    /**
     * @return string|bool
     */
    public function prepareProperties()
    {
        //
        // Get raw properties
        $properties = $this->getProperties();
        // return print_r($properties, 1);

        //
        // Time of create and update of object
        unset($properties['createdon']);
        $this->unsetProperty('createdon');
        $properties['updatedon'] = time();

        //
        // Subobjects
        $properties['subobjects'] = @($this->mel->tools->isJSON($properties['subobjects'])
            ? $this->modx->fromJSON($properties['subobjects']) : $properties['subobjects']) ?: [];

        //
        // Files
        $properties['files'] = @($this->mel->tools->isJSON($properties['files'])
            ? $this->modx->fromJSON($properties['files']) : $properties['files']) ?: [];
        foreach ($properties['files'] as &$file) {
            $file = ['file' => @$file['file'] ?: $file['urlpath']];
            $file['filepath'] = $this->source->getBasePath($file['file']) . $file['file'];
            if (!file_exists($file['filepath'])) {
                $file = null;
            }
        }
        $properties['files'] = array_values(array_diff($properties['files'], [null]));
        unset($file);

        //
        // Set prepared properties
        $this->setProperties($properties);
        // return print_r($properties, 1);

        return true;
    }
}

return 'melObjectUpdateProcessor';