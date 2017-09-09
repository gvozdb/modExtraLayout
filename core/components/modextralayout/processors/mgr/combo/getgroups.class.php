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
        $filter = $this->getProperty('filter', false);
        $notempty = $this->getProperty('notempty', true);
        if ($filter) {
            $output = array(
                array(
                    'display' => '(Все)',
                    'value' => '',
                ),
            );
        } else {
            $output = array();
        }

        $rows = array(
            '',
            'Group 1',
            'Group 2',
            'Group 3',
            'Group 4',
        );
        foreach ($rows as $v) {
            $tmp = null;
            if (empty($v)) {
                if ($filter || !$notempty) {
                    $tmp = array(
                        'display' => '(Не указано)',
                        'value' => '_',
                    );
                }
            } else {
                $tmp = array(
                    'display' => $v,
                    'value' => preg_replace('/\s+/', '_', strtolower($v)),
                );
            }
            if (!empty($tmp)) {
                $output[] = $tmp;
            }
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