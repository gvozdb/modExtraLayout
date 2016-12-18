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

        // Фильтр
        if ($group = $this->getProperty('group')) {
            $c->where(array(
                'melObject.group' => $group,
            ));
        }

        // Поиск
        if ($query = trim($this->getProperty('query'))) {
            $c->where(array(
                'melObject.name:LIKE' => "%{$query}%",
                'OR:melObject.description:LIKE' => "%{$query}%",
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