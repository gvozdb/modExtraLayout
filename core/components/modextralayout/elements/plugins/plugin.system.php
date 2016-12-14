<?php
/** @var modX $modx */
/** @var modExtraLayout $mel */

$path = MODX_CORE_PATH . 'components/modextralayout/model/modextralayout/';
if (!is_object($modx->modextralayout)) {
    $mel = $modx->getService('modextralayout', 'modextralayout', $path);
} else {
    $mel = $modx->modextralayout;
}
$className = 'mel' . $modx->event->name;
$modx->loadClass('melPlugin', $mel->config['pluginsPath'], true, true);
$modx->loadClass($className, $mel->config['pluginsPath'], true, true);
if (class_exists($className)) {
    $handler = new $className($modx, $scriptProperties);
    $handler->run();
}
return;