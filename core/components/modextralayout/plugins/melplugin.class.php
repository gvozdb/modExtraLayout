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
        $this->mel = $this->modx->getService('modextralayout', 'modExtraLayout',
            $this->modx->getOption('mel_core_path', null, MODX_CORE_PATH . 'components/modextralayout/') . 'model/modextralayout/', $this->sp);
        $this->mel->initialize($this->modx->context->key);
    }

    abstract public function run();
}