<?php

abstract class melPlugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var modExtraLayout $mel */
    protected $mel;
    /** @var array $sp */
    protected $sp;

    public function __construct(&$modx, &$sp)
    {
        $this->sp = &$sp;
        $this->modx = &$modx;
        $this->mel = $this->modx->modextralayout;

        if (!is_object($this->mel)) {
            $path = MODX_CORE_PATH . 'components/modextralayout/model/modextralayout/';
            $this->mel = $this->modx->getService('modextralayout', 'modextralayout', $path, $this->sp);
        }
        if (!$this->mel->initialized[$this->modx->context->key]) {
            $this->mel->initialize($this->modx->context->key);
        }
    }

    abstract public function run();
}