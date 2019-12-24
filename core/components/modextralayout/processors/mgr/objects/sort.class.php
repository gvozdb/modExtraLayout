<?php

class melObjectSortProcessor extends modObjectProcessor
{
    public $classKey = 'melObject';
    private $_object;
    private $_object_key = ''; // Для группировки по родителю указать ключ: 'object_id';

    /**
     * @return array|string
     */
    public function process()
    {
        /** @var melObject $target */
        if (!$target = $this->modx->getObject($this->classKey, $this->getProperty('target'))) {
            return $this->failure();
        }
        $this->_object = empty($this->_object_key) ? 0 : $target->get($this->_object_key);

        $sources = json_decode($this->getProperty('sources'), true);
        if (!is_array($sources)) {
            return $this->failure();
        }
        foreach ($sources as $id) {
            /** @var melObject $source */
            if ($source = $this->modx->getObject($this->classKey, $id)) {
                if (empty($this->_object_key) || $source->get($this->_object_key) == $this->_object) {
                    $target = $this->modx->getObject($this->classKey, $this->getProperty('target'));
                    $this->sort($source, $target);
                } else {
                    $this->move($source);
                }
            }
        }
        $this->updateIndex();

        return $this->modx->error->success();
    }

    /**
     * @param melObject $source
     * @param melObject $target
     */
    public function sort(melObject $source, melObject $target)
    {
        $c = $this->modx->newQuery($this->classKey);
        $c->command('UPDATE');
        if (!empty($this->_object_key)) {
            $c->where([
                $this->_object_key => $this->_object,
            ]);
        }
        if ($source->get('idx') < $target->get('idx')) {
            $c->query['set']['idx'] = [
                'value' => '`idx` - 1',
                'type' => false,
            ];
            $c->andCondition([
                'idx:<=' => $target->idx,
                'idx:>' => $source->idx,
            ]);
            $c->andCondition([
                'idx:>' => 0,
            ]);
        } else {
            $c->query['set']['idx'] = [
                'value' => '`idx` + 1',
                'type' => false,
            ];
            $c->andCondition([
                'idx:>=' => $target->idx,
                'idx:<' => $source->idx,
            ]);
        }
        $c->prepare();
        $c->stmt->execute();
        $source->set('idx', $target->get('idx'));
        $source->save();
    }

    /**
     * @param melObject $source
     */
    public function move(melObject $source)
    {
        if (!empty($this->_object_key)) {
            $source->set($this->_object_key, $this->_object);
            $source->set('idx', $this->modx->getCount($this->classKey, [$this->_object_key => $this->_object]));
            $source->save();
        }
    }

    /**
     *
     */
    public function updateIndex()
    {
        // Update indexes
        $condition = empty($this->_object_key) ? ['id:!=' => 0] : [$this->_object_key => $this->_object];
        $c = $this->modx->newQuery($this->classKey, $condition);
        $c->select('id');
        $c->sortby('idx', 'ASC');
        $c->sortby('id', 'ASC');
        if ($c->prepare() && $c->stmt->execute()) {
            $table = $this->modx->getTableName($this->classKey);
            $update = $this->modx->prepare("UPDATE {$table} SET idx = ? WHERE id = ?");
            while ($id = $c->stmt->fetch(PDO::FETCH_COLUMN)) {
                $i = empty($i) ? 1 : ++$i;
                $update->execute([$i, $id]);
            }
        }
    }
}

return 'melObjectSortProcessor';