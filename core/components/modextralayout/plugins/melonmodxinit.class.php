<?php

/**
 * Расширяем нативный MAP массив
 */
class melOnMODXInit extends melPlugin
{
    public function run()
    {
        $map = array(
            'modResource' => array(
                'composites' => array(
                    'melObjects' => array(
                        'class' => 'melObject',
                        'local' => 'id',
                        'foreign' => 'parent',
                        'cardinality' => 'many',
                        'owner' => 'local',
                        'criteria' => array(
                            'foreign' => array(
                                'class' => 'modResource',
                            ),
                        ),
                    ),
                ),
            ),
        );
        $this->mel->tools->systemMapExtends($map);
    }
}