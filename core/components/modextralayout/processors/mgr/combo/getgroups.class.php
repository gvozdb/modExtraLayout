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
        $path = MODX_CORE_PATH . 'components/modextralayout/model/modextralayout/';
        $this->mel = $this->modx->getService('modextralayout', 'modExtraLayout', $path);
        $this->mel->initialize($this->modx->context->get('key'));

        return parent::initialize();
    }

    /**
     * @return string
     */
    public function process()
    {
        $output = array();
        $groups = array(
            'Group 1',
            'Group 2',
            'Group 3',
            'Group 4',
        );
        foreach ($groups as $group) {
            $output[] = array(
                'value' => preg_replace('/\s+/', '_', strtolower($group)),
                'display' => $group,
            );
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