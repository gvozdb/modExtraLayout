<?php

abstract class melPlugin
{
    /**
     * @var int $priority
     */
    public $priority = 0;
    /**
     * @var modX $modx
     */
    protected $modx;
    /**
     * @var modExtraLayout $mel
     */
    protected $mel;
    /**
     * @var array $sp
     */
    protected $sp;

    /**
     * @param modExtraLayout $mel
     * @param array          $sp
     */
    public function __construct(modExtraLayout &$mel, array &$sp)
    {
        $this->mel = &$mel;
        $this->modx = &$this->mel->modx;
        $this->sp = &$sp;
        $this->mel->initialize($this->modx->context->key);
    }

    // abstract public function run();
}