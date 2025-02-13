<?php

class melObjectGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'melObject';
    public $classKey = 'melObject';
    public $defaultSortField = 'idx';
    public $defaultSortDirection = 'DESC';
    public $permission = 'list';

    /**
     * @return boolean|string
     */
    public function initialize()
    {
        return parent::initialize();
    }

    /**
     * @return boolean|string
     */
    public function beforeQuery()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        $this->setProperty('sort', str_replace('_formatted', '', $this->getProperty('sort')));

        return parent::beforeQuery();
    }

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->leftJoin('modResource', 'modResource', 'modResource.id = melObject.parent');

        $c->select([$this->modx->getSelectColumns('melObject', 'melObject')]);
        $c->select(['modResource.pagetitle as parent_formatted']);

        // Фильтр по свойствам основного объекта
        foreach (['group'] as $v) {
            if (${$v} = $this->getProperty($v)) {
                if (${$v} == '_') {
                    $c->where([
                        '(' . $this->classKey . '.' . $v . ' = "" OR ' . $this->classKey . '.' . $v . ' IS NULL)',
                    ]);
                } else {
                    $c->where([
                        $this->classKey . '.' . $v => ${$v},
                    ]);
                }
            }
        }

        // Поиск
        if ($query = trim($this->getProperty('query'))) {
            $c->where([
                $this->classKey . '.name:LIKE' => "%{$query}%",
                'OR:' . $this->classKey . '.description:LIKE' => "%{$query}%",
            ]);
        }

        return $c;
    }

    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function prepareRow(xPDOObject $object)
    {
        $data = $object->toArray();

        // Get menu and buttons
        $data['actions_download'] = $this->getButtons($data, ['download']);
        $data['actions_active'] = $this->getButtons($data, ['active']);
        $data['actions_other'] = $this->getButtons($data, ['full'], ['active', 'download']);
        $data['actions'] = $this->getButtons($data);

        return $data;
    }
    
    /**
     * @param array $data
     * @param array $include
     * @param array $exclude
     *
     * @return array
     */
    public function getButtons(array $data, array $include = ['full'], $exclude = [])
    {
        $buttons = [];

        // $buttons[] = [
        //     'list' => ['full', 'download'],
        //     'cls' => '',
        //     'icon' => 'icon icon-download action-darkblue',
        //     'title' => $this->modx->lexicon('mel_button_download'),
        //     'action' => 'downloadObject',
        //     'button' => true,
        //     'menu' => true,
        // ];
        // $buttons[] = [
        //     'list' => ['full', 'download'],
        //     'content' => '-',
        // ];

        $buttons[] = [
            'list' => ['full', 'update'],
            'cls' => '',
            'icon' => 'icon icon-edit',
            'title' => $this->modx->lexicon('mel_button_update'),
            'action' => 'updateObject',
            'button' => true,
            'menu' => true,
        ];

        if (empty($data['active'])) {
            $buttons[] = [
                'list' => ['full', 'active'],
                'cls' => '',
                'icon' => 'icon icon-toggle-off action-red',
                'title' => $this->modx->lexicon('mel_button_enable'),
                'multiple' => $this->modx->lexicon('mel_button_enable_multiple'),
                'action' => 'enableObject',
                'button' => true,
                'menu' => true,
            ];
        } else {
            $buttons[] = [
                'list' => ['full', 'active'],
                'cls' => '',
                'icon' => 'icon icon-toggle-on action-green',
                'title' => $this->modx->lexicon('mel_button_disable'),
                'multiple' => $this->modx->lexicon('mel_button_disable_multiple'),
                'action' => 'disableObject',
                'button' => true,
                'menu' => true,
            ];
        }

        $buttons[] = [
            'list' => ['full', 'remove'],
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('mel_button_remove'),
            'multiple' => $this->modx->lexicon('mel_button_remove_multiple'),
            'action' => 'removeObject',
            'button' => true,
            'menu' => true,
        ];

        $buttons = array_map(
            function ($button) {
                if (!empty($button['content'])) {
                    $button = $button['content'];
                }
                return $button;
            },
            array_filter(
                $buttons,
                function ($button) use ($include, $exclude) {
                    return !empty(array_intersect($include, $button['list'])) && empty(array_intersect($exclude, $button['list']));
                }
            )
        );

        return $buttons;
    }
}

return 'melObjectGetListProcessor';