<?php

class melComboResourceGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modResource';
    public $languageTopics = ['resource'];
    public $defaultSortField = 'menuindex';
    public $defaultSortDirection = 'ASC';

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->select('id,pagetitle');

        if ($context_key = $this->getProperty('context_key')) {
            $c->where(['context_key' => $context_key]);
        }
        if ($isfolder = (int)$this->getProperty('isfolder')) {
            $c->where(['isfolder' => $isfolder]);
        }
        if ($query = trim($this->getProperty('query'))) {
            $c->where([
                'id:LIKE' => "{$query}%",
                'OR:pagetitle:LIKE' => "%{$query}%",
                'OR:longtitle:LIKE' => "%{$query}%",
            ]);
        }

        return $c;
    }

    /**
     * @param xPDOObject $obj
     *
     * @return array
     */
    public function prepareRow(xPDOObject $obj)
    {
        $array = $obj->toArray();

        if ($this->getProperty('parents')) {
            $array['parents'] = [];
            $parents = $this->modx->getParentIds($array['id'], 4, ['context' => $array['context_key']]);
            if ($parents[count($parents) - 1] == 0) {
                unset($parents[count($parents) - 1]);
            }
            if (!empty($parents) && is_array($parents)) {
                $q = $this->modx->newQuery($this->classKey, ['id:IN' => $parents]);
                $q->select('id,pagetitle');
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        $key = array_search($row['id'], $parents);
                        if ($key !== false) {
                            $parents[$key] = $row;
                        }
                    }
                }
                $array['parents'] = array_reverse($parents);
            }
        }

        return $array;
    }
}

return 'melComboResourceGetListProcessor';