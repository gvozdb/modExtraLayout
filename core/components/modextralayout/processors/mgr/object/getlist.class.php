<?php

class melObjectGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'melObject';
    public $classKey = 'melObject';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $permission = 'list';

    /**
     * @return boolean|string
     */
    public function initialize()
    {
        $this->setProperty('sort', str_replace('_formatted', '', $this->getProperty('sort')));

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

        return true;
    }

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->leftJoin('modResource', 'modResource', 'modResource.id = melObject.parent');

        $c->select(array($this->modx->getSelectColumns('melObject', 'melObject')));
        $c->select(array('modResource.pagetitle as parent_formatted'));

        // Фильтр по свойствам основного объекта
        foreach (array('group') as $v) {
            if (${$v} = $this->getProperty($v)) {
                if (${$v} == '_') {
                    $c->where(array(
                        '(' . $this->classKey . '.' . $v . ' = "" OR ' . $this->classKey . '.' . $v . ' IS NULL)',
                    ));
                } else {
                    $c->where(array(
                        $this->classKey . '.' . $v => ${$v},
                    ));
                }
            }
        }

        // Поиск
        if ($query = trim($this->getProperty('query'))) {
            $c->where(array(
                $this->classKey . '.name:LIKE' => "%{$query}%",
                'OR:' . $this->classKey . '.description:LIKE' => "%{$query}%",
            ));
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
        $array = $object->toArray();

        // Кнопки
        $array['actions'] = array();
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-edit',
            'title' => $this->modx->lexicon('mel_button_update'),
            'action' => 'updateObject',
            'button' => true,
            'menu' => true,
        );
        if (!$array['active']) {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-toggle-on action-green',
                'title' => $this->modx->lexicon('mel_button_enable'),
                'multiple' => $this->modx->lexicon('mel_button_enable_multiple'),
                'action' => 'enableObject',
                'button' => true,
                'menu' => true,
            );
        } else {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-toggle-off action-red',
                'title' => $this->modx->lexicon('mel_button_disable'),
                'multiple' => $this->modx->lexicon('mel_button_disable_multiple'),
                'action' => 'disableObject',
                'button' => true,
                'menu' => true,
            );
        }
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('mel_button_remove'),
            'multiple' => $this->modx->lexicon('mel_button_remove_multiple'),
            'action' => 'removeObject',
            'button' => true,
            'menu' => true,
        );

        return $array;
    }
}

return 'melObjectGetListProcessor';