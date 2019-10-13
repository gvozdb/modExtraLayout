<?php
/** @var modX $modx */
/** @var modExtraLayout $mel */
/** @var array $scriptProperties */
if (!$mel = $modx->getService('modextralayout', 'modExtraLayout',
    $modx->getOption('mel_core_path', null, MODX_CORE_PATH . 'components/modextralayout/') . 'model/modextralayout/')) {
    return;
}

//
$exists_group = false;
$exists_single = false;

//
$className = 'mel' . $modx->event->name;
$modx->loadClass('melPlugin', $mel->config['pluginsPath'], true, true);
$modx->loadClass('melPluginGroup', $mel->config['pluginsPath'], true, true);
$modx->loadClass($className, $mel->config['pluginsPath'], true, true);

//
if (class_exists('melPluginGroup')) {
    /** @var melPluginGroup $handlerGroup */
    $handlerGroup = new melPluginGroup($mel, $scriptProperties);
    if ($exists_group = method_exists($handlerGroup, $modx->event->name)) {
        $handlerGroup->{$modx->event->name}();
    }
    unset($handlerGroup);
}

//
if ($exists_single = class_exists($className)) {
    /** @var melPlugin $handlerSingle */
    $handlerSingle = new $className($mel, $scriptProperties);
    $handlerSingle->run();
    unset($handlerSingle);
}

// Удаляем событие у плагина, если такого класса не существует
if ($exists_group === false && $exists_single === false) {
    if ($event = $modx->getObject('modPluginEvent', array(
        'pluginid' => $modx->event->plugin->get('id'),
        'event' => $modx->event->name,
    ))) {
        $event->remove();
    }
}
return;