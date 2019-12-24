<?php

/**
 *
 */
class melOnMODXInit extends melPlugin
{
    public function run()
    {
        // Чистим истёкшие регистры
        $this->modx->getService('registry', 'registry.modRegistry');
        $this->modx->removeCollection('registry.db.modDbRegisterMessage', ['expires:>' => 1, 'expires:<' => time()]);

        //
        $this->map([
            'modResource' => [
                'composites' => [
                    'melObjects' => [
                        'class' => 'melObject',
                        'local' => 'id',
                        'foreign' => 'parent',
                        'cardinality' => 'many',
                        'owner' => 'local',
                        'criteria' => [
                            'foreign' => [
                                'class' => 'modResource',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Расширяет нативный MAP массив
     *
     * @param array $map
     *
     * @return bool
     */
    public function map(array $map = [])
    {
        foreach ($map as $class => $data) {
            $this->modx->loadClass($class);

            foreach ($data as $tmp => $fields) {
                if ($tmp == 'fields') {
                    foreach ($fields as $field => $value) {
                        foreach (['fields', 'fieldMeta', 'indexes'] as $key) {
                            if (isset($data[$key][$field])) {
                                $this->modx->map[$class][$key][$field] = $data[$key][$field];
                            }
                        }
                    }
                } elseif ($tmp == 'composites' || $tmp == 'aggregates') {
                    foreach ($fields as $alias => $relation) {
                        if (!isset($this->modx->map[$class][$tmp][$alias])) {
                            $this->modx->map[$class][$tmp][$alias] = $relation;
                        }
                    }
                }
            }
        }

        return true;
    }
}