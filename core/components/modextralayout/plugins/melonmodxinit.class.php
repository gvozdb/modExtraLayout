<?php

/**
 * Расширяем нативный MAP массив
 */
class melOnMODXInit extends melPlugin
{
    public function run()
    {
        $this->mel->tools->systemMapExtens(array(
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
        ));
    }
}