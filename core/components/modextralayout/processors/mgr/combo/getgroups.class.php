<?php

class melComboGroupGetListProcessor extends modProcessor
{
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
     * @return string
     */
    public function process()
    {
        $output = [];

        //
        $filter = $this->getProperty('filter', false);
        if (!empty($filter)) {
            $output[] = [
                'display' => '(Все)',
                'value' => '',
            ];
        }

        //
        $notempty = $this->getProperty('notempty', true);
        if (!empty($filter) || empty($notempty)) {
            $output[] = [
                'display' => '(Не указано)',
                'value' => '_',
            ];
        }

        //
        if (empty($filter)) {
            $query = $this->getProperty('query', '');
            if (!empty($query)) {
                $output[] = [
                    'display' => $query,
                    'value' => $query,
                ];
            }
        }

        //
        $rows = [
            'group_1',
            'group_2',
            'group_3',
            'group_4',
        ];
        foreach ($rows as $v) {
            $output[] = [
                'display' => $this->modx->lexicon('mel_group_' . $v),
                'value' => strtolower($v),
            ];
        }

        return $this->outputArray($output);
    }

    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('modextralayout:default');
    }
}

return 'melComboGroupGetListProcessor';