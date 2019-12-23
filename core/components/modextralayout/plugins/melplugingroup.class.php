<?php

/**
 *
 */
class melPluginGroup extends melPlugin
{
    /**
     * @var int $priority
     */
    public $priority = 0;

    /**
     * @param modExtraLayout $mel
     * @param array          $sp
     */
    public function __construct(modExtraLayout &$mel, array &$sp)
    {
        parent::__construct($mel, $sp);
    }

    /**
     *
     */
    public function onMODXInit()
    {
        //
    }
}